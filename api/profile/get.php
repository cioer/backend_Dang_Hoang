<?php
include_once '../../config/database.php';
include_once '../../config/jwt.php';
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
$headers = getallheaders();
$token = isset($headers['Authorization']) ? str_replace('Bearer ', '', $headers['Authorization']) : null;
$decoded = validateJWT($token);
if (!$decoded) { http_response_code(401); echo json_encode(["message"=>"Unauthorized"]); exit; }
$db = (new Database())->getConnection();
$data = json_decode(file_get_contents("php://input"));
$target_id = isset($data->user_id) ? intval($data->user_id) : $decoded['data']->id;
if ($decoded['data']->role != 'admin' && $target_id !== $decoded['data']->id) { http_response_code(403); echo json_encode(["message"=>"Forbidden"]); exit; }
$stmt = $db->prepare("SELECT id, username, full_name, role, email, phone, avatar FROM users WHERE id=:id LIMIT 1");
$stmt->execute([':id'=>$target_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$user) { http_response_code(404); echo json_encode(["message"=>"Not found"]); exit; }

// Check Red Star status if student
if ($user['role'] == 'student') {
    $stmtRed = $db->prepare("SELECT COUNT(*) FROM red_committee_members WHERE user_id=:uid AND active=1");
    $stmtRed->execute([':uid'=>$user['id']]);
    $user['is_red_star'] = $stmtRed->fetchColumn() > 0 ? 1 : 0;
} else {
    $user['is_red_star'] = 0;
}

echo json_encode($user);
?>
