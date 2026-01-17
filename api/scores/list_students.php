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
if (!$decoded) { http_response_code(401); echo json_encode(["message"=>"Unauthorized"]); exit; }
$db = (new Database())->getConnection();
$body = json_decode(file_get_contents("php://input"), true);
$class_id = $body['class_id'] ?? null;
if (!$class_id) { http_response_code(400); echo json_encode(["message"=>"Missing class_id"]); exit; }
$stmt = $db->prepare("SELECT u.id, u.full_name, u.username AS code FROM users u JOIN student_details sd ON sd.user_id=u.id WHERE sd.class_id=:cid AND u.role='student' ORDER BY u.full_name ASC");
$stmt->execute([":cid"=>$class_id]);
echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
