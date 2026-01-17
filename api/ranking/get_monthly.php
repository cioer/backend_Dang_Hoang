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
$label = Request::get('label') ?: ($data['label'] ?? '');

if (!$classId || !$label) {
    Response::error('Missing class_id or label', 400);
}

$parts = explode('/', $label);
if (count($parts) !== 2) {
    Response::error('Invalid label format', 400);
}
$y = (int)$parts[0];
$m = (int)$parts[1];

if ($role === 'teacher') {
    $stmt = $db->prepare("SELECT 1 FROM classes WHERE id=:cid AND homeroom_teacher_id=:tid LIMIT 1");
    $stmt->execute([":cid" => $classId, ":tid" => $userId]);
    $ok1 = $stmt->fetchColumn() ? true : false;
    $stmt2 = $db->prepare("SELECT 1 FROM class_teacher_assignments WHERE class_id=:cid AND teacher_id=:tid LIMIT 1");
    $stmt2->execute([":cid" => $classId, ":tid" => $userId]);
    $ok2 = $stmt2->fetchColumn() ? true : false;
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
    COALESCE(SUM(CASE WHEN cr.type='minus' THEN cr.points ELSE 0 END),0) AS points_lost,
    COUNT(v.id) AS violations_count
FROM users u
JOIN student_details sd ON sd.user_id = u.id AND sd.class_id = :cid
LEFT JOIN violations v ON v.student_id = u.id AND YEAR(v.created_at) = :y AND MONTH(v.created_at) = :m
LEFT JOIN conduct_rules cr ON cr.id = v.rule_id
GROUP BY u.id, u.full_name, u.username
ORDER BY points_lost ASC, u.full_name ASC
";

$stmt = $db->prepare($sql);
$stmt->execute([":cid" => $classId, ":y" => $y, ":m" => $m]);
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

$result = [];
$rank = 1;
foreach ($rows as $r) {
    $lost = (int)$r['points_lost'];
    $score = max(0, 100 - $lost);
    $grade = $score >= 90 ? "Tốt" : ($score >= 80 ? "Khá" : ($score >= 65 ? "Trung bình" : "Yếu"));
    $result[] = [
        "rank" => $rank++,
        "student_id" => (int)$r['student_id'],
        "student_name" => $r['student_name'],
        "student_code" => $r['student_code'],
        "violations_count" => (int)$r['violations_count'],
        "points_lost" => $lost,
        "monthly_score" => $score,
        "grade" => $grade
    ];
}

Response::success($result);
