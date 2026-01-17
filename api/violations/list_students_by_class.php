<?php
require_once __DIR__ . '/../bootstrap.php';

use App\Core\{Middleware, Response, Request, Bootstrap};

Middleware::cors('POST');
Middleware::auth();

$db = Bootstrap::db();
$body = Request::all();

$class_id = $body['class_id'] ?? null;
if (!$class_id) {
    Response::error('Missing class_id', 400);
}

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
$stmt->execute([":cid" => $class_id]);

Response::success($stmt->fetchAll(PDO::FETCH_ASSOC));
