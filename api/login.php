<?php
// required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once '../config/database.php';
include_once '../config/jwt.php';
session_start();

$database = new Database();
$db = $database->getConnection();

$data = json_decode(file_get_contents("php://input"));

if(!empty($data->username) && !empty($data->password)){
    $stmt = $db->prepare("SELECT id, username, password, full_name, role, avatar FROM users WHERE username = :username LIMIT 1");
    $username = trim($data->username);
    $stmt->bindParam(':username', $username);
    $stmt->execute();
    if($stmt->rowCount() > 0){
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $input = (string)$data->password;
        $stored = (string)$row['password'];
        $isBcrypt = strpos($stored, '$2y$') === 0;
        $ok = $isBcrypt ? password_verify($input, $stored) : hash_equals($stored, $input);
        if($ok){
            // Trigger auto-check for expired red stars (lightweight)
            // In production, this should be a cron job. Here we do it on login to ensure consistency.
            if ($row['role'] == 'admin' || $row['role'] == 'teacher') {
                // Non-blocking approach not possible easily in simple PHP without exec, so we just run query
                $db->exec("UPDATE red_committee_members SET active = 0 WHERE active = 1 AND expired_at < CURDATE()");
            }

            $_SESSION['user_id'] = $row['id'];
            $_SESSION['username'] = $row['username'];
            $_SESSION['role'] = $row['role'];
            $jwt = new JWT();
            $token = $jwt->encode(['sub'=>$row['id'],'username'=>$row['username'],'role'=>$row['role']]);
            http_response_code(200);
            echo json_encode(array(
                "message" => "Login successful.",
                "token" => $token,
                "user" => array(
                    "id" => $row['id'],
                    "username" => $row['username'],
                    "full_name" => $row['full_name'],
                    "role" => $row['role'],
                    "avatar" => $row['avatar']
                )
            ));
        } else {
            http_response_code(401);
            echo json_encode(array("message" => "Login failed. Wrong password."));
        }
    } else {
        http_response_code(401);
        echo json_encode(array("message" => "Login failed. User not found."));
    }
} else {
    http_response_code(400);
    echo json_encode(array("message" => "Incomplete data."));
}
?>
