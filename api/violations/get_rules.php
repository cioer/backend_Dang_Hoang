<?php
include_once '../../config/database.php';
include_once '../../config/jwt.php';

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$headers = getallheaders();
$token = isset($headers['Authorization']) ? str_replace('Bearer ', '', $headers['Authorization']) : null;

if (!$token || !validateJWT($token)) {
    http_response_code(401);
    echo json_encode(["message" => "Unauthorized access."]);
    exit;
}

$database = new Database();
$db = $database->getConnection();

$query = "SELECT id, rule_name, points, description FROM conduct_rules ORDER BY id ASC";
$stmt = $db->prepare($query);
$stmt->execute();

$rules = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($rules);
?>