<?php
include_once '../../config/database.php';
include_once '../../config/jwt.php';
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
$h = getallheaders();
$auth = $h['Authorization'] ?? $h['authorization'] ?? null;
if (!$auth || stripos($auth, 'Bearer ') !== 0) { http_response_code(401); echo json_encode(["message"=>"Unauthorized"]); exit; }
$token = substr($auth, 7);
$decoded = validateJwt($token);
if (!$decoded) { http_response_code(401); echo json_encode(["message"=>"Invalid token"]); exit; }
$parentId = $decoded['data']->id ?? null;
$role = $decoded['data']->role ?? null;
if ($role !== 'parent') { http_response_code(403); echo json_encode(["message"=>"Forbidden"]); exit; }
$studentId = isset($_GET['student_id']) ? (int)$_GET['student_id'] : 0;
$type = $_GET['type'] ?? 'month';
$label = $_GET['label'] ?? '';
$year = isset($_GET['year']) ? (int)$_GET['year'] : 0;
$semester = $_GET['semester'] ?? '';
if (!$studentId) { http_response_code(400); echo json_encode(["message"=>"Missing student_id"]); exit; }
$db = (new Database())->getConnection();
if (!$db) { http_response_code(500); echo json_encode(["message"=>"Database connection failed"]); exit; }
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$chk = $db->prepare("SELECT 1 FROM parent_student_links WHERE parent_id=:p AND student_id=:s LIMIT 1");
$chk->execute([":p"=>$parentId, ":s"=>$studentId]);
if (!$chk->fetchColumn()) { http_response_code(403); echo json_encode(["message"=>"Forbidden"]); exit; }
$where = "v.student_id = :sid";
$params = [":sid"=>$studentId];
if ($type === 'week') {
    $parts = explode('/', $label);
    if (count($parts) !== 2) { http_response_code(400); echo json_encode(["message"=>"Invalid label"]); exit; }
    $params[":y"] = (int)$parts[0];
    $params[":w"] = (int)$parts[1];
    $where .= " AND YEAR(v.created_at) = :y AND WEEK(v.created_at) = :w";
} elseif ($type === 'month') {
    $parts = explode('/', $label);
    if (count($parts) !== 2) { http_response_code(400); echo json_encode(["message"=>"Invalid label"]); exit; }
    $params[":y"] = (int)$parts[0];
    $params[":m"] = (int)$parts[1];
    $where .= " AND YEAR(v.created_at) = :y AND MONTH(v.created_at) = :m";
} elseif ($type === 'semester') {
    if (!$year || !in_array(strtoupper($semester), ["HK1","HK2"], true)) { http_response_code(400); echo json_encode(["message"=>"Invalid semester"]); exit; }
    $params[":y"] = $year;
    $params[":m1"] = strtoupper($semester) === "HK1" ? 9 : 1;
    $params[":m2"] = strtoupper($semester) === "HK1" ? 12 : 5;
    $where .= " AND YEAR(v.created_at) = :y AND MONTH(v.created_at) BETWEEN :m1 AND :m2";
} else { http_response_code(400); echo json_encode(["message"=>"Invalid type"]); exit; }
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
echo json_encode([
    "points_lost"=>$lost,
    "violations_count"=>$count,
    "score"=>$score,
    "grade"=>$grade
]);
