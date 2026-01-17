<?php
require_once __DIR__ . '/../bootstrap.php';

use App\Core\{Middleware, Response, Bootstrap};

Middleware::cors('GET');

$user = Middleware::optionalAuth();
if (!$user) {
    Response::success(["complete" => false]);
}

$db = Bootstrap::db();
$uid = $user->id;
$role = $user->role;

if ($role === 'parent') {
    $stmt = $db->prepare("SELECT sp.is_complete FROM parent_student_links psl JOIN student_profiles sp ON sp.user_id=psl.student_id WHERE psl.parent_id=:pid LIMIT 1");
    $stmt->execute([":pid" => $uid]);
} else {
    $stmt = $db->prepare("SELECT is_complete FROM student_profiles WHERE user_id=:uid");
    $stmt->execute([":uid" => $uid]);
}

$row = $stmt->fetch(PDO::FETCH_ASSOC);
Response::success(["complete" => $row ? (bool)$row['is_complete'] : false]);
