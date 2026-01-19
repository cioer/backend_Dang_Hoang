<?php
require_once __DIR__ . '/../bootstrap.php';

use App\Core\{Middleware, Response, Bootstrap};

Middleware::cors('GET');

$user = Middleware::auth();
$db = Bootstrap::db();

// Lấy tham số lọc theo thời gian (nếu có)
$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : null;
$end_date = isset($_GET['end_date']) ? $_GET['end_date'] : null;

try {
    $whereClause = "";
    $params = [];

    // Statistics by Class (Total points deducted)
    // Use LEFT JOIN starting from classes to include classes with 0 violations
    $dateCondition = "";
    if ($start_date && $end_date) {
        // Important: Date condition must be in the ON clause for LEFT JOIN, 
        // otherwise it filters out classes with no violations (NULL dates)
        $dateCondition = " AND (v.created_at BETWEEN :start_date AND :end_date) ";
        $params[':start_date'] = $start_date . " 00:00:00";
        $params[':end_date'] = $end_date . " 23:59:59";
    }

    $query = "SELECT 
                c.name as class_name, 
                COALESCE(SUM(cr.points), 0) as total_deducted, 
                COUNT(v.id) as violation_count
              FROM classes c
              LEFT JOIN student_details sd ON c.id = sd.class_id
              LEFT JOIN violations v ON sd.user_id = v.student_id $dateCondition
              LEFT JOIN conduct_rules cr ON v.rule_id = cr.id
              GROUP BY c.id, c.name
              ORDER BY total_deducted ASC, c.name ASC";

    $stmt = $db->prepare($query);
    $stmt->execute($params);
    $class_stats = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Statistics by Rule (Most common violations) - Keep INNER JOIN here as we only care about actual violations
    $whereClause = "";
    if ($start_date && $end_date) {
        $whereClause = " WHERE v.created_at BETWEEN :start_date AND :end_date ";
    }
    
    $query2 = "SELECT cr.rule_name, COUNT(v.id) as count
               FROM violations v
               JOIN conduct_rules cr ON v.rule_id = cr.id
               $whereClause
               GROUP BY cr.rule_name
               ORDER BY count DESC";

    $stmt2 = $db->prepare($query2);
    $stmt2->execute($params);
    $rule_stats = $stmt2->fetchAll(PDO::FETCH_ASSOC);

    Response::success([
        "class_rankings" => $class_stats,
        "common_violations" => $rule_stats
    ]);
} catch (PDOException $e) {
    error_log("Competition stats query error: " . $e->getMessage());
    Response::error('Error fetching competition statistics', 500);
}
