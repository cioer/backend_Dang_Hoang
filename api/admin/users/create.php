<?php
require_once __DIR__ . '/../../bootstrap.php';
require_once __DIR__ . '/../../../lib/UserRepository.php';

use App\Core\{Middleware, Response, Request, Bootstrap};

Middleware::cors('POST');
Middleware::requireAdmin();

$db = Bootstrap::db();
$repo = new UserRepository($db);
$data = Request::all();

if (empty($data['username']) || empty($data['password']) || empty($data['full_name']) || empty($data['role'])) {
    Response::error("Incomplete data.", 400);
}

if ($repo->existsUsername($data['username'])) {
    Response::error("Username already exists.", 400);
}

if ($repo->createUser($data['username'], $data['password'], $data['full_name'], $data['role'], $data['email'] ?? null, $data['phone'] ?? null)) {
    Response::message("User created successfully.");
} else {
    Response::error("Unable to create user.", 503);
}
