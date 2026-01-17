<?php
require_once __DIR__ . '/../bootstrap.php';

use App\Core\{Middleware, Response, Bootstrap};

Middleware::cors('GET');

$user = Middleware::auth();
$db = Bootstrap::db();

$role = $user->role;
$classId = isset($_GET['class_id']) ? intval($_GET['class_id']) : null;

if ($role === 'teacher' && $classId) {
    $stmt = $db->prepare("SELECT 1 FROM schedule WHERE teacher_id=:tid AND class_id=:cid LIMIT 1");
    $stmt->execute([':tid' => $user->id, ':cid' => $classId]);
    if (!$stmt->fetch()) {
        Response::forbidden('Forbidden');
    }
}

$params = [];
$sql = "SELECT l.id, l.action, l.target_user_id, l.class_id, l.area, l.created_at, a.full_name AS actor_name, u.full_name AS target_name FROM red_committee_logs l JOIN users a ON l.actor_id=a.id JOIN users u ON l.target_user_id=u.id WHERE 1=1";

if ($classId) {
    $sql .= " AND l.class_id=:cid";
    $params[':cid'] = $classId;
}
$sql .= " ORDER BY l.created_at DESC LIMIT 200";

$stmt = $db->prepare($sql);
$stmt->execute($params);
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

Response::success($rows);
