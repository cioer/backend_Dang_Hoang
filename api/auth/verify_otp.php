<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
include_once '../../config/database.php';
include_once '../../config/jwt.php';
$database = new Database();
$db = $database->getConnection();
$jwt = new JWT();
$data = json_decode(file_get_contents("php://input"), true);
if(!$data || !isset($data['phone']) || !isset($data['code'])){echo json_encode(['message'=>'Thiếu dữ liệu']);http_response_code(400);exit;}
$phone = trim($data['phone']);
$code = trim($data['code']);
$q = $db->prepare("SELECT id FROM users WHERE phone=:p AND role='parent' LIMIT 1");
$q->execute([':p'=>$phone]);
if($q->rowCount()===0){echo json_encode(['message'=>'Không tìm thấy phụ huynh']);http_response_code(404);exit;}
$parent = $q->fetch(PDO::FETCH_ASSOC);
$otp = $db->prepare("SELECT id FROM otp_codes WHERE phone=:p AND code=:c AND is_used=0 AND expires_at>NOW() ORDER BY id DESC LIMIT 1");
$otp->execute([':p'=>$phone,':c'=>$code]);
if($otp->rowCount()===0){echo json_encode(['message'=>'OTP không hợp lệ hoặc đã hết hạn']);http_response_code(401);exit;}
$db->prepare("UPDATE otp_codes SET is_used=1 WHERE id=:id")->execute([':id'=>$otp->fetch(PDO::FETCH_ASSOC)['id']]);
$db->prepare("UPDATE users SET last_login=NOW() WHERE id=:id")->execute([':id'=>$parent['id']]);
$token = $jwt->encode(['sub'=>$parent['id'],'username'=>$phone,'role'=>'parent']);
echo json_encode(['message'=>'Đăng nhập thành công','token'=>$token,'user'=>['id'=>$parent['id'],'username'=>$phone,'full_name'=>'Phụ huynh','role'=>'parent']]);
http_response_code(200);
?>
