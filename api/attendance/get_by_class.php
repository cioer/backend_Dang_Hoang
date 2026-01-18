<?php
require_once __DIR__ . '/../bootstrap.php';

use App\Core\{Middleware, Response, Request, Bootstrap};

Middleware::cors('GET');

$user = Middleware::auth();

if ($user->role != 'teacher' && $user->role != 'admin') {
    Response::forbidden('Truy cập bị từ chối.');
}

$db = Bootstrap::db();

$class_id = $_GET['class_id'] ?? 0;
$date = $_GET['date'] ?? date('Y-m-d');

if ($class_id == 0) {
    Response::error('Thiếu class_id.', 400);
}

try {
    // Get students in class with their attendance status for the given date
    // Check both class_registrations and student_details tables for class membership
    $query = "SELECT
                u.id as student_id,
                u.full_name,
                u.username,
                COALESCE(a.status, 'not_recorded') as status,
                a.note,
                a.recorded_by,
                rb.full_name as recorded_by_name,
                a.created_at as recorded_at
              FROM (
                SELECT DISTINCT student_id FROM class_registrations WHERE class_id = :class_id
                UNION
                SELECT DISTINCT user_id as student_id FROM student_details WHERE class_id = :class_id2
              ) AS class_students
              JOIN users u ON u.id = class_students.student_id AND u.role = 'student'
              LEFT JOIN attendance a ON a.student_id = u.id AND a.class_id = :class_id3 AND a.date = :date
              LEFT JOIN users rb ON rb.id = a.recorded_by
              ORDER BY u.full_name";

    $stmt = $db->prepare($query);
    $stmt->bindParam(":class_id", $class_id);
    $stmt->bindParam(":class_id2", $class_id);
    $stmt->bindParam(":class_id3", $class_id);
    $stmt->bindParam(":date", $date);
    $stmt->execute();

    $students = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Get class info
    $classStmt = $db->prepare("SELECT id, name FROM classes WHERE id = :cid");
    $classStmt->execute([':cid' => $class_id]);
    $classInfo = $classStmt->fetch(PDO::FETCH_ASSOC);

    // Calculate summary
    $summary = [
        'total' => count($students),
        'present' => 0,
        'absent_excused' => 0,
        'absent_unexcused' => 0,
        'late' => 0,
        'not_recorded' => 0
    ];

    foreach ($students as $student) {
        $status = $student['status'];
        if (isset($summary[$status])) {
            $summary[$status]++;
        }
    }

    Response::success([
        "class" => $classInfo,
        "date" => $date,
        "students" => $students,
        "summary" => $summary
    ]);

} catch (Exception $e) {
    Response::error('Lỗi hệ thống: ' . $e->getMessage(), 503);
}
