<?php
include_once '../../config/database.php';
include_once '../../config/jwt.php';

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET");

$database = new Database();
$db = $database->getConnection();

$query = "SELECT id, title, content, created_at 
          FROM notifications 
          ORDER BY created_at DESC";
$stmt = $db->prepare($query);
$stmt->execute();

$notifications = array();
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    array_push($notifications, $row);
}

http_response_code(200);
echo json_encode($notifications);
?>
