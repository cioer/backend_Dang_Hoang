<?php
require_once __DIR__ . '/../bootstrap.php';

use App\Core\{Middleware, Response, Bootstrap};

Middleware::cors('POST');

$user = Middleware::auth();

if ($user->role != 'admin') {
    Response::unauthorized('Unauthorized access. Admin privileges required.');
}

$db = Bootstrap::db();

if (!isset($_FILES['image'])) {
    Response::error('No image file provided', 400);
}

// 1. Validate File Size (Max 5MB)
if ($_FILES['image']['size'] > 5 * 1024 * 1024) {
    Response::error('File quá lớn. Tối đa 5MB.', 400);
}

// 2. Validate MIME Type
$allowed_types = ['image/jpeg', 'image/png', 'image/jpg'];
$finfo = finfo_open(FILEINFO_MIME_TYPE);
$mime_type = finfo_file($finfo, $_FILES['image']['tmp_name']);
finfo_close($finfo);

if (!in_array($mime_type, $allowed_types)) {
    Response::error('Định dạng file không hợp lệ. Chỉ chấp nhận JPG, PNG.', 400);
}

$target_dir = __DIR__ . "/../../uploads/banners/";
if (!file_exists($target_dir)) {
    mkdir($target_dir, 0777, true);
}

$file_extension = pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION);
$new_filename = uniqid() . '.' . $file_extension;
$target_file = $target_dir . $new_filename;

if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
    $image_url = "uploads/banners/" . $new_filename;
    $title = $_POST['title'] ?? "";
    $cta_text = $_POST['cta_text'] ?? "Xem ngay";
    $link_url = $_POST['link_url'] ?? "";
    $priority = isset($_POST['priority']) ? (int)$_POST['priority'] : 0;

    $query = "INSERT INTO banners SET image_url=:image_url, title=:title, cta_text=:cta_text, link_url=:link_url, is_active=1, priority=:priority";
    $stmt = $db->prepare($query);

    $stmt->bindParam(":image_url", $image_url);
    $stmt->bindParam(":title", $title);
    $stmt->bindParam(":cta_text", $cta_text);
    $stmt->bindParam(":link_url", $link_url);
    $stmt->bindParam(":priority", $priority);

    if ($stmt->execute()) {
        // Log the action
        $log_query = "INSERT INTO banner_logs (admin_id, action, banner_info) VALUES (:admin_id, 'UPLOAD', :info)";
        $log_stmt = $db->prepare($log_query);
        $admin_id = $user->id;
        $info = "Uploaded banner: " . $title;
        $log_stmt->bindParam(":admin_id", $admin_id);
        $log_stmt->bindParam(":info", $info);
        $log_stmt->execute();

        Response::success(["message" => "Banner uploaded successfully"]);
    } else {
        Response::error('Database error', 500);
    }
} else {
    Response::error('File upload failed', 500);
}
