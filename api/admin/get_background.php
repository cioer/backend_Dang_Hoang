<?php
include_once '../../config/database.php';
include_once '../../config/jwt.php';
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
$headers = getallheaders();
$token = isset($headers['Authorization']) ? str_replace('Bearer ', '', $headers['Authorization']) : null;
$decoded = validateJWT($token);
if (!$decoded) { http_response_code(401); echo json_encode(["message"=>"Unauthorized"]); exit; }

$db = (new Database())->getConnection();
$q = $db->prepare("SELECT setting_key, setting_value FROM system_settings WHERE setting_key IN ('bg_mobile','bg_pc','bg_sub1','bg_sub2','bg_sub3','banner_text_1','banner_text_2','banner_text_3','banner_link_1','banner_link_2','banner_link_3')");
$q->execute();

$res = ['bg_mobile'=>null,'bg_pc'=>null,'bg_sub1'=>null,'bg_sub2'=>null,'bg_sub3'=>null];

// Helper to get fallback URL
function getFallback($k) {
    $scheme = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'];
    $fallbacks = [
        'bg_mobile' => $scheme . '://' . $host . '/Backend/api/admin/static_bg.php?size=1080x1920&text=bg_mobile',
        'bg_pc' => $scheme . '://' . $host . '/Backend/api/admin/static_bg.php?size=1920x1080&text=bg_pc',
        'bg_sub1' => $scheme . '://' . $host . '/Backend/api/admin/static_bg.php?size=800x400&text=sub1',
        'bg_sub2' => $scheme . '://' . $host . '/Backend/api/admin/static_bg.php?size=800x400&text=sub2',
        'bg_sub3' => $scheme . '://' . $host . '/Backend/api/admin/static_bg.php?size=800x400&text=sub3'
    ];
    return isset($fallbacks[$k]) ? $fallbacks[$k] : $scheme . '://' . $host . '/Backend/api/admin/static_bg.php?size=800x400&text=bg';
}

// Fill from DB
while ($row = $q->fetch(PDO::FETCH_ASSOC)) {
    $k = $row['setting_key'];
    $raw = $row['setting_value'];
    $val = $raw;
    if (!$val || strpos($val, 'http') !== 0) {
        $filename = $raw;
        if ($filename && !preg_match('/\.(png|jpg|jpeg|webp)$/i', $filename)) {
            $filename .= '.png';
        }
        $baseDir = dirname(__DIR__, 2) . '/uploads/backgrounds/';
        $filePath = $baseDir . $filename;
        if ($filename && file_exists($filePath)) {
            $scheme = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? 'https' : 'http';
            $host = $_SERVER['HTTP_HOST'];
            $val = $scheme . '://' . $host . '/Backend/uploads/backgrounds/' . $filename;
        } else {
            $val = getFallback($k);
        }
    }
    $res[$k] = $val;
}

// Fill missing keys with fallbacks
foreach ($res as $k => $v) {
    if ($v === null) {
        $res[$k] = getFallback($k);
    }
}

echo json_encode($res);
?>
