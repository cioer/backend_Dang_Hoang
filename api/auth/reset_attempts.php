<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
include_once '../../config/database.php';
include_once '../../config/jwt.php';
$database = new Database();
$db = $database->getConnection();
$data = json_decode(file_get_contents("php://input"), true);
$token = isset($data['token']) ? $data['token'] : (isset($_SERVER['HTTP_AUTHORIZATION']) ? str_replace('Bearer ', '', $_SERVER['HTTP_AUTHORIZATION']) : "");
$decoded = validateJWT($token);
if (!$decoded || $decoded['data']->role != 'admin') { http_response_code(401); echo json_encode(["message"=>"Unauthorized"]); exit; }
$username = isset($data['username']) ? trim($data['username']) : null;
if (!$username) { http_response_code(400); echo json_encode(["message"=>"Thiếu username"]); exit; }
$stmt = $db->prepare("DELETE FROM login_attempts WHERE username=:u");
$ok = $stmt->execute([":u"=>$username]);
echo json_encode(["ok"=>$ok,"username"=>$username]);
