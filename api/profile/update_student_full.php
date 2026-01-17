<?php
include_once '../../config/database.php';
include_once '../../config/jwt.php';
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
$headers = getallheaders();
$token = isset($headers['Authorization']) ? str_replace('Bearer ', '', $headers['Authorization']) : null;
$decoded = $token ? validateJWT($token) : null;
if (!$decoded || $decoded['data']->role!='student') { http_response_code(403); echo json_encode(["message"=>"Forbidden"]); exit; }
$db = (new Database())->getConnection();
$body = json_decode(file_get_contents('php://input'), true);
$fields = ['full_name','birth_date','address','phone','email','guardian_name','guardian_phone'];
foreach ($fields as $f) { if (!isset($body[$f]) || $body[$f]==='') { http_response_code(400); echo json_encode(["message"=>"Missing ".$f]); exit; } }
$uid = $decoded['data']->id;
$stmt = $db->prepare("INSERT INTO student_profiles(user_id, full_name, birth_date, address, phone, email, guardian_name, guardian_phone, is_complete) VALUES (:uid,:fn,:bd,:ad,:ph,:em,:gn,:gp,1) ON DUPLICATE KEY UPDATE full_name=:fn,birth_date=:bd,address=:ad,phone=:ph,email=:em,guardian_name=:gn,guardian_phone=:gp,is_complete=1");
$stmt->execute([
  ":uid"=>$uid, ":fn"=>$body['full_name'], ":bd"=>$body['birth_date'], ":ad"=>$body['address'],
  ":ph"=>$body['phone'], ":em"=>$body['email'], ":gn"=>$body['guardian_name'], ":gp"=>$body['guardian_phone']
]);
echo json_encode(["message"=>"OK"]);
