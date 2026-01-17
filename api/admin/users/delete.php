<?php
include_once '../../../config/database.php';
include_once '../../../config/jwt.php';
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
error_reporting(0);
ini_set('display_errors', '0');
$db = (new Database())->getConnection();
$data = json_decode(file_get_contents("php://input"));
$token = isset($_SERVER['HTTP_AUTHORIZATION']) ? str_replace('Bearer ', '', $_SERVER['HTTP_AUTHORIZATION']) : ($data->token ?? "");

// Fallback for Apache/others if HTTP_AUTHORIZATION is not set
if (empty($token) && function_exists('getallheaders')) {
    $headers = getallheaders();
    if (isset($headers['Authorization'])) {
        $token = str_replace('Bearer ', '', $headers['Authorization']);
    }
}
$decoded = validateJWT($token);
if (!$decoded || ($decoded['data']->role ?? '') !== 'admin') {
    http_response_code(401);
    echo json_encode(["message"=>"Unauthorized"], JSON_UNESCAPED_UNICODE);
    exit;
}
$userId = isset($data->id) ? intval($data->id) : 0;
if ($userId <= 0) {
    http_response_code(400);
    echo json_encode(["message"=>"Missing user id"], JSON_UNESCAPED_UNICODE);
    exit;
}
try {
    $db->beginTransaction();
    $stmt = $db->prepare("UPDATE users SET is_locked=1 WHERE id=:id");
    $stmt->execute([":id"=>$userId]);
    $db->commit();
    echo json_encode(["message"=>"User locked (soft-deleted) successfully"]);
} catch (Throwable $e) {
    $db->rollBack();
    http_response_code(500);
    echo json_encode(["message"=>"Server error"], JSON_UNESCAPED_UNICODE);
}
