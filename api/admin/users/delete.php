<?php
require_once __DIR__ . '/../../bootstrap.php';

use App\Core\{Middleware, Response, Request, Bootstrap};

Middleware::cors('POST');
Middleware::requireAdmin();

$db = Bootstrap::db();
$data = Request::all();

$userId = isset($data['id']) ? intval($data['id']) : 0;
if ($userId <= 0) {
    Response::error("Missing user id", 400);
}

try {
    $db->beginTransaction();
    $stmt = $db->prepare("UPDATE users SET is_locked=1 WHERE id=:id");
    $stmt->execute([":id" => $userId]);
    $db->commit();
    Response::message("User locked (soft-deleted) successfully");
} catch (Throwable $e) {
    $db->rollBack();
    Response::serverError("Server error");
}
