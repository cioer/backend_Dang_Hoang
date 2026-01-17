<?php
include_once '../../config/database.php';
include_once '../../config/jwt.php';

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");

$database = new Database();
$db = $database->getConnection();

$data = json_decode(file_get_contents("php://input"));
$token = isset($data->token) ? $data->token : (isset($_SERVER['HTTP_AUTHORIZATION']) ? str_replace('Bearer ', '', $_SERVER['HTTP_AUTHORIZATION']) : "");
$decoded = validateJWT($token);

if(!$decoded || $decoded->data->role != 'admin'){
    http_response_code(401);
    echo json_encode(["message" => "Unauthorized"]);
    exit;
}

if(isset($data->id)){
    $query = "DELETE FROM banners WHERE id = :id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(":id", $data->id);
    
    if($stmt->execute()){
        // Log the action
        $log_query = "INSERT INTO banner_logs (admin_id, action, banner_info) VALUES (:admin_id, 'DELETE', :info)";
        $log_stmt = $db->prepare($log_query);
        $admin_id = $decoded['data']->id;
        $info = "Deleted banner ID: " . $data->id;
        $log_stmt->bindParam(":admin_id", $admin_id);
        $log_stmt->bindParam(":info", $info);
        $log_stmt->execute();

        echo json_encode(["message" => "Banner deleted"]);
    } else {
        http_response_code(500);
        echo json_encode(["message" => "Error deleting banner"]);
    }
}
?>