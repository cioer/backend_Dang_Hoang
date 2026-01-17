<?php
require_once __DIR__ . '/../bootstrap.php';

use App\Core\{Middleware, Response, Request, Bootstrap};

Middleware::cors('POST');

$user = Middleware::auth();

if ($user->role != 'teacher') {
    Response::forbidden('Forbidden');
}

$db = Bootstrap::db();
$data = Request::all();

$class_id = $data['class_id'] ?? null;
$items = $data['items'] ?? [];

if (!$class_id || !is_array($items)) {
    Response::error('Missing fields', 400);
}

$tid = $user->id;
$stmt = $db->prepare("INSERT INTO conduct_results(student_id, teacher_id, class_id, date, score, comment) VALUES (:sid,:tid,:cid,:d,:s,:c)");

foreach ($items as $it) {
    $sid = $it['student_id'] ?? null;
    $score = $it['score'] ?? null;
    $comment = $it['comment'] ?? '';
    if ($sid === null || $score === null) continue;
    $stmt->execute([":sid" => $sid, ":tid" => $tid, ":cid" => $class_id, ":d" => date('Y-m-d'), ":s" => $score, ":c" => $comment]);
}

Response::success(["message" => "OK"]);
