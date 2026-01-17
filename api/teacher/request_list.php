<?php
require_once __DIR__ . '/../bootstrap.php';

use App\Core\{Middleware, Response, Bootstrap};

Middleware::cors('GET, POST');
$user = Middleware::requireRole('teacher');
$db = Bootstrap::db();

$tid = $user->id;

$q = $db->prepare("SELECT r.id, r.class_id, c.name AS class_name, r.status, r.requested_at, r.approved_at FROM teacher_class_requests r JOIN classes c ON c.id=r.class_id WHERE r.teacher_id=:tid ORDER BY r.requested_at DESC");
$q->execute([":tid" => $tid]);

Response::success($q->fetchAll(PDO::FETCH_ASSOC));
