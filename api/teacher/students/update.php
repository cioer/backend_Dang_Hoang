<?php
require_once __DIR__ . '/../../bootstrap.php';

use App\Core\{Middleware, Response, Request, Bootstrap};

Middleware::cors('POST');
$user = Middleware::requireRole('teacher');
$db = Bootstrap::db();

// Load UserRepository
require_once __DIR__ . '/../../../lib/UserRepository.php';
$repo = new UserRepository($db);

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

    // Update User
    $fields = [];
    if (isset($data['full_name'])) $fields['full_name'] = $data['full_name'];

    if (!empty($fields)) {
        $repo->updateUser($data['id'], $fields);
    }

    // Update Profile
    if (isset($data['parent_name']) || isset($data['parent_phone']) || isset($data['full_name'])) {
        // Need to ensure profile exists
        $stmt = $db->prepare("INSERT INTO student_profiles (user_id, full_name, guardian_name, guardian_phone) VALUES (?, ?, ?, ?) ON DUPLICATE KEY UPDATE full_name = VALUES(full_name), guardian_name = VALUES(guardian_name), guardian_phone = VALUES(guardian_phone)");
        $stmt->execute([
            $data['id'],
            $data['full_name'] ?? "",
            $data['parent_name'] ?? "",
            $data['parent_phone'] ?? ""
        ]);
    }

    $db->commit();
    Response::message('Student updated successfully.');

} catch (Exception $e) {
    $db->rollBack();
    Response::serverError('Error: ' . $e->getMessage());
}
