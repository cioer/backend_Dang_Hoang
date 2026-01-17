<?php
require_once __DIR__ . '/../bootstrap.php';

use App\Core\{Middleware, Response, Request, Bootstrap};

Middleware::cors('POST');

$user = Middleware::auth();
$db = Bootstrap::db();

$user_id = $user->id;
$role = $user->role;

$contacts = array();

if ($role == 'parent') {
    // Parent sees teachers of their child
    $query = "SELECT DISTINCT u.id, u.full_name, 'teacher' as role
              FROM parent_student_links psl
              JOIN student_details sd ON psl.student_id = sd.user_id
              JOIN schedule s ON sd.class_id = s.class_id
              JOIN users u ON s.teacher_id = u.id
              WHERE psl.parent_id = :user_id";

    $stmt = $db->prepare($query);
    $stmt->bindParam(":user_id", $user_id);
    $stmt->execute();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        array_push($contacts, $row);
    }
} else if ($role == 'teacher') {
    // Teacher sees parents of students in their classes
    $query = "SELECT DISTINCT u.id, u.full_name, 'parent' as role
              FROM schedule s
              JOIN student_details sd ON s.class_id = sd.class_id
              JOIN parent_student_links psl ON sd.user_id = psl.student_id
              JOIN users u ON psl.parent_id = u.id
              WHERE s.teacher_id = :user_id";

    $stmt = $db->prepare($query);
    $stmt->bindParam(":user_id", $user_id);
    $stmt->execute();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        array_push($contacts, $row);
    }
} else if ($role == 'admin') {
    // Admin sees anyone who has messaged them (e.g., teacher requests)
    $query = "SELECT DISTINCT u.id, u.full_name, u.role
              FROM messages m
              JOIN users u ON u.id = m.sender_id
              WHERE m.receiver_id = :user_id
              ORDER BY u.full_name ASC";
    $stmt = $db->prepare($query);
    $stmt->bindParam(":user_id", $user_id);
    $stmt->execute();
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        array_push($contacts, $row);
    }
}

Response::success($contacts);
