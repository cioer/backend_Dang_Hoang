<?php
require_once __DIR__ . '/../bootstrap.php';

use App\Core\{Middleware, Response, Request, Bootstrap};

Middleware::cors('POST');

$db = Bootstrap::db();
$jwt = Bootstrap::jwt();
$captcha = Bootstrap::captcha();
$repo = new UserRepository($db);
$ip = Request::ip();
$data = Request::all();

function log_attempt($db, $u, $s, $r, $ip) {
    $q = $db->prepare("INSERT INTO login_attempts(username,success,reason,ip) VALUES(:u,:s,:r,:i)");
    $q->execute([':u' => $u, ':s' => $s, ':r' => $r, ':i' => $ip]);
}

if (!$data || !isset($data['username']) || !isset($data['password'])) {
    Response::error('Thiếu dữ liệu', 400);
}

$username = trim($data['username']);
$password = $data['password'];

$user = $repo->getByUsernameRoleIn($username, ['teacher', 'admin']);
if (!$user) {
    log_attempt($db, $username, 0, 'not_found', $ip);
    Response::success(['message' => 'Tài khoản không tồn tại', 'code' => 'not_found'], 401);
}

if ($repo->isLocked($user)) {
    log_attempt($db, $username, 0, 'locked', $ip);
    Response::success(['message' => 'Tài khoản đã bị khóa', 'code' => 'locked'], 403);
}

$failsStmt = $db->prepare("SELECT COUNT(*) c FROM login_attempts WHERE username=:u AND success=0 AND created_at> (NOW() - INTERVAL 15 MINUTE)");
$failsStmt->execute([':u' => $username]);
$fails = intval($failsStmt->fetch(PDO::FETCH_ASSOC)['c']);

if ($fails >= 3) {
    if (strtolower($user['role']) !== 'admin') {
        if (!isset($data['captcha_token']) || !isset($data['captcha_answer']) || !$captcha->verify($data['captcha_token'], $data['captcha_answer'])) {
            $challenge = $captcha->generate();
            Response::success([
                'message' => 'Yêu cầu xác minh CAPTCHA',
                'code' => 'captcha_required',
                'captcha_required' => true,
                'captcha_question' => $challenge['question'],
                'captcha_token' => $challenge['token']
            ], 401);
        }
    }
}

if (!$repo->verifyPassword($user['password'], $password)) {
    log_attempt($db, $username, 0, 'wrong_password', $ip);
    Response::success(['message' => 'Sai mật khẩu', 'code' => 'wrong_password'], 401);
}

log_attempt($db, $username, 1, 'success', $ip);
$repo->updateLastLogin(intval($user['id']));

$token = $jwt->encode(['sub' => $user['id'], 'username' => $user['username'], 'role' => $user['role']]);

Response::success([
    'message' => 'Đăng nhập thành công',
    'token' => $token,
    'user' => [
        'id' => $user['id'],
        'username' => $user['username'],
        'full_name' => $user['full_name'],
        'role' => $user['role']
    ]
]);
