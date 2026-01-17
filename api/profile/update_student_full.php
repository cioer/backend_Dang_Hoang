<?php
require_once __DIR__ . '/../bootstrap.php';

use App\Core\{Middleware, Response, Request, Bootstrap};

Middleware::cors('POST');

$user = Middleware::auth();
if ($user->role != 'student') {
    Response::forbidden();
}

$db = Bootstrap::db();
$body = Request::all();

$fields = ['full_name', 'birth_date', 'address', 'phone', 'email', 'guardian_name', 'guardian_phone'];
foreach ($fields as $f) {
    if (!isset($body[$f]) || $body[$f] === '') {
        Response::error('Missing ' . $f, 400);
    }
}

$uid = $user->id;
$stmt = $db->prepare("INSERT INTO student_profiles(user_id, full_name, birth_date, address, phone, email, guardian_name, guardian_phone, is_complete) VALUES (:uid,:fn,:bd,:ad,:ph,:em,:gn,:gp,1) ON DUPLICATE KEY UPDATE full_name=:fn,birth_date=:bd,address=:ad,phone=:ph,email=:em,guardian_name=:gn,guardian_phone=:gp,is_complete=1");
$stmt->execute([
    ":uid" => $uid,
    ":fn" => $body['full_name'],
    ":bd" => $body['birth_date'],
    ":ad" => $body['address'],
    ":ph" => $body['phone'],
    ":em" => $body['email'],
    ":gn" => $body['guardian_name'],
    ":gp" => $body['guardian_phone']
]);

Response::message('OK');
