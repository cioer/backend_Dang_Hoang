<?php
require_once __DIR__ . '/../bootstrap.php';

use App\Core\{Middleware, Response, Bootstrap};

Middleware::cors('GET');

$db = Bootstrap::db();

$query = "SELECT id, title, summary, image_url, created_at FROM news ORDER BY created_at DESC LIMIT 5";
$stmt = $db->prepare($query);
$stmt->execute();

$news_list = array();
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $news_list[] = [
        "id" => $row['id'],
        "title" => $row['title'],
        "summary" => $row['summary'],
        "image_url" => $row['image_url'],
        "created_at" => $row['created_at']
    ];
}

Response::success($news_list);
