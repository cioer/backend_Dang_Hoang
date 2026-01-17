<?php
require_once '../red_committee/util.php';
$database = new Database();
$db = $database->getConnection();
$u = auth();
if (!$u) { http_response_code(401); exit; }
$role = $u->role;
$classId = isset($_GET['class_id']) ? intval($_GET['class_id']) : null;
if ($role === 'teacher' && $classId) {
  $stmt = $db->prepare("SELECT 1 FROM schedule WHERE teacher_id=:tid AND class_id=:cid LIMIT 1");
  $stmt->execute([':tid'=>$u->id, ':cid'=>$classId]);
  if (!$stmt->fetch()) { http_response_code(403); exit; }
}
$params = [];
$sql = "SELECT l.id, l.action, l.target_user_id, l.class_id, l.area, l.created_at, a.full_name AS actor_name, u.full_name AS target_name FROM red_committee_logs l JOIN users a ON l.actor_id=a.id JOIN users u ON l.target_user_id=u.id WHERE 1=1";
if ($classId) { $sql .= " AND l.class_id=:cid"; $params[':cid']=$classId; }
$sql .= " ORDER BY l.created_at DESC LIMIT 200";
$stmt = $db->prepare($sql);
$stmt->execute($params);
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
header('Content-Type: application/json');
echo json_encode($rows);
?>
