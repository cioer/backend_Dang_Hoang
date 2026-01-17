<?php
require_once __DIR__ . '/bootstrap.php';

use App\Core\{Middleware, Response, Request, Bootstrap};

Middleware::cors('POST');

$db = Bootstrap::db();
$jwt = Bootstrap::jwt();

$data = Request::all();

if (empty($data['username']) || empty($data['password'])) {
    Response::error('Incomplete data.', 400);
}

$stmt = $db->prepare("SELECT id, username, password, full_name, role, avatar FROM users WHERE username = :username LIMIT 1");
$username = trim($data['username']);
$stmt->bindParam(':username', $username);
$stmt->execute();

if ($stmt->rowCount() === 0) {
    Response::error('Login failed. User not found.', 401);
}

$row = $stmt->fetch(PDO::FETCH_ASSOC);
$input = (string)$data['password'];
$stored = (string)$row['password'];
$isBcrypt = strpos($stored, '$2y$') === 0;
$ok = $isBcrypt ? password_verify($input, $stored) : hash_equals($stored, $input);

if (!$ok) {
    Response::error('Login failed. Wrong password.', 401);
}

// Trigger auto-check for expired red stars
if ($row['role'] == 'admin' || $row['role'] == 'teacher') {
    $db->exec("UPDATE red_committee_members SET active = 0 WHERE active = 1 AND expired_at < CURDATE()");
}

session_start();
$_SESSION['user_id'] = $row['id'];
$_SESSION['username'] = $row['username'];
$_SESSION['role'] = $row['role'];

$token = $jwt->encode(['sub' => $row['id'], 'username' => $row['username'], 'role' => $row['role']]);

Response::success([
    "message" => "Login successful.",
    "token" => $token,
    "user" => [
        "id" => $row['id'],
        "username" => $row['username'],
        "full_name" => $row['full_name'],
        "role" => $row['role'],
        "avatar" => $row['avatar']
    ]
]);
