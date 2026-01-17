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
$contact_id = isset($data->contact_id) ? $data->contact_id : 0;

$decoded = validateJwt($token);
if (!$decoded) {
    http_response_code(401);
    echo json_encode(["message" => "Truy cập bị từ chối."]);
    exit;
}

$user_id = $decoded['data']->id;

$query = "SELECT m.*, u.full_name as sender_name 
          FROM messages m 
          JOIN users u ON m.sender_id = u.id
          WHERE (sender_id = :user_id AND receiver_id = :contact_id) 
             OR (sender_id = :contact_id AND receiver_id = :user_id) 
          ORDER BY created_at ASC";

$stmt = $db->prepare($query);
$stmt->bindParam(":user_id", $user_id);
$stmt->bindParam(":contact_id", $contact_id);
$stmt->execute();

$messages = array();
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    array_push($messages, $row);
}

http_response_code(200);
echo json_encode($messages);
?>
