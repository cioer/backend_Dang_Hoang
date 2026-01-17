<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET");
include_once '../../config/database.php';
$database = new Database();
$db = $database->getConnection();
$username = isset($_GET['username']) ? trim($_GET['username']) : null;
if (!$username) { http_response_code(400); echo json_encode(["message"=>"Thiếu username"]); exit; }
$userStmt = $db->prepare("SELECT id,username,role,is_locked FROM users WHERE username=:u LIMIT 1");
$userStmt->execute([":u"=>$username]);
if ($userStmt->rowCount()===0) { http_response_code(404); echo json_encode(["message"=>"Không tìm thấy tài khoản"]); exit; }
$user = $userStmt->fetch(PDO::FETCH_ASSOC);
$failsStmt = $db->prepare("SELECT COUNT(*) c FROM login_attempts WHERE username=:u AND success=0 AND created_at> (NOW() - INTERVAL 15 MINUTE)");
$failsStmt->execute([":u"=>$username]);
$fails = intval($failsStmt->fetch(PDO::FETCH_ASSOC)['c']);
$captcha_required = $fails >= 3;
echo json_encode([
  "username"=>$user['username'],
  "role"=>$user['role'],
  "is_locked"=>intval($user['is_locked'])===1,
  "recent_fail_count"=>$fails,
  "captcha_required"=>$captcha_required
]);
