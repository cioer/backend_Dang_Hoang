<?php
include_once '../../config/database.php';
include_once '../../config/jwt.php';
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
$headers = getallheaders();
$token = isset($headers['Authorization']) ? str_replace('Bearer ', '', $headers['Authorization']) : null;
$decoded = $token ? validateJWT($token) : null;
if (!$decoded) { http_response_code(401); echo json_encode(["complete"=>false]); exit; }
$db = (new Database())->getConnection();
$uid = $decoded['data']->id;
$role = $decoded['data']->role;
if ($role === 'parent') {
  $stmt = $db->prepare("SELECT sp.is_complete FROM parent_student_links psl JOIN student_profiles sp ON sp.user_id=psl.student_id WHERE psl.parent_id=:pid LIMIT 1");
  $stmt->execute([":pid"=>$uid]);
} else {
  $stmt = $db->prepare("SELECT is_complete FROM student_profiles WHERE user_id=:uid");
  $stmt->execute([":uid"=>$uid]);
}
$row = $stmt->fetch(PDO::FETCH_ASSOC);
echo json_encode(["complete"=> $row ? (bool)$row['is_complete'] : false]);
