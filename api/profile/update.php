<?php
include_once '../../config/database.php';
include_once '../../config/jwt.php';
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
$headers = getallheaders();
$token = isset($headers['Authorization']) ? str_replace('Bearer ', '', $headers['Authorization']) : null;
$decoded = validateJWT($token);
if (!$decoded) { http_response_code(401); echo json_encode(["message"=>"Unauthorized"]); exit; }
$db = (new Database())->getConnection();
$data = json_decode(file_get_contents("php://input"));
$target_id = isset($data->user_id) ? intval($data->user_id) : $decoded['data']->id;
if ($decoded['data']->role != 'admin' && $target_id !== $decoded['data']->id) { http_response_code(403); echo json_encode(["message"=>"Forbidden"]); exit; }
$stmt = $db->prepare("SELECT id, full_name, email, phone, avatar FROM users WHERE id=:id LIMIT 1");
$stmt->execute([':id'=>$target_id]);
$prev = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$prev) { http_response_code(404); echo json_encode(["message"=>"Not found"]); exit; }
$full_name = isset($data->full_name) ? $data->full_name : $prev['full_name'];
$email = isset($data->email) ? $data->email : $prev['email'];
$phone = isset($data->phone) ? $data->phone : $prev['phone'];
$avatar = isset($data->avatar_url) ? $data->avatar_url : $prev['avatar'];
$upd = $db->prepare("UPDATE users SET full_name=:fn, email=:em, phone=:ph, avatar=:av WHERE id=:id");
$ok = $upd->execute([':fn'=>$full_name, ':em'=>$email, ':ph'=>$phone, ':av'=>$avatar, ':id'=>$target_id]);
if ($ok) {
  $log = $db->prepare("INSERT INTO audit_logs (user_id, action, details, ip) VALUES (:uid,'PROFILE_UPDATE',:details,:ip)");
  $ip = $_SERVER['REMOTE_ADDR'] ?? null;
  $details = json_encode(['before'=>$prev,'after'=>['full_name'=>$full_name,'email'=>$email,'phone'=>$phone,'avatar'=>$avatar]]);
  $log->execute([':uid'=>$decoded['data']->id, ':details'=>$details, ':ip'=>$ip]);
  echo json_encode(['message'=>'Updated']);
} else { http_response_code(500); echo json_encode(['message'=>'Update failed']); }
?>
