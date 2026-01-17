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

if (empty($data->username) || empty($data->password) || empty($data->full_name) || empty($data->role)) {
    http_response_code(400);
    echo json_encode(["message" => "Incomplete data."]);
    exit;
}

if ($repo->existsUsername($data->username)) {
    http_response_code(400);
    echo json_encode(["message" => "Username already exists."]);
    exit;
}

if ($repo->createUser($data->username, $data->password, $data->full_name, $data->role, isset($data->email)?$data->email:null, isset($data->phone)?$data->phone:null)) {
    echo json_encode(["message" => "User created successfully."]);
} else {
    http_response_code(503);
    echo json_encode(["message" => "Unable to create user."]);
}
?>
