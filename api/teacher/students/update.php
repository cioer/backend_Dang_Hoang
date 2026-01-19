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

    // Update User full_name if provided
    if (isset($data['full_name']) && !empty($data['full_name'])) {
        $repo->updateUser($data['id'], ['full_name' => $data['full_name']]);
    }

    // Update Profile - only update fields that are provided
    if (isset($data['parent_name']) || isset($data['parent_phone']) || isset($data['full_name'])) {
        // Check if profile exists
        $checkProfile = $db->prepare("SELECT user_id, full_name FROM student_profiles WHERE user_id = ?");
        $checkProfile->execute([$data['id']]);
        $existingProfile = $checkProfile->fetch(PDO::FETCH_ASSOC);

        if ($existingProfile) {
            // Profile exists - only update provided fields
            $updateFields = [];
            $updateParams = [];

            if (isset($data['full_name']) && !empty($data['full_name'])) {
                $updateFields[] = "full_name = ?";
                $updateParams[] = $data['full_name'];
            }
            if (isset($data['parent_name'])) {
                $updateFields[] = "guardian_name = ?";
                $updateParams[] = $data['parent_name'];
            }
            if (isset($data['parent_phone'])) {
                $updateFields[] = "guardian_phone = ?";
                $updateParams[] = $data['parent_phone'];
            }

            if (!empty($updateFields)) {
                $updateParams[] = $data['id'];
                $stmt = $db->prepare("UPDATE student_profiles SET " . implode(", ", $updateFields) . " WHERE user_id = ?");
                $stmt->execute($updateParams);
            }
        } else {
            // Profile doesn't exist - need full_name to create
            $fullName = $data['full_name'] ?? null;
            if (empty($fullName)) {
                // Get full_name from users table
                $userStmt = $db->prepare("SELECT full_name FROM users WHERE id = ?");
                $userStmt->execute([$data['id']]);
                $userRow = $userStmt->fetch(PDO::FETCH_ASSOC);
                $fullName = $userRow['full_name'] ?? 'Unknown';
            }

            $stmt = $db->prepare("INSERT INTO student_profiles (user_id, full_name, guardian_name, guardian_phone) VALUES (?, ?, ?, ?)");
            $stmt->execute([
                $data['id'],
                $fullName,
                $data['parent_name'] ?? null,
                $data['parent_phone'] ?? null
            ]);
        }
    }

    $db->commit();
    Response::message('Student updated successfully.');

} catch (Exception $e) {
    $db->rollBack();
    Response::serverError('Error: ' . $e->getMessage());
}
