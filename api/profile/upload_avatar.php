<?php
include_once '../../config/database.php';
include_once '../../config/jwt.php';
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
$headers = getallheaders();
$token = isset($headers['Authorization']) ? str_replace('Bearer ', '', $headers['Authorization']) : null;
$decoded = validateJWT($token);
if (!$decoded) { http_response_code(401); echo json_encode(["message"=>"Unauthorized"]); exit; }
if (!isset($_FILES['image'])) { http_response_code(400); echo json_encode(["message"=>"No file"]); exit; }
$file = $_FILES['image'];
$allowed = ['image/jpeg'=>'jpg','image/png'=>'png','image/webp'=>'webp'];
$finfo = finfo_open(FILEINFO_MIME_TYPE);
$mime = finfo_file($finfo, $file['tmp_name']);
if (!isset($allowed[$mime])) { http_response_code(400); echo json_encode(["message"=>"Invalid type"]); exit; }
$dir = __DIR__ . '/../../uploads/avatars';
if (!is_dir($dir)) { mkdir($dir, 0775, true); }
$name = 'avatar_' . $decoded['data']->id . '_' . time() . '.' . $allowed[$mime];
$path = $dir . '/' . $name;
if (!move_uploaded_file($file['tmp_name'], $path)) { http_response_code(500); echo json_encode(["message"=>"Save failed"]); exit; }
$db = (new Database())->getConnection();
$upd = $db->prepare("UPDATE users SET avatar=:av WHERE id=:id");
$upd->execute([':av'=>$name, ':id'=>$decoded['data']->id]);
$scheme = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'];
$url = $scheme . '://' . $host . '/Backend/uploads/avatars/' . $name;
echo json_encode(['message'=>'Uploaded','url'=>$url,'file'=>$name]);
?>
