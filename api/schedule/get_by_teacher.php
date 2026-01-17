<?php
require_once __DIR__ . '/../bootstrap.php';

use App\Core\{Middleware, Response, Bootstrap};

Middleware::cors('GET');

$user = Middleware::auth();
$teacherId = $user->id;

$db = Bootstrap::db();

$stmt = $db->prepare("SELECT s.id, c.name AS class, sub.name AS subject, s.day_of_week, s.period, s.start_time, s.end_time
FROM schedule s
JOIN classes c ON c.id=s.class_id
JOIN subjects sub ON sub.id=s.subject_id
WHERE s.teacher_id=:tid
ORDER BY s.day_of_week, s.period");
$stmt->execute([":tid" => $teacherId]);

Response::success($stmt->fetchAll(PDO::FETCH_ASSOC));
