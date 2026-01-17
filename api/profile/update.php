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

$stmt = $db->prepare("SELECT id, full_name, email, phone, avatar FROM users WHERE id=:id LIMIT 1");
$stmt->execute([':id' => $target_id]);
$prev = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$prev) {
    Response::notFound();
}

$full_name = $data['full_name'] ?? $prev['full_name'];
$email = $data['email'] ?? $prev['email'];
$phone = $data['phone'] ?? $prev['phone'];
$avatar = $data['avatar_url'] ?? $prev['avatar'];

$upd = $db->prepare("UPDATE users SET full_name=:fn, email=:em, phone=:ph, avatar=:av WHERE id=:id");
$ok = $upd->execute([':fn' => $full_name, ':em' => $email, ':ph' => $phone, ':av' => $avatar, ':id' => $target_id]);

if ($ok) {
    $log = $db->prepare("INSERT INTO audit_logs (user_id, action, details, ip) VALUES (:uid,'PROFILE_UPDATE',:details,:ip)");
    $ip = Request::ip();
    $details = json_encode(['before' => $prev, 'after' => ['full_name' => $full_name, 'email' => $email, 'phone' => $phone, 'avatar' => $avatar]]);
    $log->execute([':uid' => $user->id, ':details' => $details, ':ip' => $ip]);
    Response::message('Updated');
} else {
    Response::error('Update failed', 500);
}
