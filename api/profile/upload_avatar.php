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
            break;
        case UPLOAD_ERR_PARTIAL:
            Response::error('File tải lên bị lỗi', 400);
            break;
        case UPLOAD_ERR_NO_FILE:
            Response::error('Không có file', 400);
            break;
        default:
            Response::error('Lỗi upload: ' . $file['error'], 500);
            break;
    }
}

$allowed = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];

$finfo = finfo_open(FILEINFO_MIME_TYPE);
$mime = finfo_file($finfo, $file['tmp_name']);

if (!isset($allowed[$mime])) {
    Response::error('Định dạng không hợp lệ. Chỉ chấp nhận JPG, PNG, WEBP', 400);
}

$dir = __DIR__ . '/../../uploads/avatars';
// Create directory if not exists
if (!file_exists($dir)) {
    if (!mkdir($dir, 0775, true)) {
        $error = error_get_last();
        Response::error('Lỗi server: Không thể tạo thư mục lưu ảnh. ' . ($error['message'] ?? ''), 500);
    }
}

// Ensure directory is writable
if (!is_writable($dir)) {
     // Try to chmod if owner
     @chmod($dir, 0775);
     if (!is_writable($dir)) {
         Response::error('Lỗi server: Thư mục lưu ảnh không có quyền ghi (Permission denied)', 500);
     }
}

$name = 'avatar_' . $user->id . '_' . time() . '.' . $allowed[$mime];
$path = $dir . '/' . $name;

if (!move_uploaded_file($file['tmp_name'], $path)) {
    $error = error_get_last();
    Response::error('Không thể lưu file ảnh: ' . ($error['message'] ?? 'Unknown error'), 500);
}

$upd = $db->prepare("UPDATE users SET avatar=:av WHERE id=:id");
$upd->execute([':av' => $name, ':id' => $user->id]);

$scheme = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'];
$url = $scheme . '://' . $host . '/Backend/uploads/avatars/' . $name;

Response::success(['message' => 'Uploaded', 'url' => $url, 'file' => $name]);
