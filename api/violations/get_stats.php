<?php
include_once '../../config/database.php';
include_once '../../config/jwt.php';
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
$headers = getallheaders();
$token = isset($headers['Authorization']) ? str_replace('Bearer ', '', $headers['Authorization']) : null;
if (!$token || !validateJWT($token)) { http_response_code(401); echo json_encode(["message"=>"Unauthorized"]); exit; }

$database = new Database();
$db = $database->getConnection();

if (!$db) {
    http_response_code(500);
    echo json_encode(["message" => "Database connection failed"]);
    exit;
}

try {
    $classStats = $db->query(
        "SELECT c.name AS class, COUNT(v.id) AS violations 
         FROM violations v 
         JOIN student_details sd ON sd.user_id = v.student_id 
         JOIN classes c ON c.id = sd.class_id 
         GROUP BY c.name ORDER BY c.name"
    )->fetchAll(PDO::FETCH_ASSOC);
    
    $monthlyStats = $db->query(
        "SELECT DATE_FORMAT(v.created_at,'%Y-%m') AS month, COUNT(*) AS violations 
         FROM violations v 
         GROUP BY DATE_FORMAT(v.created_at,'%Y-%m') 
         ORDER BY month DESC"
    )->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode(["by_class" => $classStats, "by_month" => $monthlyStats]);
} catch (PDOException $e) {
    error_log("Stats query error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(["message" => "Error fetching statistics"]);
    exit;
}
