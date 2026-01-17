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
$date = isset($data->date) ? $data->date : date('Y-m-d');
$attendance_list = isset($data->attendance_list) ? $data->attendance_list : [];

$decoded = validateJwt($token);
if (!$decoded || ($decoded['data']->role != 'teacher' && $decoded['data']->role != 'admin')) {
    http_response_code(401);
    echo json_encode(["message" => "Truy cập bị từ chối."]);
    exit;
}

$teacher_id = $decoded['data']->id;

if ($class_id == 0 || empty($attendance_list)) {
    http_response_code(400);
    echo json_encode(["message" => "Dữ liệu không hợp lệ."]);
    exit;
}

try {
    $db->beginTransaction();

    $query = "INSERT INTO attendance (class_id, student_id, date, status, note, recorded_by) 
              VALUES (:class_id, :student_id, :date, :status, :note, :recorded_by)
              ON DUPLICATE KEY UPDATE status = :status_update, note = :note_update, recorded_by = :recorded_by_update";
    
    $stmt = $db->prepare($query);

    foreach ($attendance_list as $item) {
        $stmt->bindParam(":class_id", $class_id);
        $stmt->bindParam(":student_id", $item->student_id);
        $stmt->bindParam(":date", $date);
        $stmt->bindParam(":status", $item->status);
        $stmt->bindParam(":note", $item->note);
        $stmt->bindParam(":recorded_by", $teacher_id);
        
        // For update
        $stmt->bindParam(":status_update", $item->status);
        $stmt->bindParam(":note_update", $item->note);
        $stmt->bindParam(":recorded_by_update", $teacher_id);
        
        $stmt->execute();
    }

    $db->commit();
    http_response_code(200);
    echo json_encode(["message" => "Lưu điểm danh thành công."]);

} catch (Exception $e) {
    $db->rollBack();
    http_response_code(503);
    echo json_encode(["message" => "Lỗi hệ thống: " . $e->getMessage()]);
}
?>
