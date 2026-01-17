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
// Check if class already has an active Red Star
$replace = isset($body['replace']) ? (bool)$body['replace'] : false;
$durationWeeks = isset($body['duration_weeks']) ? intval($body['duration_weeks']) : 4;
$startDate = isset($body['start_date']) ? $body['start_date'] : date('Y-m-d');
$expiredAt = date('Y-m-d', strtotime($startDate . " + $durationWeeks weeks"));

$stmt = $db->prepare("SELECT u.full_name FROM red_committee_members m JOIN users u ON m.user_id = u.id WHERE m.class_id=:cid AND m.active=1");
$stmt->execute([':cid'=>$classId]);
$current = $stmt->fetch(PDO::FETCH_ASSOC);

if ($current) {
    if (!$replace) {
        http_response_code(409);
        echo json_encode([
            'message' => 'Lớp này đã có sao đỏ.',
            'current_user' => $current['full_name']
        ]);
        exit;
    } else {
        // Deactivate old
        $update = $db->prepare("UPDATE red_committee_members SET active=0, revoked_at=NOW() WHERE class_id=:cid AND active=1");
        $update->execute([':cid'=>$classId]);
        
        // Log the replacement (optional but good for tracking)
        $log = $db->prepare("INSERT INTO red_committee_logs(actor_id,action,target_user_id,class_id,area) VALUES(:aid,'replace',0,:cid,:area)");
        $log->execute([':aid'=>$u->id, ':cid'=>$classId, ':area'=>$area]);
    }
}
$h = committeeHash($userId,$classId,$area);
$stmt = $db->prepare("INSERT INTO red_committee_members(user_id,class_id,area,active,assigned_by,hash,duration_weeks,start_date,expired_at) VALUES(:uid,:cid,:area,1,:by,:hash,:dur,:start,:exp) ON DUPLICATE KEY UPDATE active=1, revoked_at=NULL, assigned_by=:by2, hash=:hash2, duration_weeks=:dur2, start_date=:start2, expired_at=:exp2");
$stmt->execute([
    ':uid'=>$userId, ':cid'=>$classId, ':area'=>$area, ':by'=>$u->id, ':hash'=>$h, 
    ':dur'=>$durationWeeks, ':start'=>$startDate, ':exp'=>$expiredAt,
    ':by2'=>$u->id, ':hash2'=>$h, ':dur2'=>$durationWeeks, ':start2'=>$startDate, ':exp2'=>$expiredAt
]);
$log = $db->prepare("INSERT INTO red_committee_logs(actor_id,action,target_user_id,class_id,area) VALUES(:aid,'add',:tuid,:cid,:area)");
$log->execute([':aid'=>$u->id, ':tuid'=>$userId, ':cid'=>$classId, ':area'=>$area]);
header('Content-Type: application/json');
echo json_encode(['status'=>'ok', 'expired_at' => $expiredAt]);
?>
