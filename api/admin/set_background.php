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
if (!$decoded || ($decoded['data']->role != 'admin')) { http_response_code(403); echo json_encode(["message"=>"Forbidden"]); exit; }
$db = (new Database())->getConnection();
$data = json_decode(file_get_contents("php://input"));
$target = isset($data->target) ? $data->target : null;
$key = isset($data->image_key) ? $data->image_key : null;
if (!$target || !$key || !in_array($target, ['mobile','pc'])) { http_response_code(400); echo json_encode(["message"=>"Invalid data"]); exit; }
$setting_key = $target === 'mobile' ? 'bg_mobile' : 'bg_pc';
$stmt = $db->prepare("INSERT INTO system_settings (setting_key, setting_value) VALUES (:k,:v) ON DUPLICATE KEY UPDATE setting_value=:v");
$stmt->execute([':k'=>$setting_key, ':v'=>$key]);
// Audit log
$log = $db->prepare("INSERT INTO audit_logs (user_id, action, details, ip) VALUES (:uid, 'SET_BACKGROUND', :details, :ip)");
$ip = $_SERVER['REMOTE_ADDR'] ?? null;
$log->execute([':uid'=>$decoded['data']->id, ':details'=>"{$setting_key}={$key}", ':ip'=>$ip]);
echo json_encode(["message"=>"Saved"]);
?>
