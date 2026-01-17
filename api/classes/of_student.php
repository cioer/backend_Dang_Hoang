<?php
require_once __DIR__ . '/../bootstrap.php';

use App\Core\{Middleware, Response, Bootstrap};

Middleware::cors('GET');

$user = Middleware::auth();
$userId = $user->id;

$db = Bootstrap::db();

$stmt = $db->prepare("SELECT c.id, c.name FROM student_details sd JOIN classes c ON c.id=sd.class_id WHERE sd.user_id=:uid");
$stmt->execute([":uid" => $userId]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$row) {
    Response::notFound('Not found');
}

Response::success($row);
