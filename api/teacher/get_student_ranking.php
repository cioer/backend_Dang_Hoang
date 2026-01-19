<?php
require_once __DIR__ . '/../bootstrap.php';

use App\Core\{Middleware, Response, Bootstrap};

Middleware::cors('GET');
$user = Middleware::auth();
$db = Bootstrap::db();

if ($user->role !== 'teacher' && $user->role !== 'admin') {
    Response::error('Chỉ giáo viên mới có quyền xem.', 403);
}

// Lấy tham số class_id (nếu giáo viên dạy nhiều lớp, họ phải chọn lớp)
$class_id = isset($_GET['class_id']) ? intval($_GET['class_id']) : 0;
$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : null;
$end_date = isset($_GET['end_date']) ? $_GET['end_date'] : null;

// Nếu không truyền class_id, thử lấy lớp chủ nhiệm
if ($class_id == 0) {
    $stmt = $db->prepare("SELECT id FROM classes WHERE homeroom_teacher_id = :tid LIMIT 1");
    $stmt->execute([':tid' => $user->id]);
    $class_id = $stmt->fetchColumn();
}

if (!$class_id) {
    Response::error('Không tìm thấy lớp học quản lý.', 404);
}

// Validate quyền truy cập lớp (nếu là GV)
if ($user->role === 'teacher') {
    $stmt = $db->prepare("SELECT COUNT(*) FROM classes c 
                          LEFT JOIN class_teacher_assignments cta ON c.id = cta.class_id
                          LEFT JOIN schedule s ON c.id = s.class_id
                          WHERE c.id = :cid AND (c.homeroom_teacher_id = :tid OR cta.teacher_id = :tid OR s.teacher_id = :tid)");
    $stmt->execute([':cid' => $class_id, ':tid' => $user->id]);
    if ($stmt->fetchColumn() == 0) {
        Response::error('Bạn không có quyền xem thống kê lớp này.', 403);
    }
}

// Xây dựng điều kiện lọc ngày
$dateCondition = "";
$params = [':class_id' => $class_id];

if ($start_date && $end_date) {
    $dateCondition = " AND (v.created_at BETWEEN :start_date AND :end_date) ";
    $params[':start_date'] = $start_date . " 00:00:00";
    $params[':end_date'] = $end_date . " 23:59:59";
}

// Query: Lấy danh sách học sinh và tổng điểm trừ
$query = "SELECT 
            u.id as student_id,
            u.full_name,
            u.username as student_code,
            COALESCE(SUM(cr.points), 0) as total_deducted,
            COUNT(v.id) as violation_count
          FROM student_details sd
          JOIN users u ON sd.user_id = u.id
          LEFT JOIN violations v ON u.id = v.student_id $dateCondition
          LEFT JOIN conduct_rules cr ON v.rule_id = cr.id
          WHERE sd.class_id = :class_id
          GROUP BY u.id, u.full_name, u.username
          ORDER BY total_deducted ASC, violation_count ASC, u.full_name ASC";

try {
    $stmt = $db->prepare($query);
    $stmt->execute($params);
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    Response::success($data);
} catch (PDOException $e) {
    Response::error('Lỗi truy vấn: ' . $e->getMessage(), 500);
}
