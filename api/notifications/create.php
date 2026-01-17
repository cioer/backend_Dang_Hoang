<?php
include_once '../../config/database.php';
include_once '../../config/jwt.php';

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");

$database = new Database();
$db = $database->getConnection();

$data = json_decode(file_get_contents("php://input"));
$token = isset($data->token) ? $data->token : "";
$title = isset($data->title) ? $data->title : "";
$content = isset($data->content) ? $data->content : "";

$decoded = validateJwt($token);
if (!$decoded || ($decoded['data']->role != 'teacher' && $decoded['data']->role != 'admin')) {
    http_response_code(401);
    echo json_encode(["message" => "Truy cập bị từ chối."]);
    exit;
}

if (empty($title) || empty($content)) {
    http_response_code(400);
    echo json_encode(["message" => "Tiêu đề và nội dung không được để trống."]);
    exit;
}

$query = "INSERT INTO notifications (title, content, sender_id, target_role) 
          VALUES (:title, :content, :sender_id, :target_role)";
$stmt = $db->prepare($query);
$stmt->bindParam(":title", $title);
$stmt->bindParam(":content", $content);
$stmt->bindParam(":sender_id", $decoded['data']->id);
$target_role = "all";
$stmt->bindParam(":target_role", $target_role);

if ($stmt->execute()) {
    http_response_code(200);
    echo json_encode(["message" => "Tạo thông báo thành công."]);
} else {
    http_response_code(503);
    echo json_encode(["message" => "Lỗi hệ thống."]);
}
?>
