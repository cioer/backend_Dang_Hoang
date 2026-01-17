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
$class_id = isset($data->class_id) ? $data->class_id : 0;

$decoded = validateJWT($token);
if (!$decoded || ($decoded['data']->role != 'teacher' && $decoded['data']->role != 'admin')) {
    http_response_code(401);
    echo json_encode(["message" => "Truy cập bị từ chối."]);
    exit;
}

if ($class_id == 0) {
    http_response_code(400);
    echo json_encode(["message" => "Thiếu class_id."]);
    exit;
}

// Get students in the class
$query = "SELECT u.id, u.full_name, u.username AS code, u.avatar
          FROM class_registrations cr
          JOIN users u ON cr.student_id = u.id
          WHERE cr.class_id = :class_id 
          ORDER BY u.full_name";

$stmt = $db->prepare($query);
$stmt->bindParam(":class_id", $class_id);
$stmt->execute();

$students = array();
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    array_push($students, $row);
}

http_response_code(200);
echo json_encode($students);
?>
