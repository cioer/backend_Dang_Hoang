<?php
include_once '../../../config/database.php';
include_once '../../../config/jwt.php';
include_once '../../../lib/UserRepository.php';

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");

$database = new Database();
$db = $database->getConnection();
$repo = new UserRepository($db);

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
if (!$decoded || $decoded['data']->role != 'admin') {
    http_response_code(401);
    echo json_encode(["message" => "Unauthorized access."]);
    exit;
}

if (empty($data->id)) {
    http_response_code(400);
    echo json_encode(["message" => "User ID required."]);
    exit;
}

$stmtCur = $db->prepare("SELECT id, username, role FROM users WHERE id=:id LIMIT 1");
$stmtCur->execute([":id"=>intval($data->id)]);
$current = $stmtCur->fetch(PDO::FETCH_ASSOC);
if (!$current) {
    http_response_code(404);
    echo json_encode(["message"=>"User not found"]);
    exit;
}
$usernamePrefix = substr($current['username'], 0, 3);
$requiredRole = null;
if ($usernamePrefix === 'GV-') $requiredRole = 'teacher';
elseif ($usernamePrefix === 'HS-') $requiredRole = 'student';
elseif ($usernamePrefix === 'PH-') $requiredRole = 'parent';

$fields = [];
if (isset($data->full_name) && trim($data->full_name) !== "") { $fields["full_name"] = $data->full_name; }
if (isset($data->email) && trim((string)$data->email) !== "") { $fields["email"] = $data->email; }
if (isset($data->phone) && trim((string)$data->phone) !== "") { $fields["phone"] = $data->phone; }
if (isset($data->role) && trim((string)$data->role) !== "") { $fields["role"] = $data->role; }
if (!empty($data->password)) { $fields["password"] = $data->password; }

if ($requiredRole !== null) {
    if (isset($fields["role"]) && $fields["role"] !== $requiredRole) {
        http_response_code(400);
        echo json_encode(["message"=>"Role does not match username prefix"]);
        exit;
    }
    $fields["role"] = $requiredRole;
}

if ($repo->updateUser(intval($data->id), $fields)) {
    echo json_encode(["message" => "User updated successfully."]);
} else {
    http_response_code(503);
    echo json_encode(["message" => "Unable to update user."]);
}
?>
