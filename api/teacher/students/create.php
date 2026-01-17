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

if (empty($data['full_name']) || empty($data['username']) || empty($data['password']) || empty($data['class_id'])) {
    Response::error('Missing data.', 400);
}

// Check if teacher owns the class
$check = $db->prepare("SELECT id FROM classes WHERE id = ? AND teacher_id = ?");
$check->execute([$data['class_id'], $user->id]);
if ($check->rowCount() == 0) {
    Response::forbidden('You are not assigned to this class.');
}

// Create User
if ($repo->existsUsername($data['username'])) {
    Response::error('Username already exists.', 409);
}

try {
    $db->beginTransaction();

    // 1. Create User
    $userId = $repo->createUserReturnId($data['username'], $data['password'], $data['full_name'], 'student', null, null);
    if (!$userId) {
        throw new Exception("Failed to create user.");
    }

    // 2. Assign to Class (using class_registrations)
    $stmt = $db->prepare("INSERT INTO class_registrations (class_id, student_id) VALUES (?, ?)");
    $stmt->execute([$data['class_id'], $userId]);

    // 3. Update Profile (using student_profiles)
    if (!empty($data['parent_name']) || !empty($data['parent_phone'])) {
        $stmt = $db->prepare("INSERT INTO student_profiles (user_id, full_name, guardian_name, guardian_phone) VALUES (?, ?, ?, ?) ON DUPLICATE KEY UPDATE guardian_name = VALUES(guardian_name), guardian_phone = VALUES(guardian_phone)");
        $stmt->execute([
            $userId,
            $data['full_name'],
            $data['parent_name'] ?? null,
            $data['parent_phone'] ?? null
        ]);
    }

    $db->commit();
    Response::success(['message' => 'Student created successfully.', 'id' => $userId]);

} catch (Exception $e) {
    $db->rollBack();
    Response::serverError('Error: ' . $e->getMessage());
}
