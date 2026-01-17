<?php
require_once __DIR__ . '/../bootstrap.php';

use App\Core\{Middleware, Response, Bootstrap};

Middleware::cors('GET');

$user = Middleware::auth();

if ($user->role !== 'parent') {
    Response::forbidden('Forbidden');
}

$parentId = $user->id;
$studentId = isset($_GET['student_id']) ? (int)$_GET['student_id'] : 0;
$type = $_GET['type'] ?? 'month';
$label = $_GET['label'] ?? '';
$year = isset($_GET['year']) ? (int)$_GET['year'] : 0;
$semester = $_GET['semester'] ?? '';

if (!$studentId) {
    Response::error('Missing student_id', 400);
}

$db = Bootstrap::db();

// Verify parent-student link
$chk = $db->prepare("SELECT 1 FROM parent_student_links WHERE parent_id=:p AND student_id=:s LIMIT 1");
$chk->execute([":p" => $parentId, ":s" => $studentId]);
if (!$chk->fetchColumn()) {
    Response::forbidden('Forbidden');
}

$where = "v.student_id = :sid";
$params = [":sid" => $studentId];

if ($type === 'week') {
    $parts = explode('/', $label);
    if (count($parts) !== 2) {
        Response::error('Invalid label', 400);
    }
    $params[":y"] = (int)$parts[0];
    $params[":w"] = (int)$parts[1];
    $where .= " AND YEAR(v.created_at) = :y AND WEEK(v.created_at) = :w";
} elseif ($type === 'month') {
    $parts = explode('/', $label);
    if (count($parts) !== 2) {
        Response::error('Invalid label', 400);
    }
    $params[":y"] = (int)$parts[0];
    $params[":m"] = (int)$parts[1];
    $where .= " AND YEAR(v.created_at) = :y AND MONTH(v.created_at) = :m";
} elseif ($type === 'semester') {
    if (!$year || !in_array(strtoupper($semester), ["HK1", "HK2"], true)) {
        Response::error('Invalid semester', 400);
    }
    $params[":y"] = $year;
    $params[":m1"] = strtoupper($semester) === "HK1" ? 9 : 1;
    $params[":m2"] = strtoupper($semester) === "HK1" ? 12 : 5;
    $where .= " AND YEAR(v.created_at) = :y AND MONTH(v.created_at) BETWEEN :m1 AND :m2";
} else {
    Response::error('Invalid type', 400);
}

$sql = "
SELECT
    COALESCE(SUM(CASE WHEN cr.type='minus' THEN cr.points ELSE 0 END),0) AS points_lost,
    COUNT(v.id) AS violations_count
FROM violations v
LEFT JOIN conduct_rules cr ON cr.id = v.rule_id
WHERE $where
";

$st = $db->prepare($sql);
$st->execute($params);
$row = $st->fetch(PDO::FETCH_ASSOC);

$lost = (int)($row['points_lost'] ?? 0);
$count = (int)($row['violations_count'] ?? 0);
$score = max(0, 100 - $lost);
$grade = $score >= 90 ? "Tốt" : ($score >= 80 ? "Khá" : ($score >= 65 ? "Trung bình" : "Yếu"));

Response::success([
    "points_lost" => $lost,
    "violations_count" => $count,
    "score" => $score,
    "grade" => $grade
]);
