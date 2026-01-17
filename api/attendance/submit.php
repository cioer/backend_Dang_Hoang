<?php
require_once __DIR__ . '/../bootstrap.php';

use App\Core\{Middleware, Response, Request, Bootstrap};

Middleware::cors('POST');

$user = Middleware::auth();

if ($user->role != 'teacher' && $user->role != 'admin') {
    Response::forbidden('Truy cập bị từ chối.');
}

$db = Bootstrap::db();
$data = Request::all();

$class_id = $data['class_id'] ?? 0;
$date = $data['date'] ?? date('Y-m-d');
$attendance_list = $data['attendance_list'] ?? [];
$teacher_id = $user->id;

if ($class_id == 0 || empty($attendance_list)) {
    Response::error('Dữ liệu không hợp lệ.', 400);
}

try {
    $db->beginTransaction();

    $query = "INSERT INTO attendance (class_id, student_id, date, status, note, recorded_by)
              VALUES (:class_id, :student_id, :date, :status, :note, :recorded_by)
              ON DUPLICATE KEY UPDATE status = :status_update, note = :note_update, recorded_by = :recorded_by_update";

    $stmt = $db->prepare($query);

    foreach ($attendance_list as $item) {
        $item = (object)$item;
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
    Response::success(["message" => "Lưu điểm danh thành công."]);

} catch (Exception $e) {
    $db->rollBack();
    Response::error('Lỗi hệ thống: ' . $e->getMessage(), 503);
}
