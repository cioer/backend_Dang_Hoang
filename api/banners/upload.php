<?php
include_once '../../config/database.php';
include_once '../../config/jwt.php';

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");

$database = new Database();
$db = $database->getConnection();

$token = isset($_POST['token']) ? $_POST['token'] : (isset($_SERVER['HTTP_AUTHORIZATION']) ? str_replace('Bearer ', '', $_SERVER['HTTP_AUTHORIZATION']) : "");
$decoded = validateJWT($token);

if(!$decoded || $decoded->data->role != 'admin'){
    http_response_code(401);
    // Detailed error logging
    error_log("Banner upload unauthorized access attempt. Token valid: " . ($decoded ? "Yes" : "No") . ". Role: " . ($decoded ? $decoded->data->role : "N/A"));
    echo json_encode([
        "message" => "Unauthorized access. Admin privileges required.",
        "debug_info" => [
            "token_valid" => (bool)$decoded,
            "required_role" => "admin",
            "current_role" => $decoded ? $decoded->data->role : null
        ]
    ]);
    exit;
}

if(isset($_FILES['image'])){
    // 1. Validate File Size (Max 5MB)
    if ($_FILES['image']['size'] > 5 * 1024 * 1024) {
        http_response_code(400);
        echo json_encode(["message" => "File quá lớn. Tối đa 5MB."]);
        exit;
    }

    // 2. Validate MIME Type
    $allowed_types = ['image/jpeg', 'image/png', 'image/jpg'];
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime_type = finfo_file($finfo, $_FILES['image']['tmp_name']);
    finfo_close($finfo);

    if (!in_array($mime_type, $allowed_types)) {
        http_response_code(400);
        echo json_encode(["message" => "Định dạng file không hợp lệ. Chỉ chấp nhận JPG, PNG."]);
        exit;
    }

    $target_dir = "../../uploads/banners/";
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }
    
    $file_extension = pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION);
    $new_filename = uniqid() . '.' . $file_extension;
    $target_file = $target_dir . $new_filename;
    
    if(move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)){
        $image_url = "uploads/banners/" . $new_filename;
        $title = isset($_POST['title']) ? $_POST['title'] : "";
        $cta_text = isset($_POST['cta_text']) ? $_POST['cta_text'] : "Xem ngay";
        $link_url = isset($_POST['link_url']) ? $_POST['link_url'] : "";
        $priority = isset($_POST['priority']) ? (int)$_POST['priority'] : 0;
        
        $query = "INSERT INTO banners SET image_url=:image_url, title=:title, cta_text=:cta_text, link_url=:link_url, is_active=1, priority=:priority";
        $stmt = $db->prepare($query);
        
        $stmt->bindParam(":image_url", $image_url);
        $stmt->bindParam(":title", $title);
        $stmt->bindParam(":cta_text", $cta_text);
        $stmt->bindParam(":link_url", $link_url);
        $stmt->bindParam(":priority", $priority);
                
                if($stmt->execute()){
                    // Log the action
                    $log_query = "INSERT INTO banner_logs (admin_id, action, banner_info) VALUES (:admin_id, 'UPLOAD', :info)";
                    $log_stmt = $db->prepare($log_query);
                    $admin_id = $decoded['data']->id;
                    $info = "Uploaded banner: " . $title;
                    $log_stmt->bindParam(":admin_id", $admin_id);
                    $log_stmt->bindParam(":info", $info);
                    $log_stmt->execute();

                    echo json_encode(["message" => "Banner uploaded successfully"]);
                } else {
            http_response_code(500);
            echo json_encode(["message" => "Database error"]);
        }
    } else {
        http_response_code(500);
        echo json_encode(["message" => "File upload failed"]);
    }
} else {
    http_response_code(400);
    echo json_encode(["message" => "No image file provided"]);
}
?>