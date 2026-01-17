<?php
require_once __DIR__ . '/../../bootstrap.php';

use App\Core\{Middleware, Response, Request, Bootstrap};

Middleware::cors('POST');
Middleware::requireAdmin();

$db = Bootstrap::db();

$data = Request::all();
$role = isset($data['role']) ? trim($data['role']) : "";
$search = isset($data['search']) ? trim($data['search']) : "";
$page = isset($data['page']) ? intval($data['page']) : 1;
$limit = isset($data['limit']) ? intval($data['limit']) : 20;
$offset = ($page - 1) * $limit;
$strict = isset($data['strict']) ? (bool)$data['strict'] : false;

if (empty($role)) {
    Response::error("Role parameter is required.", 400);
}

// Build query
$query = "SELECT id, username, full_name, role, avatar, email, phone, is_locked, created_at
          FROM users
          WHERE role = :role";
if ($strict && $role === 'teacher') {
    $query .= " AND is_locked = 0 AND full_name <> '' AND (role='teacher' OR username LIKE 'GV-%')";
}

if (!empty($search)) {
    $query .= " AND (full_name LIKE :search OR username LIKE :search OR email LIKE :search OR phone LIKE :search)";
}

$query .= " ORDER BY id DESC LIMIT :limit OFFSET :offset";

$stmt = $db->prepare($query);
$stmt->bindParam(":role", $role);

if (!empty($search)) {
    $search_term = "%{$search}%";
    $stmt->bindParam(":search", $search_term);
}

$stmt->bindParam(":limit", $limit, PDO::PARAM_INT);
$stmt->bindParam(":offset", $offset, PDO::PARAM_INT);

try {
    $stmt->execute();
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Throwable $e) {
    Response::serverError("Server error");
}

// Get total count for pagination
$count_query = "SELECT COUNT(*) as total FROM users WHERE role = :role";
if ($strict && $role === 'teacher') {
    $count_query .= " AND is_locked = 0 AND full_name <> '' AND (role='teacher' OR username LIKE 'GV-%')";
}
if (!empty($search)) {
    $count_query .= " AND (full_name LIKE :search OR username LIKE :search OR email LIKE :search OR phone LIKE :search)";
}

$count_stmt = $db->prepare($count_query);
$count_stmt->bindParam(":role", $role);
if (!empty($search)) {
    $count_stmt->bindParam(":search", $search_term);
}
$count_stmt->execute();
$row = $count_stmt->fetch(PDO::FETCH_ASSOC);
$total_records = $row['total'];
$total_pages = ceil($total_records / $limit);

Response::paginated($users, $page, $total_pages, $total_records, $limit);
