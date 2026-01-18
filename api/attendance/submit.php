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

// Summary counters
$summary = [
    'total' => count($attendance_list),
    'present' => 0,
    'absent_excused' => 0,
    'absent_unexcused' => 0,
    'late' => 0
];

try {
    $db->beginTransaction();

    // Main attendance insert/update query
    $query = "INSERT INTO attendance (class_id, student_id, date, status, note, recorded_by)
              VALUES (:class_id, :student_id, :date, :status, :note, :recorded_by)
              ON DUPLICATE KEY UPDATE status = :status_update, note = :note_update, recorded_by = :recorded_by_update";

    $stmt = $db->prepare($query);

    // Get the rule_id for "Vắng học không phép" once (using LIKE for encoding compatibility)
    $ruleStmt = $db->prepare("SELECT id, points FROM conduct_rules WHERE rule_name LIKE '%ng h%c kh%ng ph%p' LIMIT 1");
    $ruleStmt->execute();
    $absenceRule = $ruleStmt->fetch(PDO::FETCH_ASSOC);

    foreach ($attendance_list as $item) {
        $item = (object)$item;
        $note = $item->note ?? '';

        $stmt->bindParam(":class_id", $class_id);
        $stmt->bindParam(":student_id", $item->student_id);
        $stmt->bindParam(":date", $date);
        $stmt->bindParam(":status", $item->status);
        $stmt->bindParam(":note", $note);
        $stmt->bindParam(":recorded_by", $teacher_id);

        // For update
        $stmt->bindParam(":status_update", $item->status);
        $stmt->bindParam(":note_update", $note);
        $stmt->bindParam(":recorded_by_update", $teacher_id);

        $stmt->execute();

        // Update summary
        if (isset($summary[$item->status])) {
            $summary[$item->status]++;
        }

        // Handle unexcused absence - deduct discipline points and notify parents
        if ($item->status === 'absent_unexcused' && $absenceRule) {
            $studentId = $item->student_id;
            $ruleId = $absenceRule['id'];
            $points = (int)$absenceRule['points'];

            // 1. Record violation
            $violationStmt = $db->prepare("INSERT INTO violations (student_id, rule_id, reporter_id, note, created_at)
                                           VALUES (:student_id, :rule_id, :reporter_id, :note, NOW())");
            $violationNote = "Vắng học không phép ngày " . $date . ($note ? ": " . $note : "");
            $violationStmt->bindParam(":student_id", $studentId);
            $violationStmt->bindParam(":rule_id", $ruleId);
            $violationStmt->bindParam(":reporter_id", $teacher_id);
            $violationStmt->bindParam(":note", $violationNote);
            $violationStmt->execute();

            // 2. Initialize discipline points if not exists
            $initStmt = $db->prepare("INSERT IGNORE INTO discipline_points(student_id, points) VALUES (:sid, 100)");
            $initStmt->execute([':sid' => $studentId]);

            // 3. Get current points
            $curStmt = $db->prepare("SELECT points FROM discipline_points WHERE student_id = :sid");
            $curStmt->execute([':sid' => $studentId]);
            $curRow = $curStmt->fetch(PDO::FETCH_ASSOC);
            $currentPoints = $curRow ? (int)$curRow['points'] : 100;

            // 4. Deduct points (minus type)
            $newPoints = max(0, $currentPoints - $points);

            if ($newPoints !== $currentPoints) {
                $updStmt = $db->prepare("UPDATE discipline_points SET points = :p WHERE student_id = :sid");
                $updStmt->bindParam(":p", $newPoints);
                $updStmt->bindParam(":sid", $studentId);
                $updStmt->execute();
            }

            // 5. Get student name for notification
            $studentNameStmt = $db->prepare("SELECT full_name FROM users WHERE id = :sid");
            $studentNameStmt->execute([':sid' => $studentId]);
            $studentRow = $studentNameStmt->fetch(PDO::FETCH_ASSOC);
            $studentName = $studentRow ? $studentRow['full_name'] : 'Học sinh';

            // 6. Send notification to parent(s) via parent_student_links
            $parentStmt = $db->prepare("SELECT parent_id FROM parent_student_links WHERE student_id = :sid");
            $parentStmt->execute([':sid' => $studentId]);
            $parents = $parentStmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($parents as $parent) {
                $notifyStmt = $db->prepare("INSERT INTO notifications (title, content, sender_id, receiver_id, target_role, priority, created_at)
                                            VALUES (:title, :content, :sender_id, :receiver_id, 'parent', 'high', NOW())");

                $notifyTitle = "Thông báo vắng học không phép";
                $notifyContent = "Con em $studentName đã vắng học không phép ngày $date. Điểm nề nếp bị trừ $points điểm, còn lại $newPoints điểm.";

                $notifyStmt->bindParam(":title", $notifyTitle);
                $notifyStmt->bindParam(":content", $notifyContent);
                $notifyStmt->bindParam(":sender_id", $teacher_id);
                $notifyStmt->bindParam(":receiver_id", $parent['parent_id']);
                $notifyStmt->execute();
            }
        }
    }

    $db->commit();
    Response::success([
        "message" => "Điểm danh thành công",
        "summary" => $summary
    ]);

} catch (Exception $e) {
    $db->rollBack();
    Response::error('Lỗi hệ thống: ' . $e->getMessage(), 503);
}
