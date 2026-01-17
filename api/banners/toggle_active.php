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

if (!$decoded || $decoded['data']->role != 'admin') {
    http_response_code(401);
    echo json_encode(["message" => "Unauthorized"]);
    exit;
}

$data = json_decode(file_get_contents("php://input"));

if (!empty($data->id) && isset($data->is_active)) {
    $database = new Database();
    $db = $database->getConnection();

    $query = "UPDATE banners SET is_active = :is_active WHERE id = :id";
    $stmt = $db->prepare($query);

    $is_active = $data->is_active ? 1 : 0;
    $stmt->bindParam(":is_active", $is_active);
    $stmt->bindParam(":id", $data->id);

    if ($stmt->execute()) {
        http_response_code(200);
        echo json_encode(["message" => "Banner status updated."]);
    } else {
        http_response_code(503);
        echo json_encode(["message" => "Unable to update banner status."]);
    }
} else {
    http_response_code(400);
    echo json_encode(["message" => "Incomplete data."]);
}
?>
