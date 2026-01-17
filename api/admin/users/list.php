<?php
include_once '../../../config/database.php';
include_once '../../../config/jwt.php';

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
error_reporting(0);
ini_set('display_errors', '0');

$database = new Database();
$db = $database->getConnection();

$data = json_decode(file_get_contents("php://input"));
$token = isset($data->token) ? $data->token : (isset($_SERVER['HTTP_AUTHORIZATION']) ? str_replace('Bearer ', '', $_SERVER['HTTP_AUTHORIZATION']) : "");

// Fallback for Apache/others if HTTP_AUTHORIZATION is not set
if (empty($token) && function_exists('getallheaders')) {
    $headers = getallheaders();
    if (isset($headers['Authorization'])) {
        $token = str_replace('Bearer ', '', $headers['Authorization']);
    }
}

$decoded = validateJWT($token);
if (!$decoded) {
    http_response_code(401);
    echo json_encode(["message" => "Unauthorized access. Invalid Token."], JSON_UNESCAPED_UNICODE);
    exit;
}
if ($decoded['data']->role != 'admin') {
    http_response_code(401);
    echo json_encode(["message" => "Unauthorized access. Role is " . $decoded['data']->role], JSON_UNESCAPED_UNICODE);
    exit;
}

$role = isset($data->role) ? trim($data->role) : "";
$search = isset($data->search) ? trim($data->search) : "";
$page = isset($data->page) ? intval($data->page) : 1;
$limit = isset($data->limit) ? intval($data->limit) : 20;
$offset = ($page - 1) * $limit;
$strict = isset($data->strict) ? (bool)$data->strict : false;

if (empty($role)) {
    http_response_code(400);
    echo json_encode(["message" => "Role parameter is required."], JSON_UNESCAPED_UNICODE);
    exit;
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
    http_response_code(500);
    echo json_encode(["message" => "Server error"], JSON_UNESCAPED_UNICODE);
    exit;
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

echo json_encode([
    "data" => $users,
    "pagination" => [
        "current_page" => $page,
        "total_pages" => $total_pages,
        "total_records" => $total_records,
        "limit" => $limit
    ]
], JSON_UNESCAPED_UNICODE);
?>
