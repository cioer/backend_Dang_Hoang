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
if (!$decoded || $decoded['data']->role != 'admin') { http_response_code(403); echo json_encode(["message"=>"Forbidden"]); exit; }
$db = (new Database())->getConnection();
$tables = ['conduct_rules','violations','discipline_points','student_details','classes','subjects','system_settings'];
$backup = [];
foreach ($tables as $t) {
  $stmt = $db->query("SELECT * FROM ".$t);
  $backup[$t] = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
echo json_encode($backup, JSON_UNESCAPED_UNICODE);
