<?php
require_once __DIR__ . '/../../bootstrap.php';

use App\Core\{Middleware, Response, Request, Bootstrap};

Middleware::cors('POST');
Middleware::requireAdmin();

$db = Bootstrap::db();
$data = Request::all();

if (empty($data['id']) && empty($data['user_id'])) {
    Response::error("User ID required.", 400);
}
$id = !empty($data['id']) ? $data['id'] : $data['user_id'];

$query = "SELECT id, username, full_name, email, phone, role FROM users WHERE id = :id LIMIT 0,1";
$stmt = $db->prepare($query);
$stmt->bindParam(":id", $id);
$stmt->execute();

$num = $stmt->rowCount();
if ($num > 0) {
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    Response::success($row);
} else {
    Response::notFound("User not found.");
}
