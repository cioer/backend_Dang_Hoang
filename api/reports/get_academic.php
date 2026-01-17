<?php
require_once __DIR__ . '/../bootstrap.php';

use App\Core\{Middleware, Response, Request, Bootstrap};

Middleware::cors('POST');

$user = Middleware::auth();
$db = Bootstrap::db();
$data = Request::all();

$user_id = $user->id;
$role = $user->role;
$student_id = $user_id;

// If parent, get child's id (simplified)
if ($role == 'parent') {
    $query = "SELECT student_id FROM parent_student_links WHERE parent_id = :parent_id LIMIT 1";
    $stmt = $db->prepare($query);
    $stmt->bindParam(":parent_id", $user_id);
    $stmt->execute();
    if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $student_id = $row['student_id'];
    }
}

$query = "SELECT s.name as subject_name, sc.score_15m, sc.score_45m, sc.score_final
          FROM scores sc
          JOIN subjects s ON sc.subject_id = s.id
          WHERE sc.student_id = :student_id";

$stmt = $db->prepare($query);
$stmt->bindParam(":student_id", $student_id);
$stmt->execute();

$scores = $stmt->fetchAll(PDO::FETCH_ASSOC);

Response::success($scores);
