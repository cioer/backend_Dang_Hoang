<?php
include_once '../../../config/database.php';
include_once '../../../config/jwt.php';
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$headers = getallheaders();
$auth = $headers['Authorization'] ?? $headers['authorization'] ?? null;
if (!$auth || stripos($auth, 'Bearer ') !== 0) {
    http_response_code(401);
    echo json_encode(["message" => "Unauthorized"]);
    exit;
}
$token = substr($auth, 7);
$decoded = validateJwt($token);
if (!$decoded) {
    http_response_code(401);
    echo json_encode(["message" => "Invalid token"]);
    exit;
}

$database = new Database();
$db = $database->getConnection();

$type = isset($_GET['type']) ? $_GET['type'] : 'day'; // day, week, month

$query = "";
if ($type == 'day') {
    $query = "SELECT DATE(created_at) as label, COUNT(*) as count FROM violations GROUP BY DATE(created_at) ORDER BY DATE(created_at) DESC LIMIT 7";
} elseif ($type == 'week') {
    $query = "SELECT CONCAT(YEAR(created_at), '/', WEEK(created_at)) as label, COUNT(*) as count FROM violations GROUP BY YEAR(created_at), WEEK(created_at) ORDER BY YEAR(created_at) DESC, WEEK(created_at) DESC LIMIT 4";
} elseif ($type == 'month') {
    $query = "SELECT CONCAT(YEAR(created_at), '/', MONTH(created_at)) as label, COUNT(*) as count FROM violations GROUP BY YEAR(created_at), MONTH(created_at) ORDER BY YEAR(created_at) DESC, MONTH(created_at) DESC LIMIT 12";
} else {
    echo json_encode(array("message" => "Invalid type."));
    exit;
}

$stmt = $db->prepare($query);
$stmt->execute();

$num = $stmt->rowCount();
$data = array();

if($num > 0){
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
        extract($row);
        $item = array(
            "label" => $label,
            "count" => $count
        );
        array_push($data, $item);
    }
    echo json_encode($data);
} else {
    echo json_encode(array());
}
?>
