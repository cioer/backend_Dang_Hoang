<?php
require_once __DIR__ . '/../../bootstrap.php';

use App\Core\{Middleware, Response, Request, Bootstrap};

Middleware::cors('POST');
Middleware::requireAdmin();

$db = Bootstrap::db();
$body = Request::all();

$warn = isset($body['warn']) ? (int)$body['warn'] : null;
$conduct = isset($body['conduct']) ? (int)$body['conduct'] : null;
$class_change = isset($body['class_change']) ? (int)$body['class_change'] : null;
$class_name = isset($body['class_name']) ? $body['class_name'] : null;

if ($warn !== null) {
    $db->prepare("INSERT INTO system_settings(setting_key,setting_value) VALUES ('discipline_threshold_warn', :v) ON DUPLICATE KEY UPDATE setting_value=:v")->execute([":v" => $warn]);
}
if ($conduct !== null) {
    $db->prepare("INSERT INTO system_settings(setting_key,setting_value) VALUES ('discipline_threshold_conduct', :v) ON DUPLICATE KEY UPDATE setting_value=:v")->execute([":v" => $conduct]);
}
if ($class_change !== null) {
    $db->prepare("INSERT INTO system_settings(setting_key,setting_value) VALUES ('discipline_threshold_class_change', :v) ON DUPLICATE KEY UPDATE setting_value=:v")->execute([":v" => $class_change]);
}
if ($class_name !== null) {
    $db->prepare("INSERT INTO system_settings(setting_key,setting_value) VALUES ('discipline_class_name', :v) ON DUPLICATE KEY UPDATE setting_value=:v")->execute([":v" => $class_name]);
}

Response::message("OK");
