<?php
include_once '../../../config/database.php';
include_once '../../../config/jwt.php';
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
$headers = getallheaders();
$token = isset($headers['Authorization']) ? str_replace('Bearer ', '', $headers['Authorization']) : null;
$decoded = $token ? validateJWT($token) : null;
if (!$decoded || $decoded['data']->role!='admin') { http_response_code(403); echo json_encode(["message"=>"Forbidden"]); exit; }
$db = (new Database())->getConnection();
$q = $db->query("SELECT r.id, r.status, r.requested_at, u.full_name AS teacher_name, c.name AS class_name
                 FROM teacher_class_requests r
                 JOIN users u ON u.id=r.teacher_id
                 JOIN classes c ON c.id=r.class_id
                 ORDER BY r.requested_at DESC");
echo json_encode($q->fetchAll(PDO::FETCH_ASSOC));
