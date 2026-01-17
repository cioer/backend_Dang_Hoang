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
if (!$decoded || $decoded['data']->role != 'admin') { http_response_code(403); echo json_encode(["message"=>"Forbidden"]); exit; }
$db = (new Database())->getConnection();
$body = json_decode(file_get_contents('php://input'), true);
$class_name = $body['class_name'] ?? null;
$subject_name = $body['subject_name'] ?? null;
$weekday = $body['weekday'] ?? null;
$start_time = $body['start_time'] ?? null;
$end_time = $body['end_time'] ?? null;
if (!$class_name || !$subject_name || !$weekday || !$start_time || !$end_time) { http_response_code(400); echo json_encode(["message"=>"Missing fields"]); exit; }
$cid = $db->prepare("SELECT id FROM classes WHERE name=:n"); $cid->execute([":n"=>$class_name]); $c = $cid->fetch(PDO::FETCH_ASSOC);
$sid = $db->prepare("SELECT id FROM subjects WHERE name=:n"); $sid->execute([":n"=>$subject_name]); $s = $sid->fetch(PDO::FETCH_ASSOC);
if (!$c || !$s) { http_response_code(404); echo json_encode(["message"=>"Class or subject not found"]); exit; }
$teacher_id = $decoded['data']->id;
$ins = $db->prepare("INSERT INTO schedule(class_id, subject_id, teacher_id, day_of_week, period, semester) VALUES (:c,:s,:t,:w,:p,:sem)");
$ins->execute([":c"=>$c['id'],":s"=>$s['id'],":t"=>$teacher_id,":w"=>$weekday,":p"=>1,":sem"=>"HK1-2025"]);
echo json_encode(["message"=>"OK"]);
