<?php
// Validate and auto-fix violations data integrity
include_once '../../config/database.php';
include_once '../../config/jwt.php';
header("Content-Type: application/json");
$headers = getallheaders();
$auth = $headers['Authorization'] ?? $headers['authorization'] ?? null;
if (!$auth || stripos($auth, 'Bearer ') !== 0) { http_response_code(401); echo json_encode(['error'=>'Unauthorized']); exit; }
$token = substr($auth, 7);
$decoded = validateJwt($token);
if (!$decoded || !in_array($decoded['data']->role, ['admin'])) { http_response_code(403); echo json_encode(['error'=>'Forbidden']); exit; }
$db = (new Database())->getConnection();
$invalid = [];
// Remove violations with missing rule
$stmt = $db->query("SELECT v.id FROM violations v LEFT JOIN conduct_rules r ON v.rule_id=r.id WHERE r.id IS NULL");
$toDelete = $stmt->fetchAll(PDO::FETCH_COLUMN);
if (!empty($toDelete)) {
  $in = implode(',', array_map('intval',$toDelete));
  $db->exec("DELETE FROM violations WHERE id IN ($in)");
}
// Ensure student_id references existing users(role=student)
$stmt2 = $db->query("SELECT v.id FROM violations v LEFT JOIN users u ON v.student_id=u.id WHERE u.id IS NULL OR u.role<>'student'");
$badStudents = $stmt2->fetchAll(PDO::FETCH_COLUMN);
$deletedStudents = 0;
if (!empty($badStudents)) {
  $in = implode(',', array_map('intval',$badStudents));
  $db->exec("DELETE FROM violations WHERE id IN ($in)");
  $deletedStudents = count($badStudents);
}
echo json_encode(['status'=>'ok','deleted_rules'=>count($toDelete),'deleted_students'=>$deletedStudents]);
?>
