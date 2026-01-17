<?php
include_once '../../config/database.php';
include_once '../../config/jwt.php';

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$headers = getallheaders();
$headerToken = isset($headers['Authorization']) ? str_replace('Bearer ', '', $headers['Authorization']) : null;
$token = $headerToken ?: (isset($_POST['token']) ? $_POST['token'] : null);

if (!$token) {
    http_response_code(401);
    echo json_encode(["message" => "Unauthorized"]);
    exit;
}

$decoded = validateJWT($token);
if (!$decoded || $decoded['data']->role != 'admin') {
    http_response_code(403);
    echo json_encode(["message" => "Forbidden"]);
    exit;
}

$data = json_decode(file_get_contents("php://input"));

if (!empty($data->filename) && !empty($data->file_data)) {
    $database = new Database();
    $db = $database->getConnection();

    try {
        $db->beginTransaction();

        $query = "INSERT INTO image_archives (filename, file_size, mime_type, file_data) VALUES (:filename, :file_size, :mime_type, :file_data)";
        $stmt = $db->prepare($query);

        $stmt->bindParam(":filename", $data->filename);
        $stmt->bindParam(":file_size", $data->file_size);
        $stmt->bindParam(":mime_type", $data->mime_type);
        $stmt->bindParam(":file_data", $data->file_data);

        if ($stmt->execute()) {
            $db->commit();
            http_response_code(201);
            echo json_encode(["message" => "Image imported successfully."]);
        } else {
            $db->rollBack();
            http_response_code(503);
            echo json_encode(["message" => "Unable to import image."]);
        }
    } catch (Exception $e) {
        $db->rollBack();
        http_response_code(500);
        echo json_encode(["message" => "Error: " . $e->getMessage()]);
    }
} else {
    http_response_code(400);
    echo json_encode(["message" => "Incomplete data."]);
}
?>
