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
if (!in_array($decoded['data']->role, ['teacher','admin'])) { http_response_code(403); echo json_encode(["message"=>"Forbidden"]); exit; }
$db = (new Database())->getConnection();
$body = json_decode(file_get_contents("php://input"), true);
$class_id = $body['class_id'] ?? null;
$subject_id = $body['subject_id'] ?? null;
$term = $body['term'] ?? 'HK1';
$items = $body['items'] ?? [];
if (!$class_id || !$subject_id || !is_array($items)) { http_response_code(400); echo json_encode(["message"=>"Missing fields"]); exit; }
$stmt = $db->prepare("INSERT INTO scores(student_id, subject_id, term, score, created_at) VALUES (:sid,:sub,:term,:score,NOW()) ON DUPLICATE KEY UPDATE score=:score");
foreach ($items as $it) {
  $sid = $it['student_id'] ?? null;
  $sc = $it['score'] ?? null;
  if ($sid===null || $sc===null) continue;
  $stmt->execute([":sid"=>$sid,":sub"=>$subject_id,":term"=>$term,":score"=>$sc]);
}
echo json_encode(["message"=>"OK"]);
