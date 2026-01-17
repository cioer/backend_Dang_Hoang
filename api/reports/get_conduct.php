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

$query = "SELECT type, content, date FROM conduct WHERE student_id = :student_id ORDER BY date DESC";

$stmt = $db->prepare($query);
$stmt->bindParam(":student_id", $student_id);
$stmt->execute();

$conducts = $stmt->fetchAll(PDO::FETCH_ASSOC);

Response::success($conducts);
