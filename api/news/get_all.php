<?php
require_once __DIR__ . '/../bootstrap.php';

use App\Core\{Middleware, Response, Bootstrap};

Middleware::cors('GET');

$db = Bootstrap::db();

$query = "SELECT id, title, summary, image_url, created_at FROM news ORDER BY created_at DESC";
$stmt = $db->prepare($query);
$stmt->execute();

$news_list = $stmt->fetchAll(PDO::FETCH_ASSOC);

Response::success($news_list);
