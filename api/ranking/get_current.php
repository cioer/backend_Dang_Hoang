<?php
require_once __DIR__ . '/../bootstrap.php';

use App\Core\{Middleware, Response, Request, Bootstrap};

Middleware::cors('GET, POST');

$user = Middleware::auth();
$userId = $user->id;
$role = $user->role;

$db = Bootstrap::db();
$data = Request::all();

$classId = Request::getInt('class_id') ?: ($data['class_id'] ?? 0);

if (!$classId) {
    Response::error('Missing class_id', 400);
}

if ($role === 'teacher') {
    $stmt = $db->prepare("SELECT 1 FROM classes WHERE id=:cid AND homeroom_teacher_id=:tid LIMIT 1");
    $stmt->execute([":cid" => $classId, ":tid" => $userId]);
    $ok1 = $stmt->fetchColumn();
    $stmt2 = $db->prepare("SELECT 1 FROM class_teacher_assignments WHERE class_id=:cid AND teacher_id=:tid LIMIT 1");
    $stmt2->execute([":cid" => $classId, ":tid" => $userId]);
    $ok2 = $stmt2->fetchColumn();
    if (!$ok1 && !$ok2) {
        Response::forbidden();
    }
}
if ($role === 'student' || $role === 'parent') {
    Response::forbidden();
}

$sql = "
SELECT
    u.id AS student_id,
    u.full_name AS student_name,
    u.username AS student_code,
    COALESCE(dp.points, 100) AS current_score,
    (100 - COALESCE(dp.points, 100)) AS points_lost
FROM users u
JOIN student_details sd ON sd.user_id = u.id AND sd.class_id = :cid
LEFT JOIN discipline_points dp ON dp.student_id = u.id
ORDER BY current_score DESC, u.full_name ASC
";

try {
    $stmt = $db->prepare($sql);
    $stmt->execute([":cid" => $classId]);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $result = [];
    $rank = 1;
    foreach ($rows as $r) {
        $score = (int)$r['current_score'];
        $grade = $score >= 90 ? "Tốt" : ($score >= 80 ? "Khá" : ($score >= 65 ? "Trung bình" : "Yếu"));
        $result[] = [
            "rank" => $rank++,
            "student_id" => (int)$r['student_id'],
            "student_name" => $r['student_name'],
            "student_code" => $r['student_code'],
            "points_lost" => (int)$r['points_lost'],
            "current_score" => $score,
            "grade" => $grade
        ];
    }
    Response::success($result);
} catch (Exception $e) {
    Response::error('Error: ' . $e->getMessage(), 500);
}
