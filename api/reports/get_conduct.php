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

$decoded = validateJwt($token);
if (!$decoded) {
    http_response_code(401);
    echo json_encode(["message" => "Truy cập bị từ chối."]);
    exit;
}

$user_id = $decoded['data']->id;
$role = $decoded['data']->role;
$student_id = $user_id;

// If parent, get child's id (simplified)
if ($role == 'parent') {
    // Logic to get child id
    $query = "SELECT student_id FROM parent_student_links WHERE parent_id = :parent_id LIMIT 1";
    $stmt = $db->prepare($query);
    $stmt->bindParam(":parent_id", $user_id);
    $stmt->execute();
    if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $student_id = $row['student_id'];
    }
}

$query = "SELECT type, content, date FROM conduct WHERE student_id = :student_id ORDER BY date DESC";

$stmt = $db->prepare($query);
$stmt->bindParam(":student_id", $student_id);
$stmt->execute();

$conducts = array();
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    array_push($conducts, $row);
}

http_response_code(200);
echo json_encode($conducts);
?>
