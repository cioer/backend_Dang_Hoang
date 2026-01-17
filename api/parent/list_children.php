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
$role = $decoded['data']->role ?? null;
$parentId = $decoded['data']->id ?? null;
if ($role !== 'parent') { http_response_code(403); echo json_encode(["message"=>"Forbidden"]); exit; }

$database = new Database();
$db = $database->getConnection();
if (!$db) { http_response_code(500); echo json_encode(["message"=>"Database connection failed"]); exit; }
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$sql = "
SELECT 
    s.id AS student_id,
    s.full_name AS name,
    s.username AS code,
    s.phone AS phone,
    c.name AS class_name
FROM parent_student_links p
JOIN users s ON s.id = p.student_id
LEFT JOIN student_details sd ON sd.user_id = s.id
LEFT JOIN classes c ON c.id = sd.class_id
WHERE p.parent_id = :pid
ORDER BY s.full_name ASC
";
$stmt = $db->prepare($sql);
$stmt->execute([":pid"=>$parentId]);
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
foreach ($rows as &$r) {
    $r['birth_date'] = "N/A";
    $r['address'] = "";
    if (!$r['class_name']) $r['class_name'] = "Chưa xếp lớp";
}
echo json_encode($rows);
