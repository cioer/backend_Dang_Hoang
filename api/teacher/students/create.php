<?php
include_once '../../../config/database.php';
include_once '../../../config/jwt.php';
include_once '../../../lib/UserRepository.php';

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");

$database = new Database();
$db = $database->getConnection();
$repo = new UserRepository($db);

$data = json_decode(file_get_contents("php://input"));
$token = isset($data->token) ? $data->token : (isset($_SERVER['HTTP_AUTHORIZATION']) ? str_replace('Bearer ', '', $_SERVER['HTTP_AUTHORIZATION']) : "");

$decoded = validateJWT($token);
if (!$decoded || $decoded['data']->role != 'teacher') {
    http_response_code(401);
    echo json_encode(["message" => "Unauthorized."]);
    exit;
}

if (empty($data->full_name) || empty($data->username) || empty($data->password) || empty($data->class_id)) {
    http_response_code(400);
    echo json_encode(["message" => "Missing data."]);
    exit;
}

// Check if teacher owns the class
$check = $db->prepare("SELECT id FROM classes WHERE id = ? AND teacher_id = ?");
$check->execute([$data->class_id, $decoded['data']->id]);
if ($check->rowCount() == 0) {
    http_response_code(403);
    echo json_encode(["message" => "You are not assigned to this class."]);
    exit;
}

// Create User
if ($repo->existsUsername($data->username)) {
    http_response_code(409);
    echo json_encode(["message" => "Username already exists."]);
    exit;
}

try {
    $db->beginTransaction();

    // 1. Create User
    $userId = $repo->createUserReturnId($data->username, $data->password, $data->full_name, 'student', null, null);
    if (!$userId) {
        throw new Exception("Failed to create user.");
    }

    // 2. Assign to Class (using class_registrations)
    $stmt = $db->prepare("INSERT INTO class_registrations (class_id, student_id) VALUES (?, ?)");
    $stmt->execute([$data->class_id, $userId]);

    // 3. Update Profile (using student_profiles)
    if (!empty($data->parent_name) || !empty($data->parent_phone)) {
        $stmt = $db->prepare("INSERT INTO student_profiles (user_id, full_name, guardian_name, guardian_phone) VALUES (?, ?, ?, ?) ON DUPLICATE KEY UPDATE guardian_name = VALUES(guardian_name), guardian_phone = VALUES(guardian_phone)");
        $stmt->execute([
            $userId, 
            $data->full_name, 
            isset($data->parent_name)?$data->parent_name:null, 
            isset($data->parent_phone)?$data->parent_phone:null
        ]);
    }

    $db->commit();
    echo json_encode(["message" => "Student created successfully.", "id" => $userId]);

} catch (Exception $e) {
    $db->rollBack();
    http_response_code(500);
    echo json_encode(["message" => "Error: " . $e->getMessage()]);
}
?>