<?php
require_once __DIR__ . '/../bootstrap.php';

use App\Core\{Middleware, Response, Request, Bootstrap};

Middleware::cors('POST');
$user = Middleware::requireRole('teacher');
$db = Bootstrap::db();

$data = Request::all();
$class_ids = $data['class_ids'] ?? [];

if (!is_array($class_ids) || empty($class_ids)) {
    Response::error('Missing class_ids', 400);
}

$tid = $user->id;
$tname = $user->full_name ?? ('GV-' . $tid);

$stmt = $db->prepare("INSERT INTO teacher_class_requests(teacher_id, class_id, status, requested_at) VALUES (:tid, :cid, 'pending', NOW())");
$names = [];

foreach ($class_ids as $cid) {
    $cid = (int)$cid;
    $chk = $db->prepare("SELECT name FROM classes WHERE id=:cid");
    $chk->execute([":cid" => $cid]);
    $cls = $chk->fetch(PDO::FETCH_ASSOC);
    if ($cls) {
        $stmt->execute([":tid" => $tid, ":cid" => $cid]);
        $rid = $db->lastInsertId();
        $names[] = $cls['name'];
        // per-class message with request id tag
        $msgContent = "Yeu cau quan ly lop boi " . $tname . ": " . $cls['name'] . " - Thoi gian: " . date('Y-m-d H:i:s') . " [REQ:" . $rid . "]";
        $admins = $db->query("SELECT id FROM users WHERE role='admin'")->fetchAll(PDO::FETCH_ASSOC);
        if ($admins) {
            $insMsg = $db->prepare("INSERT INTO messages(sender_id, receiver_id, content, is_read, created_at) VALUES (:s,:r,:c,0,NOW())");
            foreach ($admins as $a) {
                $insMsg->execute([":s" => $tid, ":r" => $a['id'], ":c" => $msgContent]);
            }
        }
    }
}

$content = "Yeu cau quan ly lop boi " . $tname . ": " . implode(', ', $names) . " - Thoi gian: " . date('Y-m-d H:i:s');
$notify = $db->prepare("INSERT INTO notifications(title, content, sender_id, target_role) VALUES (:t,:c,:s,'admin')");
$notify->execute([":t" => "Yeu cau phan lop", ":c" => $content, ":s" => $tid]);

Response::message('OK');
