<?php
require_once __DIR__ . '/../bootstrap.php';

use App\Core\{Middleware, Response, Request, Bootstrap};

Middleware::cors('POST');

$user = Middleware::auth();

if ($user->role != 'admin') {
    Response::unauthorized('Unauthorized');
}

$data = Request::all();

if (empty($data['id']) || !isset($data['is_active'])) {
    Response::error('Incomplete data.', 400);
}

$db = Bootstrap::db();

$query = "UPDATE banners SET is_active = :is_active WHERE id = :id";
$stmt = $db->prepare($query);

$is_active = $data['is_active'] ? 1 : 0;
$stmt->bindParam(":is_active", $is_active);
$stmt->bindParam(":id", $data['id']);

if ($stmt->execute()) {
    Response::success(["message" => "Banner status updated."]);
} else {
    Response::error('Unable to update banner status.', 503);
}
