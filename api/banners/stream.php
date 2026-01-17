<?php
// Simple SSE endpoint that streams active banners changes
require_once __DIR__ . '/../bootstrap.php';

use App\Core\Bootstrap;

header('Content-Type: text/event-stream');
header('Cache-Control: no-cache');
header('Connection: keep-alive');
header('Access-Control-Allow-Origin: *');
header('X-Accel-Buffering: no');
@ini_set('zlib.output_compression', '0');
@ini_set('output_buffering', 'off');
set_time_limit(0);
while (ob_get_level() > 0) { @ob_end_flush(); }

$db = Bootstrap::db();

function getActiveBannersHash($db) {
    $query = "SELECT id, image_url, title, cta_text, link_url FROM banners WHERE is_active = 1 ORDER BY priority DESC, created_at DESC";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return [md5(json_encode($rows)), $rows];
}

$lastHash = null;
$start = microtime(true);
$lastCheck = 0.0;
$lastHeartbeat = 0.0;

while (true) {
    $now = microtime(true);
    if (($now - $lastCheck) >= 5.0) {
        $lastCheck = $now;
        list($hash, $data) = getActiveBannersHash($db);
        if ($hash !== $lastHash) {
            $lastHash = $hash;
            echo "event: banners\n";
            echo "data: " . json_encode($data) . "\n\n";
            @ob_flush();
            @flush();
        }
    }
    // send heartbeat every 15s to keep connection alive
    if (($now - $lastHeartbeat) >= 15.0) {
        $lastHeartbeat = $now;
        echo ": heartbeat\n\n";
        @ob_flush();
        @flush();
    }
    usleep(500000);
}
