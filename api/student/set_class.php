<?php
require_once __DIR__ . '/../bootstrap.php';

use App\Core\{Middleware, Response, Request, Bootstrap};

Middleware::cors('POST');

$user = Middleware::auth();
$db = Bootstrap::db();
$data = Request::all();

$class_id = isset($data['class_id']) ? (int)$data['class_id'] : 0;

if ($class_id <= 0) {
    Response::error('Missing class_id', 400);
}

$user_id = $user->id;

$exists = $db->prepare("SELECT 1 FROM class_registrations WHERE student_id=:uid");
$exists->execute([":uid" => $user_id]);

if ($exists->fetch()) {
    $upd = $db->prepare("UPDATE class_registrations SET class_id=:cid, registered_at=NOW() WHERE student_id=:uid");
    $upd->execute([":cid" => $class_id, ":uid" => $user_id]);
} else {
    $ins = $db->prepare("INSERT INTO class_registrations(student_id, class_id) VALUES (:uid, :cid)");
    $ins->execute([":uid" => $user_id, ":cid" => $class_id]);
}

Response::success(["message" => "OK"]);
