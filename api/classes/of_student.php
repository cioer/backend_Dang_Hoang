<?php
include_once '../../config/database.php';
include_once '../../config/jwt.php';
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
$headers = getallheaders();
$token = isset($headers['Authorization']) ? str_replace('Bearer ', '', $headers['Authorization']) : null;
$decoded = $token ? validateJWT($token) : null;
if (!$decoded) { http_response_code(401); echo json_encode(["message"=>"Unauthorized"]); exit; }
$userId = $decoded['data']->id;
$db = (new Database())->getConnection();
$stmt = $db->prepare("SELECT c.id, c.name FROM student_details sd JOIN classes c ON c.id=sd.class_id WHERE sd.user_id=:uid");
$stmt->execute([":uid"=>$userId]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$row) { http_response_code(404); echo json_encode(["message"=>"Not found"]); exit; }
echo json_encode($row);
