<?php
require_once __DIR__ . '/../bootstrap.php';

use App\Core\{Middleware, Response, Bootstrap};

Middleware::cors('GET');

$db = Bootstrap::db();

$query = "SELECT id, title, content, created_at
          FROM notifications
          ORDER BY created_at DESC";
$stmt = $db->prepare($query);
$stmt->execute();

$notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);

Response::success($notifications);
