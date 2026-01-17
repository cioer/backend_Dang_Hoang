<?php
require_once __DIR__ . '/../bootstrap.php';

use App\Core\{Middleware, Response, Request, Bootstrap};

Middleware::cors('POST');

$user = Middleware::auth();
$db = Bootstrap::db();
$data = Request::all();

$class_name = $data['class_name'] ?? null;
$subject_name = $data['subject_name'] ?? null;
$teacher_id = $user->id;

if (!$class_name || !$subject_name) {
    Response::error('Missing fields', 400);
}

$cid = $db->prepare("SELECT id FROM classes WHERE name=:n");
$cid->execute([":n" => $class_name]);
$c = $cid->fetch(PDO::FETCH_ASSOC);

$sid = $db->prepare("SELECT id FROM subjects WHERE name=:n");
$sid->execute([":n" => $subject_name]);
$s = $sid->fetch(PDO::FETCH_ASSOC);

if (!$c || !$s) {
    Response::notFound('Class or subject not found');
}

$occupied = $db->prepare("SELECT day_of_week, period FROM schedule WHERE (class_id=:c OR teacher_id=:t)");
$occupied->execute([":c" => $c['id'], ":t" => $teacher_id]);
$busy = [];
foreach ($occupied->fetchAll(PDO::FETCH_ASSOC) as $row) {
    $busy[$row['day_of_week'] . '-' . $row['period']] = true;
}

$result = [];
for ($dow = 2; $dow <= 7; $dow++) {
    for ($p = 1; $p <= 6; $p++) {
        $key = $dow . '-' . $p;
        if (!isset($busy[$key])) {
            $result[] = ["class" => $class_name, "subject" => $subject_name, "day_of_week" => $dow, "period" => $p, "start_time" => "08:00", "end_time" => "08:45"];
            if (count($result) >= 5) break;
        }
    }
    if (count($result) >= 5) break;
}

Response::success(["suggestions" => $result]);
