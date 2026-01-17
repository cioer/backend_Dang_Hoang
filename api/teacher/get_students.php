<?php
require_once __DIR__ . '/../bootstrap.php';

use App\Core\{Middleware, Response, Request, Bootstrap};

Middleware::cors('POST');
$user = Middleware::requireRole('teacher', 'admin');
$db = Bootstrap::db();

$class_id = Request::getInt('class_id', 0);

if ($class_id == 0) {
    Response::error('Thiếu class_id.', 400);
}

// Get students in the class
$query = "SELECT u.id, u.full_name, u.username AS code, u.avatar
          FROM class_registrations cr
          JOIN users u ON cr.student_id = u.id
          WHERE cr.class_id = :class_id
          ORDER BY u.full_name";

$stmt = $db->prepare($query);
$stmt->bindParam(":class_id", $class_id);
$stmt->execute();

$students = $stmt->fetchAll(PDO::FETCH_ASSOC);

Response::success($students);
