<?php
require_once __DIR__ . '/../bootstrap.php';

use App\Core\{Middleware, Response, Bootstrap};

Middleware::cors('GET');
Middleware::requireAdmin();

$db = Bootstrap::db();

$tables = ['conduct_rules', 'violations', 'discipline_points', 'student_details', 'classes', 'subjects', 'system_settings'];
$backup = [];

foreach ($tables as $t) {
    $stmt = $db->query("SELECT * FROM " . $t);
    $backup[$t] = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

Response::success($backup);
