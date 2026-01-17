<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
include_once '../../config/database.php';
$database = new Database();
$db = $database->getConnection();
$data = json_decode(file_get_contents("php://input"), true);
if(!$data || !isset($data['phone'])){echo json_encode(['message'=>'Thiếu dữ liệu']);http_response_code(400);exit;}
$phone = trim($data['phone']);
$stmt = $db->prepare("SELECT id,phone_verified FROM users WHERE phone=:p AND role='parent' LIMIT 1");
$stmt->execute([':p'=>$phone]);
if($stmt->rowCount()===0){echo json_encode(['message'=>'Không tìm thấy phụ huynh']);http_response_code(404);exit;}
$u = $stmt->fetch(PDO::FETCH_ASSOC);
if(intval($u['phone_verified'])!==1){echo json_encode(['message'=>'Số điện thoại chưa xác minh']);http_response_code(403);exit;}
$code = str_pad(strval(random_int(0,999999)),6,'0',STR_PAD_LEFT);
$expires = date('Y-m-d H:i:s', time()+300);
$ins = $db->prepare("INSERT INTO otp_codes(phone,code,expires_at,is_used) VALUES(:p,:c,:e,0)");
$ins->execute([':p'=>$phone,':c'=>$code,':e'=>$expires]);
echo json_encode(['message'=>'Đã gửi OTP','phone'=>$phone,'dev_code'=>$code]);
http_response_code(200);
?>
