<?php
require_once __DIR__ . '/../bootstrap.php';

use App\Core\{Middleware, Response, Bootstrap};

Middleware::cors('GET,POST');

$user = Middleware::auth();

if ($user->role !== 'parent') {
    Response::forbidden('Forbidden');
}

$parentId = $user->id;
$db = Bootstrap::db();

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
$stmt->execute([":pid" => $parentId]);
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($rows as &$r) {
    $r['birth_date'] = "N/A";
    $r['address'] = "";
    if (!$r['class_name']) $r['class_name'] = "Chưa xếp lớp";
}

Response::success($rows);
