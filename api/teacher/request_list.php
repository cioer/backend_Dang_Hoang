<?php
include_once '../../config/database.php';
include_once '../../config/jwt.php';
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
$headers = getallheaders();
$token = isset($headers['Authorization']) ? str_replace('Bearer ', '', $headers['Authorization']) : null;
$decoded = $token ? validateJWT($token) : null;
if (!$decoded || $decoded['data']->role!='teacher') { http_response_code(403); echo json_encode(["message"=>"Forbidden"]); exit; }
$tid = $decoded['data']->id;
$db = (new Database())->getConnection();
$q = $db->prepare("SELECT r.id, r.class_id, c.name AS class_name, r.status, r.requested_at, r.approved_at FROM teacher_class_requests r JOIN classes c ON c.id=r.class_id WHERE r.teacher_id=:tid ORDER BY r.requested_at DESC");
$q->execute([":tid"=>$tid]);
echo json_encode($q->fetchAll(PDO::FETCH_ASSOC));
