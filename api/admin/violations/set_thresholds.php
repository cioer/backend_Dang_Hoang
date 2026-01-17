<?php
include_once '../../../config/database.php';
include_once '../../../config/jwt.php';
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
$headers = getallheaders();
$token = isset($headers['Authorization']) ? str_replace('Bearer ', '', $headers['Authorization']) : null;
$decoded = $token ? validateJWT($token) : null;
if (!$decoded || $decoded['data']->role != 'admin') { http_response_code(403); echo json_encode(["message"=>"Forbidden"]); exit; }
$db = (new Database())->getConnection();
$body = json_decode(file_get_contents('php://input'), true);
$warn = isset($body['warn']) ? (int)$body['warn'] : null;
$conduct = isset($body['conduct']) ? (int)$body['conduct'] : null;
$class_change = isset($body['class_change']) ? (int)$body['class_change'] : null;
$class_name = isset($body['class_name']) ? $body['class_name'] : null;
if ($warn!==null) { $db->prepare("INSERT INTO system_settings(setting_key,setting_value) VALUES ('discipline_threshold_warn', :v) ON DUPLICATE KEY UPDATE setting_value=:v")->execute([":v"=>$warn]); }
if ($conduct!==null) { $db->prepare("INSERT INTO system_settings(setting_key,setting_value) VALUES ('discipline_threshold_conduct', :v) ON DUPLICATE KEY UPDATE setting_value=:v")->execute([":v"=>$conduct]); }
if ($class_change!==null) { $db->prepare("INSERT INTO system_settings(setting_key,setting_value) VALUES ('discipline_threshold_class_change', :v) ON DUPLICATE KEY UPDATE setting_value=:v")->execute([":v"=>$class_change]); }
if ($class_name!==null) { $db->prepare("INSERT INTO system_settings(setting_key,setting_value) VALUES ('discipline_class_name', :v) ON DUPLICATE KEY UPDATE setting_value=:v")->execute([":v"=>$class_name]); }
echo json_encode(["message"=>"OK"]);
