<?php
require_once __DIR__ . '/../bootstrap.php';

use App\Core\{Middleware, Response, Request, Bootstrap};

Middleware::cors('POST');

$db = Bootstrap::db();
$repo = new UserRepository($db);

$user = Middleware::auth();
$data = Request::all();

$old_password = $data['old_password'] ?? '';
$new_password = $data['new_password'] ?? '';

// Verify old password
$stmt = $db->prepare("SELECT password FROM users WHERE id = :id LIMIT 1");
$stmt->bindParam(":id", $user->id);
$stmt->execute();
$userData = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$userData || !$repo->verifyPassword($userData['password'], $old_password)) {
    Response::error('Mật khẩu cũ không đúng.', 400);
}

// Update password
if ($repo->updatePassword(intval($user->id), $new_password)) {
    Response::message('Đổi mật khẩu thành công.');
} else {
    Response::error('Không thể cập nhật mật khẩu.', 503);
}
