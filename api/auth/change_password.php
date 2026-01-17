<?php
include_once '../../config/database.php';
include_once '../../config/jwt.php';
include_once '../../lib/UserRepository.php';

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");

$database = new Database();
$db = $database->getConnection();
$repo = new UserRepository($db);

$data = json_decode(file_get_contents("php://input"));
$token = isset($data->token) ? $data->token : "";
$authHeader = isset($_SERVER['HTTP_AUTHORIZATION']) ? $_SERVER['HTTP_AUTHORIZATION'] : null;
if (!$token && $authHeader && stripos($authHeader, 'Bearer ') === 0) {
    $token = substr($authHeader, 7);
}
$old_password = isset($data->old_password) ? $data->old_password : "";
$new_password = isset($data->new_password) ? $data->new_password : "";

$decoded = validateJwt($token);
if (!$decoded) {
    http_response_code(401);
    echo json_encode(["message" => "Truy cập bị từ chối."]);
    exit;
}

$user_id = $decoded['data']->id;

// Verify old password
$stmt = $db->prepare("SELECT password FROM users WHERE id = :id LIMIT 1");
$stmt->bindParam(":id", $user_id);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user || !$repo->verifyPassword($user['password'], $old_password)) {
    http_response_code(400);
    echo json_encode(["message" => "Mật khẩu cũ không đúng."]);
    exit;
}

// Update password
if ($repo->updatePassword(intval($user_id), $new_password)) {
    http_response_code(200);
    echo json_encode(["message" => "Đổi mật khẩu thành công."]);
} else {
    http_response_code(503);
    echo json_encode(["message" => "Không thể cập nhật mật khẩu."]);
}
?>
