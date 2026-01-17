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

$user_role = $decoded['data']->role;
$user_id = $decoded['data']->id;

// Check if student is red star
$is_red_star = false;
if ($user_role == 'red_star') {
    $is_red_star = true;
} else if ($user_role == 'student') {
    $stmt = $db->prepare("SELECT COUNT(*) FROM red_committee_members WHERE user_id = :uid AND active = 1");
    $stmt->execute([':uid' => $user_id]);
    if ($stmt->fetchColumn() > 0) {
        $is_red_star = true;
    }
}

if ($user_role != 'teacher' && $user_role != 'admin' && !$is_red_star) {
    http_response_code(401);
    echo json_encode(["message" => "Truy cập bị từ chối."]);
    exit;
}

if ($user_role == 'admin' || $is_red_star) {
    // Admin and Red Star sees all classes
    $query = "SELECT id, name FROM classes ORDER BY name";
    $stmt = $db->prepare($query);
} else {
    // Teacher sees assigned classes
    $teacher_id = $user_id;
    $query = "SELECT DISTINCT c.id, c.name 
              FROM classes c 
              LEFT JOIN schedule s ON c.id = s.class_id 
              LEFT JOIN class_teacher_assignments cta ON c.id = cta.class_id 
              WHERE s.teacher_id = :tid1 
                 OR cta.teacher_id = :tid2 
                 OR c.homeroom_teacher_id = :tid3
              ORDER BY c.name";
    $stmt = $db->prepare($query);
    $stmt->bindParam(":tid1", $teacher_id);
    $stmt->bindParam(":tid2", $teacher_id);
    $stmt->bindParam(":tid3", $teacher_id);
}

$stmt->execute();

$classes = array();
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    array_push($classes, $row);
}

http_response_code(200);
echo json_encode($classes);
?>
