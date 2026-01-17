<?php
require_once __DIR__ . '/../../bootstrap.php';

use App\Core\{Middleware, Response, Request, Bootstrap};

Middleware::cors('POST');
Middleware::requireAdmin();

$db = Bootstrap::db();
$body = Request::all();

$index = $body['index'] ?? 1; // 1..3
$text = $body['text'] ?? '';
$link = $body['link'] ?? '';

$db->prepare("INSERT INTO system_settings(setting_key,setting_value) VALUES (:k1,:v1) ON DUPLICATE KEY UPDATE setting_value=:v1")->execute([":k1" => "banner_text_" . $index, ":v1" => $text]);
$db->prepare("INSERT INTO system_settings(setting_key,setting_value) VALUES (:k2,:v2) ON DUPLICATE KEY UPDATE setting_value=:v2")->execute([":k2" => "banner_link_" . $index, ":v2" => $link]);

Response::message("OK");
