<?php
include_once '../../config/database.php';
include_once '../../config/jwt.php';
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$headers = getallheaders();
$auth = $headers['Authorization'] ?? $headers['authorization'] ?? null;
if (!$auth || stripos($auth, 'Bearer ') !== 0) { http_response_code(401); echo "Unauthorized"; exit; }
$token = substr($auth, 7);
$decoded = validateJwt($token);
if (!$decoded) { http_response_code(401); echo "Invalid token"; exit; }
$userId = $decoded['data']->id ?? null;
$role = $decoded['data']->role ?? null;

$database = new Database();
$db = $database->getConnection();
if (!$db) { http_response_code(500); echo "Database connection failed"; exit; }
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$type = $_GET['type'] ?? ($_POST['type'] ?? '');
$classId = isset($_GET['class_id']) ? (int)$_GET['class_id'] : (isset($_POST['class_id']) ? (int)$_POST['class_id'] : 0);
$label = $_GET['label'] ?? ($_POST['label'] ?? '');
$year = isset($_GET['year']) ? (int)$_GET['year'] : (isset($_POST['year']) ? (int)$_POST['year'] : 0);
$semester = $_GET['semester'] ?? ($_POST['semester'] ?? '');

if ($role === 'teacher') {
    $stmt = $db->prepare("SELECT 1 FROM classes WHERE id=:cid AND homeroom_teacher_id=:tid LIMIT 1");
    $stmt->execute([":cid"=>$classId, ":tid"=>$userId]);
    $ok1 = $stmt->fetchColumn() ? true : false;
    $stmt2 = $db->prepare("SELECT 1 FROM class_teacher_assignments WHERE class_id=:cid AND teacher_id=:tid LIMIT 1");
    $stmt2->execute([":cid"=>$classId, ":tid"=>$userId]);
    $ok2 = $stmt2->fetchColumn() ? true : false;
    if (!$ok1 && !$ok2) { http_response_code(403); echo "Forbidden"; exit; }
}
if ($role === 'student' || $role === 'parent') { http_response_code(403); echo "Forbidden"; exit; }

$rows = [];
if ($type === 'week') {
    if (!$classId || !$label) { http_response_code(400); echo "Missing class_id or label"; exit; }
    $parts = explode('/', $label); if (count($parts) !== 2) { http_response_code(400); echo "Invalid label"; exit; }
    $y = (int)$parts[0]; $w = (int)$parts[1];
    $sql = "
    SELECT u.id AS student_id,u.full_name AS student_name,u.username AS student_code,
           COALESCE(SUM(CASE WHEN cr.type='minus' THEN cr.points ELSE 0 END),0) AS points_lost,
           COUNT(v.id) AS violations_count
    FROM users u
    JOIN student_details sd ON sd.user_id=u.id AND sd.class_id=:cid
    LEFT JOIN violations v ON v.student_id=u.id AND YEAR(v.created_at)=:y AND WEEK(v.created_at)=:w
    LEFT JOIN conduct_rules cr ON cr.id=v.rule_id
    GROUP BY u.id,u.full_name,u.username
    ORDER BY points_lost ASC,u.full_name ASC";
    $stmt = $db->prepare($sql);
    $stmt->execute([":cid"=>$classId, ":y"=>$y, ":w"=>$w]);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
} elseif ($type === 'month') {
    if (!$classId || !$label) { http_response_code(400); echo "Missing class_id or label"; exit; }
    $parts = explode('/', $label); if (count($parts) !== 2) { http_response_code(400); echo "Invalid label"; exit; }
    $y = (int)$parts[0]; $m = (int)$parts[1];
    $sql = "
    SELECT u.id AS student_id,u.full_name AS student_name,u.username AS student_code,
           COALESCE(SUM(CASE WHEN cr.type='minus' THEN cr.points ELSE 0 END),0) AS points_lost,
           COUNT(v.id) AS violations_count
    FROM users u
    JOIN student_details sd ON sd.user_id=u.id AND sd.class_id=:cid
    LEFT JOIN violations v ON v.student_id=u.id AND YEAR(v.created_at)=:y AND MONTH(v.created_at)=:m
    LEFT JOIN conduct_rules cr ON cr.id=v.rule_id
    GROUP BY u.id,u.full_name,u.username
    ORDER BY points_lost ASC,u.full_name ASC";
    $stmt = $db->prepare($sql);
    $stmt->execute([":cid"=>$classId, ":y"=>$y, ":m"=>$m]);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
} elseif ($type === 'semester') {
    if (!$classId || !$year || !$semester) { http_response_code(400); echo "Missing class_id/year/semester"; exit; }
    $semester = strtoupper($semester); if (!in_array($semester, ["HK1","HK2"], true)) { http_response_code(400); echo "Invalid semester"; exit; }
    $m1 = $semester === "HK1" ? 9 : 1; $m2 = $semester === "HK1" ? 12 : 5;
    $sql = "
    SELECT u.id AS student_id,u.full_name AS student_name,u.username AS student_code,
           COALESCE(SUM(CASE WHEN cr.type='minus' THEN cr.points ELSE 0 END),0) AS points_lost,
           COUNT(v.id) AS violations_count
    FROM users u
    JOIN student_details sd ON sd.user_id=u.id AND sd.class_id=:cid
    LEFT JOIN violations v ON v.student_id=u.id AND YEAR(v.created_at)=:y AND MONTH(v.created_at) BETWEEN :m1 AND :m2
    LEFT JOIN conduct_rules cr ON cr.id=v.rule_id
    GROUP BY u.id,u.full_name,u.username
    ORDER BY points_lost ASC,u.full_name ASC";
    $stmt = $db->prepare($sql);
    $stmt->execute([":cid"=>$classId, ":y"=>$year, ":m1"=>$m1, ":m2"=>$m2]);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    http_response_code(400); echo "Invalid type"; exit;
}

$out = fopen('php://output', 'w');
header("Content-Type: text/csv; charset=utf-8");
header("Content-Disposition: attachment; filename=ranking_export.csv");
fputcsv($out, ["STT","Mã HS","Tên HS","Số vi phạm","Điểm trừ","Điểm","Xếp loại"]);
$i = 1;
foreach ($rows as $r) {
    $lost = (int)$r['points_lost'];
    $score = max(0, 100 - $lost);
    $grade = $score >= 90 ? "Tốt" : ($score >= 80 ? "Khá" : ($score >= 65 ? "Trung bình" : "Yếu"));
    fputcsv($out, [$i++, $r['student_code'], $r['student_name'], (int)$r['violations_count'], $lost, $score, $grade]);
}
fclose($out);
