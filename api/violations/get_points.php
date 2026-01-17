<?php
require_once __DIR__ . '/../bootstrap.php';

use App\Core\{Middleware, Response, Request, Bootstrap};

Middleware::cors('POST');
Middleware::auth();

$db = Bootstrap::db();
$body = Request::all();

$sid = isset($body['student_id']) ? (int)$body['student_id'] : null;
if (!$sid) {
    Response::error('Missing student_id', 400);
}

$db->exec("INSERT IGNORE INTO discipline_points(student_id, points) VALUES ($sid, 100)");

$stmt = $db->prepare("SELECT points FROM discipline_points WHERE student_id=:sid");
$stmt->bindParam(":sid", $sid);
$stmt->execute();
$p = $stmt->fetch(PDO::FETCH_ASSOC);
$points = $p ? (int)$p['points'] : 100;

$th = [];
foreach (['discipline_threshold_warn', 'discipline_threshold_conduct', 'discipline_threshold_class_change'] as $k) {
    $st = $db->prepare("SELECT setting_value FROM system_settings WHERE setting_key=:k");
    $st->bindParam(":k", $k);
    $st->execute();
    $v = $st->fetch(PDO::FETCH_ASSOC);
    $th[$k] = $v ? (int)$v['setting_value'] : null;
}

Response::success(["points" => $points, "thresholds" => $th]);
