<?php
require_once __DIR__ . '/../bootstrap.php';

use App\Core\{Middleware, Response, Request, Bootstrap};

Middleware::cors('POST');

$user = Middleware::auth();

if ($user->role != 'admin') {
    Response::unauthorized('Unauthorized');
}

$db = Bootstrap::db();
$data = Request::all();

if (!isset($data['id'])) {
    Response::error('Missing ID', 400);
}

$query = "DELETE FROM banners WHERE id = :id";
$stmt = $db->prepare($query);
$stmt->bindParam(":id", $data['id']);

if ($stmt->execute()) {
    // Log the action
    $log_query = "INSERT INTO banner_logs (admin_id, action, banner_info) VALUES (:admin_id, 'DELETE', :info)";
    $log_stmt = $db->prepare($log_query);
    $admin_id = $user->id;
    $info = "Deleted banner ID: " . $data['id'];
    $log_stmt->bindParam(":admin_id", $admin_id);
    $log_stmt->bindParam(":info", $info);
    $log_stmt->execute();

    Response::success(["message" => "Banner deleted"]);
} else {
    Response::error('Error deleting banner', 500);
}
