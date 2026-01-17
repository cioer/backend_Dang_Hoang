<?php
require_once __DIR__ . '/../bootstrap.php';

use App\Core\{Middleware, Response, Request, Bootstrap};

Middleware::cors('POST');

$db = Bootstrap::db();
$data = Request::all();

if (!$data || !isset($data['phone'])) {
    Response::error('Thiếu dữ liệu', 400);
}

$phone = trim($data['phone']);

$stmt = $db->prepare("SELECT id,phone_verified FROM users WHERE phone=:p AND role='parent' LIMIT 1");
$stmt->execute([':p' => $phone]);
if ($stmt->rowCount() === 0) {
    Response::notFound('Không tìm thấy phụ huynh');
}

$u = $stmt->fetch(PDO::FETCH_ASSOC);
if (intval($u['phone_verified']) !== 1) {
    Response::forbidden('Số điện thoại chưa xác minh');
}

$code = str_pad(strval(random_int(0, 999999)), 6, '0', STR_PAD_LEFT);
$expires = date('Y-m-d H:i:s', time() + 300);

$ins = $db->prepare("INSERT INTO otp_codes(phone,code,expires_at,is_used) VALUES(:p,:c,:e,0)");
$ins->execute([':p' => $phone, ':c' => $code, ':e' => $expires]);

Response::success(['message' => 'Đã gửi OTP', 'phone' => $phone, 'dev_code' => $code]);
