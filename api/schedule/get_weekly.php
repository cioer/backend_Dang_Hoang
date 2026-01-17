<?php
include_once '../../config/database.php';
include_once '../../config/jwt.php';

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

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

$query = "";
if ($role == 'student') {
    $query = "SELECT s.day_of_week, s.period, sub.name as subject_name, u.full_name as teacher_name 
              FROM schedule s 
              JOIN subjects sub ON s.subject_id = sub.id 
              JOIN users u ON s.teacher_id = u.id 
              JOIN student_details sd ON s.class_id = sd.class_id 
              WHERE sd.user_id = :user_id 
              ORDER BY s.day_of_week, s.period";
} else if ($role == 'teacher') {
    $query = "SELECT s.day_of_week, s.period, sub.name as subject_name, c.name as class_name 
              FROM schedule s 
              JOIN subjects sub ON s.subject_id = sub.id 
              JOIN classes c ON s.class_id = c.id 
              WHERE s.teacher_id = :user_id 
              ORDER BY s.day_of_week, s.period";
} else if ($role == 'parent') {
     // For parent, get schedule of the first child (simplified)
    $query = "SELECT s.day_of_week, s.period, sub.name as subject_name, u.full_name as teacher_name 
              FROM schedule s 
              JOIN subjects sub ON s.subject_id = sub.id 
              JOIN users u ON s.teacher_id = u.id 
              JOIN student_details sd ON s.class_id = sd.class_id 
              JOIN parent_student_links psl ON sd.user_id = psl.student_id
              WHERE psl.parent_id = :user_id 
              ORDER BY s.day_of_week, s.period";
} else {
    http_response_code(403);
    echo json_encode(["message" => "Không có quyền truy cập."]);
    exit;
}

$stmt = $db->prepare($query);
$stmt->bindParam(":user_id", $user_id);
$stmt->execute();

$schedule_list = array();
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    array_push($schedule_list, $row);
}

http_response_code(200);
echo json_encode($schedule_list);
?>