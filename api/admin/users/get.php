<?php
include_once '../../../config/database.php';
include_once '../../../config/jwt.php';

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");

$database = new Database();
$db = $database->getConnection();

$data = json_decode(file_get_contents("php://input"));
$token = isset($data->token) ? $data->token : (isset($_SERVER['HTTP_AUTHORIZATION']) ? str_replace('Bearer ', '', $_SERVER['HTTP_AUTHORIZATION']) : "");

$decoded = validateJWT($token);
if (!$decoded || $decoded['data']->role != 'admin') {
    http_response_code(401);
    echo json_encode(["message" => "Unauthorized access."]);
    exit;
}

if (empty($data->id) && empty($data->user_id)) {
    http_response_code(400);
    echo json_encode(["message" => "User ID required."]);
    exit;
}
$id = !empty($data->id) ? $data->id : $data->user_id;

$query = "SELECT id, username, full_name, email, phone, role FROM users WHERE id = :id LIMIT 0,1";
$stmt = $db->prepare($query);
$stmt->bindParam(":id", $id);
$stmt->execute();

$num = $stmt->rowCount();
if ($num > 0) {
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    echo json_encode($row);
} else {
    http_response_code(404);
    echo json_encode(["message" => "User not found."]);
}
?>
