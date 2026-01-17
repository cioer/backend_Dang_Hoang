<?php
include_once '../../config/database.php';
include_once '../../config/jwt.php';
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$headers = getallheaders();
$auth = $headers['Authorization'] ?? $headers['authorization'] ?? null;
if (!$auth || stripos($auth, 'Bearer ') !== 0) { http_response_code(401); echo json_encode(["message"=>"Unauthorized"]); exit; }
$token = substr($auth, 7);
$decoded = validateJwt($token);
if (!$decoded) { http_response_code(401); echo json_encode(["message"=>"Invalid token"]); exit; }
$userId = $decoded['data']->id ?? null;
$role = $decoded['data']->role ?? null;

$database = new Database();
$db = $database->getConnection();
if (!$db) { http_response_code(500); echo json_encode(["message"=>"Database connection failed"]); exit; }
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$input = file_get_contents("php://input");
$data = json_decode($input, true);
$classId = isset($_GET['class_id']) ? (int)$_GET['class_id'] : (isset($data['class_id']) ? (int)$data['class_id'] : 0);
$label = isset($_GET['label']) ? $_GET['label'] : (isset($data['label']) ? $data['label'] : "");
if (!$classId || !$label) { http_response_code(400); echo json_encode(["message"=>"Missing class_id or label"]); exit; }
$parts = explode('/', $label);
if (count($parts) !== 2) { http_response_code(400); echo json_encode(["message"=>"Invalid label format"]); exit; }
$y = (int)$parts[0];
$m = (int)$parts[1];

if ($role === 'teacher') {
    $stmt = $db->prepare("SELECT 1 FROM classes WHERE id=:cid AND homeroom_teacher_id=:tid LIMIT 1");
    $stmt->execute([":cid"=>$classId, ":tid"=>$userId]);
    $ok1 = $stmt->fetchColumn() ? true : false;
    $stmt2 = $db->prepare("SELECT 1 FROM class_teacher_assignments WHERE class_id=:cid AND teacher_id=:tid LIMIT 1");
    $stmt2->execute([":cid"=>$classId, ":tid"=>$userId]);
    $ok2 = $stmt2->fetchColumn() ? true : false;
    if (!$ok1 && !$ok2) { http_response_code(403); echo json_encode(["message"=>"Forbidden"]); exit; }
}
if ($role === 'student' || $role === 'parent') { http_response_code(403); echo json_encode(["message"=>"Forbidden"]); exit; }

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
$stmt->execute([":cid"=>$classId, ":y"=>$y, ":m"=>$m]);
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
echo json_encode($result);
