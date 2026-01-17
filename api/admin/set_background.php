<?php
require_once __DIR__ . '/../bootstrap.php';

use App\Core\{Middleware, Response, Request, Bootstrap};

Middleware::cors('POST');
$user = Middleware::requireAdmin();

$db = Bootstrap::db();
$data = Request::all();

$target = $data['target'] ?? null;
$key = $data['image_key'] ?? null;

if (!$target || !$key || !in_array($target, ['mobile', 'pc'])) {
    Response::error("Invalid data", 400);
}

$setting_key = $target === 'mobile' ? 'bg_mobile' : 'bg_pc';
$stmt = $db->prepare("INSERT INTO system_settings (setting_key, setting_value) VALUES (:k,:v) ON DUPLICATE KEY UPDATE setting_value=:v");
$stmt->execute([':k' => $setting_key, ':v' => $key]);

// Audit log
$log = $db->prepare("INSERT INTO audit_logs (user_id, action, details, ip) VALUES (:uid, 'SET_BACKGROUND', :details, :ip)");
$ip = Request::ip();
$log->execute([':uid' => $user->id, ':details' => "{$setting_key}={$key}", ':ip' => $ip]);

Response::message("Saved");
