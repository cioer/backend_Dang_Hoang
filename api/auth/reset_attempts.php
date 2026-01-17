<?php
require_once __DIR__ . '/../bootstrap.php';

use App\Core\{Middleware, Response, Request, Bootstrap};

Middleware::cors('POST');

$db = Bootstrap::db();
Middleware::requireAdmin();

$data = Request::all();
$username = isset($data['username']) ? trim($data['username']) : null;

if (!$username) {
    Response::error('Thiếu username', 400);
}

$stmt = $db->prepare("DELETE FROM login_attempts WHERE username=:u");
$ok = $stmt->execute([":u" => $username]);

Response::success(["ok" => $ok, "username" => $username]);
