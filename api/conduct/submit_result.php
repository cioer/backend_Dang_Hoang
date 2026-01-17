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
if (!$decoded || $decoded['data']->role!='teacher') { http_response_code(403); echo json_encode(["message"=>"Forbidden"]); exit; }
$db = (new Database())->getConnection();
$body = json_decode(file_get_contents('php://input'), true);
$class_id = $body['class_id'] ?? null;
$items = $body['items'] ?? [];
if (!$class_id || !is_array($items)) { http_response_code(400); echo json_encode(["message"=>"Missing fields"]); exit; }
$tid = $decoded['data']->id;
$stmt = $db->prepare("INSERT INTO conduct_results(student_id, teacher_id, class_id, date, score, comment) VALUES (:sid,:tid,:cid,:d,:s,:c)");
foreach ($items as $it) {
  $sid = $it['student_id'] ?? null; $score = $it['score'] ?? null; $comment = $it['comment'] ?? '';
  if ($sid===null || $score===null) continue;
  $stmt->execute([":sid"=>$sid,":tid"=>$tid,":cid"=>$class_id,":d"=>date('Y-m-d'),":s"=>$score,":c"=>$comment]);
}
echo json_encode(["message"=>"OK"]);
