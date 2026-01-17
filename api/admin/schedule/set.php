<?php
require_once __DIR__ . '/../../bootstrap.php';

use App\Core\{Middleware, Response, Request, Bootstrap};

Middleware::cors('POST');
$user = Middleware::requireAdmin();

$db = Bootstrap::db();
$body = Request::all();

$class_name = $body['class_name'] ?? null;
$subject_name = $body['subject_name'] ?? null;
$weekday = $body['weekday'] ?? null;
$start_time = $body['start_time'] ?? null;
$end_time = $body['end_time'] ?? null;

if (!$class_name || !$subject_name || !$weekday || !$start_time || !$end_time) {
    Response::error("Missing fields", 400);
}

$cid = $db->prepare("SELECT id FROM classes WHERE name=:n");
$cid->execute([":n" => $class_name]);
$c = $cid->fetch(PDO::FETCH_ASSOC);

$sid = $db->prepare("SELECT id FROM subjects WHERE name=:n");
$sid->execute([":n" => $subject_name]);
$s = $sid->fetch(PDO::FETCH_ASSOC);

if (!$c || !$s) {
    Response::notFound("Class or subject not found");
}

$teacher_id = $user->id;
$ins = $db->prepare("INSERT INTO schedule(class_id, subject_id, teacher_id, day_of_week, period, semester) VALUES (:c,:s,:t,:w,:p,:sem)");
$ins->execute([":c" => $c['id'], ":s" => $s['id'], ":t" => $teacher_id, ":w" => $weekday, ":p" => 1, ":sem" => "HK1-2025"]);

Response::message("OK");
