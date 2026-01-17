<?php
require_once __DIR__ . '/../bootstrap.php';

use App\Core\{Middleware, Response, Request, Bootstrap};

Middleware::cors('POST');

$db = Bootstrap::db();
$jwt = Bootstrap::jwt();
$data = Request::all();

if (!$data || !isset($data['phone']) || !isset($data['code'])) {
    Response::error('Thiếu dữ liệu', 400);
}

$phone = trim($data['phone']);
$code = trim($data['code']);

$q = $db->prepare("SELECT id FROM users WHERE phone=:p AND role='parent' LIMIT 1");
$q->execute([':p' => $phone]);
if ($q->rowCount() === 0) {
    Response::notFound('Không tìm thấy phụ huynh');
}
$parent = $q->fetch(PDO::FETCH_ASSOC);

$otp = $db->prepare("SELECT id FROM otp_codes WHERE phone=:p AND code=:c AND is_used=0 AND expires_at>NOW() ORDER BY id DESC LIMIT 1");
$otp->execute([':p' => $phone, ':c' => $code]);
if ($otp->rowCount() === 0) {
    Response::unauthorized('OTP không hợp lệ hoặc đã hết hạn');
}

$db->prepare("UPDATE otp_codes SET is_used=1 WHERE id=:id")->execute([':id' => $otp->fetch(PDO::FETCH_ASSOC)['id']]);
$db->prepare("UPDATE users SET last_login=NOW() WHERE id=:id")->execute([':id' => $parent['id']]);

$token = $jwt->encode(['sub' => $parent['id'], 'username' => $phone, 'role' => 'parent']);

Response::success([
    'message' => 'Đăng nhập thành công',
    'token' => $token,
    'user' => ['id' => $parent['id'], 'username' => $phone, 'full_name' => 'Phụ huynh', 'role' => 'parent']
]);
