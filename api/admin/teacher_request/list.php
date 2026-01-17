<?php
require_once __DIR__ . '/../../bootstrap.php';

use App\Core\{Middleware, Response, Bootstrap};

Middleware::cors('GET');
Middleware::requireAdmin();

$db = Bootstrap::db();

$q = $db->query("SELECT r.id, r.status, r.requested_at, u.full_name AS teacher_name, c.name AS class_name
                 FROM teacher_class_requests r
                 JOIN users u ON u.id=r.teacher_id
                 JOIN classes c ON c.id=r.class_id
                 ORDER BY r.requested_at DESC");

Response::success($q->fetchAll(PDO::FETCH_ASSOC));
