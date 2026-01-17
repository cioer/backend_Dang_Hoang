<?php
include_once '../../config/database.php';
include_once '../../config/jwt.php';
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
$headers = getallheaders();
$headerToken = isset($headers['Authorization']) ? str_replace('Bearer ', '', $headers['Authorization']) : null;
$postToken = isset($_POST['token']) ? $_POST['token'] : null;
$token = $headerToken ?: $postToken;
$decoded = validateJWT($token);
if (!$decoded || ($decoded['data']->role != 'admin')) { http_response_code(403); echo json_encode(["message"=>"Forbidden"]); exit; }
$target = isset($_POST['target']) ? $_POST['target'] : null;
if (!$target || !in_array($target, ['mobile','pc','sub1','sub2','sub3'])) { http_response_code(400); echo json_encode(["message"=>"Invalid target"]); exit; }
if (!isset($_FILES['image'])) { http_response_code(400); echo json_encode(["message"=>"No file"]); exit; }
$file = $_FILES['image'];
if ($file['error'] !== UPLOAD_ERR_OK) { http_response_code(400); echo json_encode(["message"=>"Upload error"]); exit; }
$allowed = ['image/jpeg'=>'jpg','image/png'=>'png','image/webp'=>'webp'];
$finfo = finfo_open(FILEINFO_MIME_TYPE);
$mime = finfo_file($finfo, $file['tmp_name']);
if (!isset($allowed[$mime])) { http_response_code(400); echo json_encode(["message"=>"Invalid type"]); exit; }
$ext = $allowed[$mime];
$dir = __DIR__ . '/../../uploads/backgrounds';
if (!is_dir($dir)) { mkdir($dir, 0775, true); }
$name = 'bg_' . $target . '_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
$path = $dir . '/' . $name;
if (!move_uploaded_file($file['tmp_name'], $path)) { http_response_code(500); echo json_encode(["message"=>"Save failed"]); exit; }
$db = (new Database())->getConnection();
$setting_key = $target === 'mobile' ? 'bg_mobile' : ($target === 'pc' ? 'bg_pc' : ($target === 'sub1' ? 'bg_sub1' : ($target === 'sub2' ? 'bg_sub2' : ($target === 'sub3' ? 'bg_sub3' : null))));
if ($setting_key === null) { http_response_code(400); echo json_encode(["message"=>"Invalid target"]); exit; }
$stmt = $db->prepare("INSERT INTO system_settings (setting_key, setting_value) VALUES (:k,:v) ON DUPLICATE KEY UPDATE setting_value=:v");
$stmt->execute([':k'=>$setting_key, ':v'=>$name]);
// Build absolute public URL
$scheme = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'];
$publicUrl = $scheme . '://' . $host . '/Backend/uploads/backgrounds/' . $name;
echo json_encode(["message"=>"Uploaded","file"=>$name,"url"=>$publicUrl]);
?>
