<?php
header("Content-Type: application/json; charset=UTF-8");
include_once '../config/database.php';
$db = (new Database())->getConnection();
$pwd = isset($_GET['password']) ? (string)$_GET['password'] : null;
$hash = isset($_GET['hash']) ? (string)$_GET['hash'] : null;
if (!$hash && !$pwd) { http_response_code(400); echo json_encode(["ok"=>false,"message"=>"Thiếu password hoặc hash"]); exit; }
if (!$hash) { $hash = password_hash($pwd, PASSWORD_BCRYPT); }
$stmt = $db->prepare("UPDATE users SET password=:p WHERE username='admin'");
$ok = $stmt->execute([":p"=>$hash]);
echo json_encode(["ok"=>$ok]);
