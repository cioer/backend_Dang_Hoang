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

$contacts = array();

if ($role == 'parent') {
    // Parent sees teachers of their child
    $query = "SELECT DISTINCT u.id, u.full_name, 'teacher' as role 
              FROM parent_student_links psl
              JOIN student_details sd ON psl.student_id = sd.user_id
              JOIN schedule s ON sd.class_id = s.class_id
              JOIN users u ON s.teacher_id = u.id
              WHERE psl.parent_id = :user_id";
    
    $stmt = $db->prepare($query);
    $stmt->bindParam(":user_id", $user_id);
    $stmt->execute();
    
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        array_push($contacts, $row);
    }
} else if ($role == 'teacher') {
    // Teacher sees parents of students in their classes
    // Simplified: Show parents linked to students in classes taught by teacher
    $query = "SELECT DISTINCT u.id, u.full_name, 'parent' as role 
              FROM schedule s
              JOIN student_details sd ON s.class_id = sd.class_id
              JOIN parent_student_links psl ON sd.user_id = psl.student_id
              JOIN users u ON psl.parent_id = u.id
              WHERE s.teacher_id = :user_id";

    $stmt = $db->prepare($query);
    $stmt->bindParam(":user_id", $user_id);
    $stmt->execute();
    
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        array_push($contacts, $row);
    }
} else if ($role == 'admin') {
    // Admin sees anyone who has messaged them (e.g., teacher requests)
    $query = "SELECT DISTINCT u.id, u.full_name, u.role 
              FROM messages m
              JOIN users u ON u.id = m.sender_id
              WHERE m.receiver_id = :user_id
              ORDER BY u.full_name ASC";
    $stmt = $db->prepare($query);
    $stmt->bindParam(":user_id", $user_id);
    $stmt->execute();
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        array_push($contacts, $row);
    }
}

http_response_code(200);
echo json_encode($contacts);
?>
