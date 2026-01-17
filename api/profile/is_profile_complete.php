<?php
require_once __DIR__ . '/../bootstrap.php';

use App\Core\{Middleware, Response, Bootstrap};

Middleware::cors('GET');

$user = Middleware::optionalAuth();
if (!$user) {
    Response::success(["complete" => false, "message" => "Unauthorized"]);
}

$db = Bootstrap::db();
$userId = $user->id;
$role = $user->role;

// Get user info
$stmt = $db->prepare("SELECT full_name, email, phone, avatar FROM users WHERE id = :id LIMIT 1");
$stmt->execute([':id' => $userId]);
$userData = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$userData) {
    Response::success(["complete" => false]);
}

$isComplete = true;

// Basic checks
if (empty($userData['full_name'])) $isComplete = false;
if (empty($userData['phone'])) $isComplete = false;

Response::success([
    "complete" => $isComplete,
    "user" => $userData
]);
