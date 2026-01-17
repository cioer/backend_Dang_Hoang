<?php
require_once __DIR__ . '/../bootstrap.php';

use App\Core\{Middleware, Response, Request, Bootstrap};

Middleware::cors('POST');

$user = Middleware::auth();

// Include utility functions
require_once __DIR__ . '/util.php';

if (!verifyAction()) {
    Response::error('Action verification required', 428);
}

$db = Bootstrap::db();
$role = $user->role;
$data = Request::all();

$userId = intval($data['user_id'] ?? 0);
$classId = isset($data['class_id']) ? intval($data['class_id']) : null;
$area = $data['area'] ?? null;

if ($userId <= 0) {
    Response::error('Invalid user_id', 400);
}

if ($role === 'teacher') {
    if (!$classId) {
        Response::forbidden('Forbidden');
    }
    $stmt = $db->prepare("SELECT 1 FROM schedule WHERE teacher_id=:tid AND class_id=:cid LIMIT 1");
    $stmt->execute([':tid' => $user->id, ':cid' => $classId]);
    if (!$stmt->fetch()) {
        Response::forbidden('Forbidden');
    }
}

$stmt = $db->prepare("UPDATE red_committee_members SET active=0, revoked_at=NOW() WHERE user_id=:uid AND (class_id<=>:cid) AND (area<=>:area)");
$stmt->execute([':uid' => $userId, ':cid' => $classId, ':area' => $area]);

$log = $db->prepare("INSERT INTO red_committee_logs(actor_id,action,target_user_id,class_id,area) VALUES(:aid,'remove',:tuid,:cid,:area)");
$log->execute([':aid' => $user->id, ':tuid' => $userId, ':cid' => $classId, ':area' => $area]);

Response::success(['status' => 'ok']);
