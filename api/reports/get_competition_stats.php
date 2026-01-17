<?php
include_once '../../config/database.php';
include_once '../../config/jwt.php';

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$headers = getallheaders();
$token = isset($headers['Authorization']) ? str_replace('Bearer ', '', $headers['Authorization']) : null;

if (!$token || !validateJWT($token)) {
    http_response_code(401);
    echo json_encode(["message" => "Unauthorized access."]);
    exit;
}

$database = new Database();
$db = $database->getConnection();

if (!$db) {
    http_response_code(500);
    echo json_encode(["message" => "Database connection failed"]);
    exit;
}

try {
    // Statistics by Class (Total points deducted)
    $query = "SELECT c.name as class_name, SUM(cr.points) as total_deducted, COUNT(v.id) as violation_count
              FROM violations v
              JOIN student_details sd ON sd.user_id = v.student_id
              JOIN classes c ON c.id = sd.class_id
              JOIN conduct_rules cr ON v.rule_id = cr.id
              GROUP BY c.name
              ORDER BY total_deducted ASC"; // Less deducted is better

    $stmt = $db->prepare($query);
    $stmt->execute();
    $class_stats = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Statistics by Rule (Most common violations)
    $query2 = "SELECT cr.rule_name, COUNT(v.id) as count
               FROM violations v
               JOIN conduct_rules cr ON v.rule_id = cr.id
               GROUP BY cr.rule_name
               ORDER BY count DESC";

    $stmt2 = $db->prepare($query2);
    $stmt2->execute();
    $rule_stats = $stmt2->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        "class_rankings" => $class_stats,
        "common_violations" => $rule_stats
    ]);
} catch (PDOException $e) {
    error_log("Competition stats query error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(["message" => "Error fetching competition statistics"]);
    exit;
}
?>