<?php
require_once __DIR__ . '/../bootstrap.php';

use App\Core\{Middleware, Response, Bootstrap};

Middleware::cors('GET');

$db = Bootstrap::db();
$stmt = $db->query("SELECT id, name FROM subjects ORDER BY name ASC");

Response::success($stmt->fetchAll(PDO::FETCH_ASSOC));
