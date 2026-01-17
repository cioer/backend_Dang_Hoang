<?php
require_once __DIR__ . '/../bootstrap.php';

use App\Core\{Middleware, Response, Request, Bootstrap};

Middleware::cors('POST');

$user = Middleware::auth();
$db = Bootstrap::db();
$data = Request::all();

$receiver_id = $data['receiver_id'] ?? 0;
$content = $data['content'] ?? "";

if (empty($content) || $receiver_id == 0) {
    Response::error('Dữ liệu không hợp lệ.', 400);
}

$sender_id = $user->id;

$query = "INSERT INTO messages (sender_id, receiver_id, content) VALUES (:sender_id, :receiver_id, :content)";
$stmt = $db->prepare($query);
$stmt->bindParam(":sender_id", $sender_id);
$stmt->bindParam(":receiver_id", $receiver_id);
$stmt->bindParam(":content", $content);

if ($stmt->execute()) {
    Response::success(["message" => "Gửi tin nhắn thành công."]);
} else {
    Response::error('Lỗi hệ thống.', 503);
}
