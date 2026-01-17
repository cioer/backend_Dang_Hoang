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
$receiver_id = isset($data->receiver_id) ? $data->receiver_id : 0;
$content = isset($data->content) ? $data->content : "";

$decoded = validateJwt($token);
if (!$decoded) {
    http_response_code(401);
    echo json_encode(["message" => "Truy cập bị từ chối."]);
    exit;
}

if (empty($content) || $receiver_id == 0) {
    http_response_code(400);
    echo json_encode(["message" => "Dữ liệu không hợp lệ."]);
    exit;
}

$sender_id = $decoded['data']->id;

$query = "INSERT INTO messages (sender_id, receiver_id, content) VALUES (:sender_id, :receiver_id, :content)";
$stmt = $db->prepare($query);
$stmt->bindParam(":sender_id", $sender_id);
$stmt->bindParam(":receiver_id", $receiver_id);
$stmt->bindParam(":content", $content);

if ($stmt->execute()) {
    http_response_code(200);
    echo json_encode(["message" => "Gửi tin nhắn thành công."]);
} else {
    http_response_code(503);
    echo json_encode(["message" => "Lỗi hệ thống."]);
}
?>
