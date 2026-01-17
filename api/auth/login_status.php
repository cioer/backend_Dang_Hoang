<?php
require_once __DIR__ . '/../bootstrap.php';

use App\Core\{Middleware, Response, Request, Bootstrap};

Middleware::cors('GET');

$db = Bootstrap::db();

$username = Request::get('username');
if (!$username) {
    Response::error('Thiếu username', 400);
}

$username = trim($username);

$userStmt = $db->prepare("SELECT id,username,role,is_locked FROM users WHERE username=:u LIMIT 1");
$userStmt->execute([":u" => $username]);
if ($userStmt->rowCount() === 0) {
    Response::notFound('Không tìm thấy tài khoản');
}

$user = $userStmt->fetch(PDO::FETCH_ASSOC);

$failsStmt = $db->prepare("SELECT COUNT(*) c FROM login_attempts WHERE username=:u AND success=0 AND created_at> (NOW() - INTERVAL 15 MINUTE)");
$failsStmt->execute([":u" => $username]);
$fails = intval($failsStmt->fetch(PDO::FETCH_ASSOC)['c']);

$captcha_required = $fails >= 3;

Response::success([
    "username" => $user['username'],
    "role" => $user['role'],
    "is_locked" => intval($user['is_locked']) === 1,
    "recent_fail_count" => $fails,
    "captcha_required" => $captcha_required
]);
