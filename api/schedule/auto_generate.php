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
$class_name = $body['class_name'] ?? null;
$subject_name = $body['subject_name'] ?? null;
$teacher_id = $decoded['data']->id;
if (!$class_name || !$subject_name) { http_response_code(400); echo json_encode(["message"=>"Missing fields"]); exit; }
$cid = $db->prepare("SELECT id FROM classes WHERE name=:n"); $cid->execute([":n"=>$class_name]); $c = $cid->fetch(PDO::FETCH_ASSOC);
$sid = $db->prepare("SELECT id FROM subjects WHERE name=:n"); $sid->execute([":n"=>$subject_name]); $s = $sid->fetch(PDO::FETCH_ASSOC);
if (!$c || !$s) { http_response_code(404); echo json_encode(["message"=>"Class or subject not found"]); exit; }
$occupied = $db->prepare("SELECT day_of_week, period FROM schedule WHERE (class_id=:c OR teacher_id=:t)");
$occupied->execute([":c"=>$c['id'],":t"=>$teacher_id]);
$busy = [];
foreach ($occupied->fetchAll(PDO::FETCH_ASSOC) as $row) { $busy[$row['day_of_week'].'-'.$row['period']] = true; }
$result = [];
for ($dow=2; $dow<=7; $dow++) {
  for ($p=1; $p<=6; $p++) {
    $key = $dow.'-'.$p;
    if (!isset($busy[$key])) {
      $result[] = ["class"=>$class_name,"subject"=>$subject_name,"day_of_week"=>$dow,"period"=>$p,"start_time"=>"08:00","end_time"=>"08:45"];
      if (count($result)>=5) break;
    }
  }
  if (count($result)>=5) break;
}
echo json_encode(["suggestions"=>$result]);
