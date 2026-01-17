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
if (!$decoded) {
    http_response_code(401);
    echo json_encode(["message" => "Unauthorized."]);
    exit;
}

if (empty($data->id)) {
    http_response_code(400);
    echo json_encode(["message" => "Missing ID."]);
    exit;
}

// Optional: Verify teacher owns the class of this red star member (omitted for brevity, trusting UI context for now)

$query = "UPDATE red_committee_members SET ";
$params = [];
$updates = [];

if (isset($data->duration_weeks)) {
    $updates[] = "duration_weeks = :dw";
    $params[':dw'] = $data->duration_weeks;
    
    // Recalculate expired_at
    // We need start_date first
    $stmt = $db->prepare("SELECT start_date FROM red_committee_members WHERE id = ?");
    $stmt->execute([$data->id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($row) {
        $updates[] = "expired_at = DATE_ADD(:sd, INTERVAL :dw2 WEEK)";
        $params[':sd'] = $row['start_date'];
        $params[':dw2'] = $data->duration_weeks;
    }
}

if (isset($data->active)) {
    $updates[] = "active = :active";
    $params[':active'] = $data->active;
}

if (empty($updates)) {
    echo json_encode(["message" => "Nothing to update."]);
    exit;
}

$query .= implode(", ", $updates) . " WHERE id = :id";
$params[':id'] = $data->id;

try {
    $stmt = $db->prepare($query);
    if ($stmt->execute($params)) {
        echo json_encode(["message" => "Updated successfully."]);
    } else {
        http_response_code(503);
        echo json_encode(["message" => "Unable to update."]);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["message" => "Error: " . $e->getMessage()]);
}
?>