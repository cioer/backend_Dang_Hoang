<?php
require_once __DIR__ . '/../bootstrap.php';

use App\Core\{Middleware, Response, Bootstrap};

Middleware::cors('POST');

$user = Middleware::auth();
$db = Bootstrap::db();

if (!isset($_FILES['image'])) {
    Response::error('Vui lòng chọn ảnh', 400);
}

$file = $_FILES['image'];

// Check upload errors
if ($file['error'] !== UPLOAD_ERR_OK) {
    switch ($file['error']) {
        case UPLOAD_ERR_INI_SIZE:
        case UPLOAD_ERR_FORM_SIZE:
            Response::error('File ảnh quá lớn', 400);
        case UPLOAD_ERR_PARTIAL:
            Response::error('File tải lên bị lỗi', 400);
        case UPLOAD_ERR_NO_FILE:
            Response::error('Không có file', 400);
        default:
            Response::error('Lỗi upload: ' . $file['error'], 500);
    }
}

$allowed = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];

$finfo = finfo_open(FILEINFO_MIME_TYPE);
$mime = finfo_file($finfo, $file['tmp_name']);

if (!isset($allowed[$mime])) {
    Response::error('Invalid type', 400);
}

$dir = __DIR__ . '/../../uploads/avatars';
if (!is_dir($dir)) {
    mkdir($dir, 0775, true);
}

$name = 'avatar_' . $user->id . '_' . time() . '.' . $allowed[$mime];
$path = $dir . '/' . $name;

if (!move_uploaded_file($file['tmp_name'], $path)) {
    Response::error('Save failed', 500);
}

$upd = $db->prepare("UPDATE users SET avatar=:av WHERE id=:id");
$upd->execute([':av' => $name, ':id' => $user->id]);

$scheme = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'];
$url = $scheme . '://' . $host . '/Backend/uploads/avatars/' . $name;

Response::success(['message' => 'Uploaded', 'url' => $url, 'file' => $name]);
