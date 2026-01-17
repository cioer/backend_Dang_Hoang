<?php
include_once '../../config/database.php';
include_once '../../config/jwt.php';
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
$headers = getallheaders();
$token = isset($headers['Authorization']) ? str_replace('Bearer ', '', $headers['Authorization']) : null;
$decoded = $token ? validateJWT($token) : null;
if (!$decoded) { http_response_code(401); echo json_encode(["message"=>"Unauthorized"]); exit; }
$db = (new Database())->getConnection();
$body = json_decode(file_get_contents('php://input'), true);
$class_id = $body['class_id'] ?? null;
if (!$class_id) { http_response_code(400); echo json_encode(["message"=>"Missing class_id"]); exit; }
$stmt = $db->prepare("
SELECT DISTINCT u.id, u.username AS code, u.full_name, u.avatar, c.name AS class,
       COALESCE(v.cnt,0) AS violations_count,
       sp.guardian_name AS parent_name, sp.guardian_phone AS parent_phone
FROM class_registrations cr
JOIN users u ON u.id=cr.student_id
JOIN classes c ON c.id=cr.class_id
LEFT JOIN student_profiles sp ON sp.user_id = u.id
LEFT JOIN (
  SELECT student_id, COUNT(*) AS cnt FROM violations GROUP BY student_id
) v ON v.student_id=u.id
WHERE u.role='student' AND cr.class_id=:cid
ORDER BY u.full_name ASC
");
$stmt->execute([":cid"=>$class_id]);
echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
