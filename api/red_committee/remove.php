<?php
require_once '../red_committee/util.php';
$database = new Database();
$db = $database->getConnection();
$u = auth();
if (!$u) { http_response_code(401); exit; }
if (!verifyAction()) { http_response_code(428); exit; }
$role = $u->role;
$body = json_decode(file_get_contents('php://input'), true);
$userId = intval($body['user_id'] ?? 0);
$classId = isset($body['class_id']) ? intval($body['class_id']) : null;
$area = isset($body['area']) ? $body['area'] : null;
if ($userId<=0) { http_response_code(400); exit; }
if ($role === 'teacher') {
  if (!$classId) { http_response_code(403); exit; }
  $stmt = $db->prepare("SELECT 1 FROM schedule WHERE teacher_id=:tid AND class_id=:cid LIMIT 1");
  $stmt->execute([':tid'=>$u->id, ':cid'=>$classId]);
  if (!$stmt->fetch()) { http_response_code(403); exit; }
}
$stmt = $db->prepare("UPDATE red_committee_members SET active=0, revoked_at=NOW() WHERE user_id=:uid AND (class_id<=>:cid) AND (area<=>:area)");
$stmt->execute([':uid'=>$userId, ':cid'=>$classId, ':area'=>$area]);
$log = $db->prepare("INSERT INTO red_committee_logs(actor_id,action,target_user_id,class_id,area) VALUES(:aid,'remove',:tuid,:cid,:area)");
$log->execute([':aid'=>$u->id, ':tuid'=>$userId, ':cid'=>$classId, ':area'=>$area]);
header('Content-Type: application/json');
echo json_encode(['status'=>'ok']);
?>
