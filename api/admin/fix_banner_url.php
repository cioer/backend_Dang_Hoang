<?php
require_once __DIR__ . '/../bootstrap.php';

use App\Core\{Middleware, Response, Bootstrap};

// This is a one-time migration script, no CORS/auth needed for internal use
// But we add minimal headers for consistency
Middleware::cors('GET');

$db = Bootstrap::db();

try {
    // Change link_url to TEXT to support long URLs (up to 65,535 chars)
    $sql = "ALTER TABLE banners MODIFY link_url TEXT";
    $db->exec($sql);
    Response::message("Successfully updated 'link_url' column to TEXT type.");
} catch (PDOException $e) {
    Response::error("Error updating table: " . $e->getMessage(), 500);
}
