<?php
include_once '../../config/database.php';
include_once '../../config/jwt.php';
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
$headers = getallheaders();
$token = isset($headers['Authorization']) ? str_replace('Bearer ', '', $headers['Authorization']) : null;
if (!$token || !validateJWT($token)) { http_response_code(401); echo json_encode(["message"=>"Unauthorized"]); exit; }
$db = (new Database())->getConnection();
$body = json_decode(file_get_contents('php://input'), true);
$sid = isset($body['student_id']) ? (int)$body['student_id'] : null;
if (!$sid) { http_response_code(400); echo json_encode(["message"=>"Missing student_id"]); exit; }
$db->exec("INSERT IGNORE INTO discipline_points(student_id, points) VALUES ($sid, 100)");
$stmt = $db->prepare("SELECT points FROM discipline_points WHERE student_id=:sid");
$stmt->bindParam(":sid", $sid);
$stmt->execute();
$p = $stmt->fetch(PDO::FETCH_ASSOC);
$points = $p ? (int)$p['points'] : 100;
$th = [];
foreach (['discipline_threshold_warn','discipline_threshold_conduct','discipline_threshold_class_change'] as $k) {
  $st = $db->prepare("SELECT setting_value FROM system_settings WHERE setting_key=:k");
  $st->bindParam(":k", $k);
  $st->execute();
  $v = $st->fetch(PDO::FETCH_ASSOC);
  $th[$k] = $v ? (int)$v['setting_value'] : null;
}
echo json_encode(["points"=>$points,"thresholds"=>$th]);
