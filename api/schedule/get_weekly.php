<?php
require_once __DIR__ . '/../bootstrap.php';

use App\Core\{Middleware, Response, Request, Bootstrap};

Middleware::cors('POST');

$user = Middleware::auth();
$db = Bootstrap::db();
$data = Request::all();

$user_id = $user->id;
$role = $user->role;

$query = "";
if ($role == 'student') {
    $query = "SELECT s.day_of_week, s.period, sub.name as subject_name, u.full_name as teacher_name
              FROM schedule s
              JOIN subjects sub ON s.subject_id = sub.id
              JOIN users u ON s.teacher_id = u.id
              JOIN student_details sd ON s.class_id = sd.class_id
              WHERE sd.user_id = :user_id
              ORDER BY s.day_of_week, s.period";
} else if ($role == 'teacher') {
    $query = "SELECT s.day_of_week, s.period, sub.name as subject_name, c.name as class_name
              FROM schedule s
              JOIN subjects sub ON s.subject_id = sub.id
              JOIN classes c ON s.class_id = c.id
              WHERE s.teacher_id = :user_id
              ORDER BY s.day_of_week, s.period";
} else if ($role == 'parent') {
    // For parent, get schedule of the first child (simplified)
    $query = "SELECT s.day_of_week, s.period, sub.name as subject_name, u.full_name as teacher_name
              FROM schedule s
              JOIN subjects sub ON s.subject_id = sub.id
              JOIN users u ON s.teacher_id = u.id
              JOIN student_details sd ON s.class_id = sd.class_id
              JOIN parent_student_links psl ON sd.user_id = psl.student_id
              WHERE psl.parent_id = :user_id
              ORDER BY s.day_of_week, s.period";
} else {
    Response::forbidden('Khong co quyen truy cap.');
}

$stmt = $db->prepare($query);
$stmt->bindParam(":user_id", $user_id);
$stmt->execute();

$schedule_list = $stmt->fetchAll(PDO::FETCH_ASSOC);

Response::success($schedule_list);
