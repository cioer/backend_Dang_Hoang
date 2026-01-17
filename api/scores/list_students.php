<?php
require_once __DIR__ . '/../bootstrap.php';

use App\Core\{Middleware, Response, Request, Bootstrap};

Middleware::cors('POST');

$user = Middleware::auth();
$db = Bootstrap::db();
$data = Request::all();

$class_id = $data['class_id'] ?? null;

if (!$class_id) {
    Response::error('Missing class_id', 400);
}

$stmt = $db->prepare("SELECT u.id, u.full_name, u.username AS code FROM users u JOIN student_details sd ON sd.user_id=u.id WHERE sd.class_id=:cid AND u.role='student' ORDER BY u.full_name ASC");
$stmt->execute([":cid" => $class_id]);

Response::success($stmt->fetchAll(PDO::FETCH_ASSOC));
