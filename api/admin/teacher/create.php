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
$username = $body['username'] ?? null;
$full_name = $body['full_name'] ?? null;
$email = $body['email'] ?? null;
$subjects = $body['subjects'] ?? [];
$homeroom_class = $body['homeroom_class'] ?? null;
if (!$username || !$full_name) { http_response_code(400); echo json_encode(["message"=>"Missing fields"]); exit; }
$password = password_hash('password', PASSWORD_BCRYPT);
$stmt = $db->prepare("INSERT INTO users(username,password,full_name,role,email,phone_verified,password_must_change) VALUES (:u,:p,:f,'teacher',:e,0,1)");
try { $stmt->execute([":u"=>$username,":p"=>$password,":f"=>$full_name,":e"=>$email]); } catch (Exception $e) { http_response_code(409); echo json_encode(["message"=>"Username exists"]); exit; }
$tid = $db->lastInsertId();
if ($homeroom_class) {
  $stc = $db->prepare("UPDATE classes SET homeroom_teacher_id=:tid WHERE name=:name");
  $stc->execute([":tid"=>$tid,":name"=>$homeroom_class]);
}
foreach ($subjects as $s) {
  $ss = $db->prepare("INSERT INTO teacher_subjects(teacher_id,subject_id) SELECT :t,id FROM subjects WHERE name=:n");
  $ss->execute([":t"=>$tid,":n"=>$s]);
}
echo json_encode(["message"=>"OK","teacher_id"=>$tid]);
