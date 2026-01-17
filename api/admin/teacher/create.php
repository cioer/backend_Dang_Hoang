<?php
require_once __DIR__ . '/../../bootstrap.php';

use App\Core\{Middleware, Response, Request, Bootstrap};

Middleware::cors('POST');
Middleware::requireAdmin();

$db = Bootstrap::db();
$body = Request::all();

$username = $body['username'] ?? null;
$full_name = $body['full_name'] ?? null;
$email = $body['email'] ?? null;
$subjects = $body['subjects'] ?? [];
$homeroom_class = $body['homeroom_class'] ?? null;

if (!$username || !$full_name) {
    Response::error("Missing fields", 400);
}

$password = password_hash('password', PASSWORD_BCRYPT);
$stmt = $db->prepare("INSERT INTO users(username,password,full_name,role,email,phone_verified,password_must_change) VALUES (:u,:p,:f,'teacher',:e,0,1)");

try {
    $stmt->execute([":u" => $username, ":p" => $password, ":f" => $full_name, ":e" => $email]);
} catch (Exception $e) {
    Response::error("Username exists", 409);
}

$tid = $db->lastInsertId();

if ($homeroom_class) {
    $stc = $db->prepare("UPDATE classes SET homeroom_teacher_id=:tid WHERE name=:name");
    $stc->execute([":tid" => $tid, ":name" => $homeroom_class]);
}

foreach ($subjects as $s) {
    $ss = $db->prepare("INSERT INTO teacher_subjects(teacher_id,subject_id) SELECT :t,id FROM subjects WHERE name=:n");
    $ss->execute([":t" => $tid, ":n" => $s]);
}

Response::success(["message" => "OK", "teacher_id" => $tid]);
