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

// Check ownership - teacher must be homeroom teacher, assigned teacher, or have schedule for this class
$checkClass = $db->prepare("
    SELECT DISTINCT c.id
    FROM classes c
    LEFT JOIN schedule s ON c.id = s.class_id
    LEFT JOIN class_teacher_assignments cta ON c.id = cta.class_id
    WHERE c.id = ?
      AND (c.homeroom_teacher_id = ? OR s.teacher_id = ? OR cta.teacher_id = ?)
");
$checkClass->execute([$data['class_id'], $user->id, $user->id, $user->id]);
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
