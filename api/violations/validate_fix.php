<?php
require_once __DIR__ . '/../bootstrap.php';

use App\Core\{Middleware, Response, Bootstrap};

Middleware::cors('POST');
Middleware::requireAdmin();

$db = Bootstrap::db();

// Remove violations with missing rule
$stmt = $db->query("SELECT v.id FROM violations v LEFT JOIN conduct_rules r ON v.rule_id=r.id WHERE r.id IS NULL");
$toDelete = $stmt->fetchAll(PDO::FETCH_COLUMN);
if (!empty($toDelete)) {
    $in = implode(',', array_map('intval', $toDelete));
    $db->exec("DELETE FROM violations WHERE id IN ($in)");
}

// Ensure student_id references existing users(role=student)
$stmt2 = $db->query("SELECT v.id FROM violations v LEFT JOIN users u ON v.student_id=u.id WHERE u.id IS NULL OR u.role<>'student'");
$badStudents = $stmt2->fetchAll(PDO::FETCH_COLUMN);
$deletedStudents = 0;
if (!empty($badStudents)) {
    $in = implode(',', array_map('intval', $badStudents));
    $db->exec("DELETE FROM violations WHERE id IN ($in)");
    $deletedStudents = count($badStudents);
}

Response::success(['status' => 'ok', 'deleted_rules' => count($toDelete), 'deleted_students' => $deletedStudents]);
