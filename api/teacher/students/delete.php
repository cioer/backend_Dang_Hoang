<?php
require_once __DIR__ . '/../../bootstrap.php';

use App\Core\{Middleware, Response, Request, Bootstrap};

Middleware::cors('POST');
$user = Middleware::requireRole('teacher');
$db = Bootstrap::db();

$data = Request::all();

if (empty($data['id']) || empty($data['class_id'])) {
    Response::error('Missing data.', 400);
}

// Check ownership
$checkClass = $db->prepare("SELECT id FROM classes WHERE id = ? AND teacher_id = ?");
$checkClass->execute([$data['class_id'], $user->id]);
if ($checkClass->rowCount() == 0) {
    Response::forbidden('You are not assigned to this class.');
}

$checkStudent = $db->prepare("SELECT id FROM class_registrations WHERE class_id = ? AND student_id = ?");
$checkStudent->execute([$data['class_id'], $data['id']]);
if ($checkStudent->rowCount() == 0) {
    Response::notFound('Student not found in this class.');
}

try {
    $db->beginTransaction();

    // Remove from class_registrations only
    $stmt = $db->prepare("DELETE FROM class_registrations WHERE class_id = ? AND student_id = ?");
    $stmt->execute([$data['class_id'], $data['id']]);

    // Soft lock user instead of hard delete
    $stmt = $db->prepare("UPDATE users SET is_locked=1 WHERE id = ?");
    $stmt->execute([$data['id']]);

    $db->commit();
    Response::message('Student deleted successfully.');

} catch (Exception $e) {
    $db->rollBack();
    Response::serverError('Error: ' . $e->getMessage());
}
