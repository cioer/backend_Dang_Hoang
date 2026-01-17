<?php
require_once __DIR__ . '/../../bootstrap.php';

use App\Core\{Middleware, Response, Request, Bootstrap};

Middleware::cors('POST');
Middleware::requireAdmin();

$db = Bootstrap::db();
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$data = Request::all();
$type = $data['type'] ?? 'day';
$label = $data['label'] ?? '';

if (!in_array($type, ['day', 'week', 'month'], true)) {
    Response::error("Invalid type", 400);
}

if (empty($label)) {
    Response::success([]);
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
        $where = "YEAR(v.created_at) = :y AND WEEK(v.created_at) = :w";
        $params[':y'] = (int)$parts[0];
        $params[':w'] = (int)$parts[1];
    } else {
        Response::success([]);
    }
} elseif ($type == 'month') {
    // Label format: YYYY/MM
    $parts = explode('/', $label);
    if (count($parts) == 2) {
        $where = "YEAR(v.created_at) = :y AND MONTH(v.created_at) = :m";
        $params[':y'] = (int)$parts[0];
        $params[':m'] = (int)$parts[1];
    } else {
        Response::success([]);
    }
}

if (empty($where)) {
    Response::success([]);
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

try {
    $stmt = $db->prepare($query);
    $stmt->execute($params);
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    Response::success($result);
} catch (PDOException $e) {
    Response::serverError("Database error: " . $e->getMessage());
}
