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
$body = json_decode(file_get_contents('php://input'), true);
$class_id = isset($body['class_id']) ? (int)$body['class_id'] : 0;
if ($class_id <= 0) { http_response_code(400); echo json_encode(["message"=>"Missing class_id"]); exit; }
$user_id = $decoded['data']->id;
$exists = $db->prepare("SELECT 1 FROM class_registrations WHERE student_id=:uid");
$exists->execute([":uid"=>$user_id]);
if ($exists->fetch()) {
  $upd = $db->prepare("UPDATE class_registrations SET class_id=:cid, registered_at=NOW() WHERE student_id=:uid");
  $upd->execute([":cid"=>$class_id, ":uid"=>$user_id]);
} else {
  $ins = $db->prepare("INSERT INTO class_registrations(student_id, class_id) VALUES (:uid, :cid)");
  $ins->execute([":uid"=>$user_id, ":cid"=>$class_id]);
}
echo json_encode(["message"=>"OK"]);
