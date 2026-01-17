<?php
include_once '../../../config/database.php';
include_once '../../../config/jwt.php';

// Suppress errors to avoid polluting JSON output
error_reporting(0);
ini_set('display_errors', 0);

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

try {
    $headers = getallheaders();
    $auth = $headers['Authorization'] ?? $headers['authorization'] ?? null;
    if (!$auth || stripos($auth, 'Bearer ') !== 0) {
        throw new Exception("Unauthorized", 401);
    }
    $token = substr($auth, 7);
    $decoded = validateJwt($token);
    if (!$decoded) {
        throw new Exception("Invalid token", 401);
    }
    $role = $decoded['data']->role ?? null;
    if ($role !== 'admin') {
        throw new Exception("Forbidden", 403);
    }

    $database = new Database();
    $db = $database->getConnection();
    // Ensure we get exceptions for SQL errors
    if (!$db) {
        throw new Exception("Database connection failed", 500);
    }
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $input = file_get_contents("php://input");
    $data = json_decode($input);
    
    if (json_last_error() !== JSON_ERROR_NONE) {
        $type = $_POST['type'] ?? 'day';
        $label = $_POST['label'] ?? '';
    } else {
        $type = isset($data->type) ? $data->type : 'day';
        $label = isset($data->label) ? $data->label : '';
    }

    if (!in_array($type, ['day','week','month'], true)) {
        throw new Exception("Invalid type", 400);
    }

    if (empty($label)) {
        echo json_encode([]);
        exit;
    }

    $where = "";
    $params = [];

    if ($type == 'day') {
        // Label format: YYYY-MM-DD
        $where = "DATE(v.created_at) = :d";
        $params[':d'] = $label;
    } elseif ($type == 'week') {
        // Label format: YYYY/WW
        $parts = explode('/', $label);
        if (count($parts) == 2) {
            // Use WEEK() without mode to match violations.php list
            // NOTE: Yearweek mode might be safer but WEEK() is default.
            // Also need to handle year crossing if needed, but simple year/week check is usually okay.
            $where = "YEAR(v.created_at) = :y AND WEEK(v.created_at) = :w";
            $params[':y'] = (int)$parts[0];
            $params[':w'] = (int)$parts[1]; 
        } else {
             // If label is just a number (week number) or different format, try to parse
             // But for now, let's assume YYYY/WW
             echo json_encode([]); exit;
        }
    } elseif ($type == 'month') {
        // Label format: YYYY/MM
        $parts = explode('/', $label);
        if (count($parts) == 2) {
            $where = "YEAR(v.created_at) = :y AND MONTH(v.created_at) = :m";
            $params[':y'] = (int)$parts[0];
            $params[':m'] = (int)$parts[1];
        } else {
             echo json_encode([]); exit;
        }
    }

    if (empty($where)) {
        echo json_encode([]);
        exit;
    }

    $query = "
        SELECT 
            v.id,
            u.full_name as student_name,
            u.username as student_code,
            COALESCE(c.name, cls_details.name, 'N/A') as class_name,
            cr.rule_name as rule_name,
            cr.points as rule_points,
            v.note,
            v.created_at
        FROM violations v
        JOIN users u ON v.student_id = u.id
        LEFT JOIN class_registrations creg ON creg.student_id = u.id
        LEFT JOIN classes c ON creg.class_id = c.id
        LEFT JOIN student_details sd ON sd.user_id = u.id
        LEFT JOIN classes cls_details ON sd.class_id = cls_details.id
        JOIN conduct_rules cr ON v.rule_id = cr.id
        WHERE $where
        ORDER BY v.created_at DESC
    ";

    $stmt = $db->prepare($query);
    if (!$stmt) {
        throw new Exception("Database prepare error: " . implode(" ", $db->errorInfo()));
    }
    
    try {
        $stmt->execute($params);
    } catch (PDOException $pdoEx) {
        throw new Exception("Database execute error: " . $pdoEx->getMessage());
    }
    
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($result);

} catch (Exception $e) {
    http_response_code($e->getCode() && is_int($e->getCode()) && $e->getCode() >= 100 && $e->getCode() <= 599 ? $e->getCode() : 500);
    echo json_encode(["message" => $e->getMessage()]);
}
?>
