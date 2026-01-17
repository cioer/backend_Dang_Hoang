<?php
require_once __DIR__ . '/../../bootstrap.php';
require_once __DIR__ . '/../../../lib/UserRepository.php';

use App\Core\{Middleware, Response, Request, Bootstrap};

Middleware::cors('POST');
Middleware::requireAdmin();

$db = Bootstrap::db();
$repo = new UserRepository($db);
$data = Request::all();

if (empty($data['id'])) {
    Response::error("User ID required.", 400);
}

$stmtCur = $db->prepare("SELECT id, username, role FROM users WHERE id=:id LIMIT 1");
$stmtCur->execute([":id" => intval($data['id'])]);
$current = $stmtCur->fetch(PDO::FETCH_ASSOC);
if (!$current) {
    Response::notFound("User not found");
}

$usernamePrefix = substr($current['username'], 0, 3);
$requiredRole = null;
if ($usernamePrefix === 'GV-') $requiredRole = 'teacher';
elseif ($usernamePrefix === 'HS-') $requiredRole = 'student';
elseif ($usernamePrefix === 'PH-') $requiredRole = 'parent';

$fields = [];
if (isset($data['full_name']) && trim($data['full_name']) !== "") { $fields["full_name"] = $data['full_name']; }
if (isset($data['email']) && trim((string)$data['email']) !== "") { $fields["email"] = $data['email']; }
if (isset($data['phone']) && trim((string)$data['phone']) !== "") { $fields["phone"] = $data['phone']; }
if (isset($data['role']) && trim((string)$data['role']) !== "") { $fields["role"] = $data['role']; }
if (!empty($data['password'])) { $fields["password"] = $data['password']; }

if ($requiredRole !== null) {
    if (isset($fields["role"]) && $fields["role"] !== $requiredRole) {
        Response::error("Role does not match username prefix", 400);
    }
    $fields["role"] = $requiredRole;
}

if ($repo->updateUser(intval($data['id']), $fields)) {
    Response::message("User updated successfully.");
} else {
    Response::error("Unable to update user.", 503);
}
