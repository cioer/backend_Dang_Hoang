<?php
require_once __DIR__ . '/../bootstrap.php';

use App\Core\{Middleware, Response, Request, Bootstrap};

Middleware::cors('POST');

$user = Middleware::auth();

if (!in_array($user->role, ['teacher', 'admin'])) {
    Response::forbidden('Forbidden');
}

$db = Bootstrap::db();
$data = Request::all();

$class_id = $data['class_id'] ?? null;
$subject_id = $data['subject_id'] ?? null;
$term = $data['term'] ?? 'HK1';
$items = $data['items'] ?? [];

if (!$class_id || !$subject_id || !is_array($items)) {
    Response::error('Missing fields', 400);
}

$stmt = $db->prepare("INSERT INTO scores(student_id, subject_id, term, score, created_at) VALUES (:sid,:sub,:term,:score,NOW()) ON DUPLICATE KEY UPDATE score=:score2");

foreach ($items as $it) {
    $sid = $it['student_id'] ?? null;
    $sc = $it['score'] ?? null;
    if ($sid === null || $sc === null) continue;
    $stmt->execute([":sid" => $sid, ":sub" => $subject_id, ":term" => $term, ":score" => $sc, ":score2" => $sc]);
}

Response::success(["message" => "OK"]);
