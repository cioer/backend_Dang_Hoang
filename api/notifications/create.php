<?php
require_once __DIR__ . '/../bootstrap.php';

use App\Core\{Middleware, Response, Request, Bootstrap};

Middleware::cors('POST');

$user = Middleware::auth();

if ($user->role != 'teacher' && $user->role != 'admin') {
    Response::forbidden('Truy cập bị từ chối.');
}

$db = Bootstrap::db();
$data = Request::all();

$title = $data['title'] ?? "";
$content = $data['content'] ?? "";

if (empty($title) || empty($content)) {
    Response::error('Tiêu đề và nội dung không được để trống.', 400);
}

$query = "INSERT INTO notifications (title, content, sender_id, target_role)
          VALUES (:title, :content, :sender_id, :target_role)";
$stmt = $db->prepare($query);
$stmt->bindParam(":title", $title);
$stmt->bindParam(":content", $content);
$stmt->bindParam(":sender_id", $user->id);
$target_role = "all";
$stmt->bindParam(":target_role", $target_role);

if ($stmt->execute()) {
    Response::success(["message" => "Tạo thông báo thành công."]);
} else {
    Response::error('Lỗi hệ thống.', 503);
}
