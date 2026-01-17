<?php
require_once __DIR__ . '/../bootstrap.php';

use App\Core\{Middleware, Response, Bootstrap};

Middleware::cors('GET');
Middleware::auth();

$db = Bootstrap::db();

try {
    $classStats = $db->query(
        "SELECT c.name AS class, COUNT(v.id) AS violations
         FROM violations v
         JOIN student_details sd ON sd.user_id = v.student_id
         JOIN classes c ON c.id = sd.class_id
         GROUP BY c.name ORDER BY c.name"
    )->fetchAll(PDO::FETCH_ASSOC);

    $monthlyStats = $db->query(
        "SELECT DATE_FORMAT(v.created_at,'%Y-%m') AS month, COUNT(*) AS violations
         FROM violations v
         GROUP BY DATE_FORMAT(v.created_at,'%Y-%m')
         ORDER BY month DESC"
    )->fetchAll(PDO::FETCH_ASSOC);

    Response::success(["by_class" => $classStats, "by_month" => $monthlyStats]);
} catch (PDOException $e) {
    error_log("Stats query error: " . $e->getMessage());
    Response::error('Error fetching statistics', 500);
}
