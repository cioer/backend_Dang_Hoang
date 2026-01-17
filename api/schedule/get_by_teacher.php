<?php
include_once '../../config/database.php';
include_once '../../config/jwt.php';
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
$headers = getallheaders();
$token = isset($headers['Authorization']) ? str_replace('Bearer ', '', $headers['Authorization']) : null;
$decoded = $token ? validateJWT($token) : null;
if (!$decoded) { http_response_code(401); echo json_encode(["message"=>"Unauthorized"]); exit; }
$teacherId = $decoded['data']->id;
$db = (new Database())->getConnection();
$stmt = $db->prepare("SELECT s.id, c.name AS class, sub.name AS subject, s.day_of_week, s.period, s.start_time, s.end_time
FROM schedule s
JOIN classes c ON c.id=s.class_id
JOIN subjects sub ON sub.id=s.subject_id
WHERE s.teacher_id=:tid
ORDER BY s.day_of_week, s.period");
$stmt->execute([":tid"=>$teacherId]);
echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
