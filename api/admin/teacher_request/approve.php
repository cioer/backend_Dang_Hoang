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
$request_id = $body['request_id'] ?? null;
$approve = $body['approve'] ?? true;
if (!$request_id) { http_response_code(400); echo json_encode(["message"=>"Missing request_id"]); exit; }
$q = $db->prepare("SELECT * FROM teacher_class_requests WHERE id=:id");
$q->execute([":id"=>$request_id]);
$req = $q->fetch(PDO::FETCH_ASSOC);
if (!$req) { http_response_code(404); echo json_encode(["message"=>"Request not found"]); exit; }
if ($approve) {
  $count = $db->prepare("SELECT COUNT(*) AS c FROM class_registrations WHERE class_id=:cid");
  $count->execute([":cid"=>$req['class_id']]);
  $has = $count->fetch(PDO::FETCH_ASSOC);
  if (!$has || (int)$has['c'] == 0) { http_response_code(400); echo json_encode(["message"=>"Lớp chưa có học sinh"]); exit; }
  $chkHm = $db->prepare("SELECT id,name FROM classes WHERE homeroom_teacher_id=:tid");
  $chkHm->execute([":tid"=>$req['teacher_id']]);
  $hm = $chkHm->fetch(PDO::FETCH_ASSOC);
  if ($hm) { http_response_code(400); echo json_encode(["message"=>"Giáo viên đã là chủ nhiệm lớp ".$hm['name']]); exit; }
  $db->prepare("UPDATE teacher_class_requests SET status='approved', approved_at=NOW(), admin_id=:aid WHERE id=:id")->execute([":aid"=>$decoded['data']->id, ":id"=>$request_id]);
  $db->prepare("INSERT IGNORE INTO class_teacher_assignments(class_id, teacher_id) VALUES (:cid,:tid)")->execute([":cid"=>$req['class_id'],":tid"=>$req['teacher_id']]);
  $db->prepare("UPDATE classes SET homeroom_teacher_id=:tid WHERE id=:cid")->execute([":tid"=>$req['teacher_id'],":cid"=>$req['class_id']]);
  $msg = "Yêu cầu quản lý lớp đã được phê duyệt";
} else {
  $db->prepare("UPDATE teacher_class_requests SET status='rejected', approved_at=NOW(), admin_id=:aid WHERE id=:id")->execute([":aid"=>$decoded['data']->id, ":id"=>$request_id]);
  $msg = "Yêu cầu quản lý lớp ".$req['class_id']." đã bị từ chối";
}
$notify = $db->prepare("INSERT INTO notifications(title, content, sender_id, target_role) VALUES (:t,:c,:s,'teacher')");
$notify->execute([":t"=>"Kết quả yêu cầu phân lớp",":c"=>$msg,":s"=>$decoded['data']->id]);
echo json_encode(["message"=>"OK"]);
