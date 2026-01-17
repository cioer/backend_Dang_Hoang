<?php
require_once __DIR__ . '/../bootstrap.php';

use App\Core\{Middleware, Response, Request, Bootstrap};

Middleware::cors('POST');

$user = Middleware::auth();
$db = Bootstrap::db();
$data = Request::all();

$target_id = isset($data['user_id']) ? intval($data['user_id']) : $user->id;

if ($user->role != 'admin' && $target_id !== $user->id) {
    Response::forbidden();
}

$stmt = $db->prepare("SELECT id, username, full_name, role, email, phone, avatar FROM users WHERE id=:id LIMIT 1");
$stmt->execute([':id' => $target_id]);
$userData = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$userData) {
    Response::notFound();
}

// Check Red Star status if student
if ($userData['role'] == 'student') {
    $stmtRed = $db->prepare("SELECT COUNT(*) FROM red_committee_members WHERE user_id=:uid AND active=1");
    $stmtRed->execute([':uid' => $userData['id']]);
    $userData['is_red_star'] = $stmtRed->fetchColumn() > 0 ? 1 : 0;
} else {
    $userData['is_red_star'] = 0;
}

Response::success($userData);
