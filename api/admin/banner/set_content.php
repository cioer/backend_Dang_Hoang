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
if (!$decoded || $decoded['data']->role!='admin') { http_response_code(403); echo json_encode(["message"=>"Forbidden"]); exit; }
$db = (new Database())->getConnection();
$body = json_decode(file_get_contents('php://input'), true);
$index = $body['index'] ?? 1; // 1..3
$text = $body['text'] ?? '';
$link = $body['link'] ?? '';
$db->prepare("INSERT INTO system_settings(setting_key,setting_value) VALUES (:k1,:v1) ON DUPLICATE KEY UPDATE setting_value=:v1")->execute([":k1"=>"banner_text_".$index,":v1"=>$text]);
$db->prepare("INSERT INTO system_settings(setting_key,setting_value) VALUES (:k2,:v2) ON DUPLICATE KEY UPDATE setting_value=:v2")->execute([":k2"=>"banner_link_".$index,":v2"=>$link]);
echo json_encode(["message"=>"OK"]);
