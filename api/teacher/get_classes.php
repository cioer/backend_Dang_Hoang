<?php
require_once __DIR__ . '/../bootstrap.php';

use App\Core\{Middleware, Response, Bootstrap};

Middleware::cors('POST');
$user = Middleware::auth();
$db = Bootstrap::db();

$user_role = $user->role;
$user_id = $user->id;

// Check if student is red star
$is_red_star = false;
if ($user_role == 'red_star') {
    $is_red_star = true;
} else if ($user_role == 'student') {
    $stmt = $db->prepare("SELECT COUNT(*) FROM red_committee_members WHERE user_id = :uid AND active = 1");
    $stmt->execute([':uid' => $user_id]);
    if ($stmt->fetchColumn() > 0) {
        $is_red_star = true;
    }
}

if ($user_role != 'teacher' && $user_role != 'admin' && !$is_red_star) {
    Response::unauthorized('Truy cap bi tu choi.');
}

if ($user_role == 'admin' || $is_red_star) {
    // Admin and Red Star sees all classes
    $query = "SELECT id, name FROM classes ORDER BY name";
    $stmt = $db->prepare($query);
} else {
    // Teacher sees assigned classes
    $teacher_id = $user_id;
    $query = "SELECT DISTINCT c.id, c.name
              FROM classes c
              LEFT JOIN schedule s ON c.id = s.class_id
              LEFT JOIN class_teacher_assignments cta ON c.id = cta.class_id
              WHERE s.teacher_id = :tid1
                 OR cta.teacher_id = :tid2
                 OR c.homeroom_teacher_id = :tid3
              ORDER BY c.name";
    $stmt = $db->prepare($query);
    $stmt->bindParam(":tid1", $teacher_id);
    $stmt->bindParam(":tid2", $teacher_id);
    $stmt->bindParam(":tid3", $teacher_id);
}

$stmt->execute();

$classes = $stmt->fetchAll(PDO::FETCH_ASSOC);

Response::success($classes);
