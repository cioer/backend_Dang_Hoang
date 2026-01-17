<?php
require_once __DIR__ . '/../bootstrap.php';

use App\Core\{Middleware, Response, Bootstrap};

Middleware::cors('POST');
Middleware::requireAdmin();

$target = isset($_POST['target']) ? $_POST['target'] : null;
if (!$target || !in_array($target, ['mobile', 'pc', 'sub1', 'sub2', 'sub3'])) {
    Response::error("Invalid target", 400);
}

if (!isset($_FILES['image'])) {
    Response::error("No file", 400);
}

$file = $_FILES['image'];
if ($file['error'] !== UPLOAD_ERR_OK) {
    Response::error("Upload error", 400);
}

$allowed = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
$finfo = finfo_open(FILEINFO_MIME_TYPE);
$mime = finfo_file($finfo, $file['tmp_name']);

if (!isset($allowed[$mime])) {
    Response::error("Invalid type", 400);
}

$ext = $allowed[$mime];
$dir = __DIR__ . '/../../uploads/backgrounds';
if (!is_dir($dir)) {
    mkdir($dir, 0775, true);
}

$name = 'bg_' . $target . '_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
$path = $dir . '/' . $name;

if (!move_uploaded_file($file['tmp_name'], $path)) {
    Response::serverError("Save failed");
}

$db = Bootstrap::db();
$setting_key = $target === 'mobile' ? 'bg_mobile' : ($target === 'pc' ? 'bg_pc' : ($target === 'sub1' ? 'bg_sub1' : ($target === 'sub2' ? 'bg_sub2' : ($target === 'sub3' ? 'bg_sub3' : null))));

if ($setting_key === null) {
    Response::error("Invalid target", 400);
}

$stmt = $db->prepare("INSERT INTO system_settings (setting_key, setting_value) VALUES (:k,:v) ON DUPLICATE KEY UPDATE setting_value=:v");
$stmt->execute([':k' => $setting_key, ':v' => $name]);

// Build absolute public URL
$scheme = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'];
$publicUrl = $scheme . '://' . $host . '/Backend/uploads/backgrounds/' . $name;

Response::success(["message" => "Uploaded", "file" => $name, "url" => $publicUrl]);
