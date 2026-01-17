<?php
include_once '../../config/database.php';
include_once '../../config/jwt.php';

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

$headers = getallheaders();
$token = isset($headers['Authorization']) ? str_replace('Bearer ', '', $headers['Authorization']) : null;
$decoded = $token ? validateJWT($token) : null;

if (!$decoded) {
    http_response_code(401);
    echo json_encode(["complete" => false, "message" => "Unauthorized"]);
    exit;
}

$userId = $decoded['data']->id;
$role = $decoded['data']->role;
$db = (new Database())->getConnection();

// Get user info
$stmt = $db->prepare("SELECT full_name, email, phone, avatar FROM users WHERE id = :id LIMIT 1");
$stmt->execute([':id' => $userId]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    echo json_encode(["complete" => false]);
    exit;
}

$isComplete = true;

// Basic checks
if (empty($user['full_name'])) $isComplete = false;
if (empty($user['phone'])) $isComplete = false;
// Email is optional but recommended. For "is_new_account" logic, phone is key.

// For parent, check if they have updated the default name
if ($role === 'parent' && strpos($user['full_name'], 'Phụ huynh em ') === 0) {
    // If name still starts with "Phụ huynh em " (default), consider incomplete
    // However, some parents might keep it. Let's rely on phone.
    // Actually, phone is better indicator.
}

echo json_encode([
    "complete" => $isComplete,
    "user" => $user
]);
?>