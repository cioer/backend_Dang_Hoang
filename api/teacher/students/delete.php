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
if (!$decoded || $decoded['data']->role != 'teacher') {
    http_response_code(401);
    echo json_encode(["message" => "Unauthorized."]);
    exit;
}

if (empty($data->id) || empty($data->class_id)) {
    http_response_code(400);
    echo json_encode(["message" => "Missing data."]);
    exit;
}

// Check ownership
$checkClass = $db->prepare("SELECT id FROM classes WHERE id = ? AND teacher_id = ?");
$checkClass->execute([$data->class_id, $decoded['data']->id]);
if ($checkClass->rowCount() == 0) {
    http_response_code(403);
    echo json_encode(["message" => "You are not assigned to this class."]);
    exit;
}

$checkStudent = $db->prepare("SELECT id FROM class_registrations WHERE class_id = ? AND student_id = ?");
$checkStudent->execute([$data->class_id, $data->id]);
if ($checkStudent->rowCount() == 0) {
    http_response_code(404);
    echo json_encode(["message" => "Student not found in this class."]);
    exit;
}

try {
    $db->beginTransaction();

    // Remove from class_registrations only
    $stmt = $db->prepare("DELETE FROM class_registrations WHERE class_id = ? AND student_id = ?");
    $stmt->execute([$data->class_id, $data->id]);

    // Soft lock user instead of hard delete
    $stmt = $db->prepare("UPDATE users SET is_locked=1 WHERE id = ?");
    $stmt->execute([$data->id]);

    $db->commit();
    echo json_encode(["message" => "Student deleted successfully."]);

} catch (Exception $e) {
    $db->rollBack();
    http_response_code(500);
    echo json_encode(["message" => "Error: " . $e->getMessage()]);
}
?>
