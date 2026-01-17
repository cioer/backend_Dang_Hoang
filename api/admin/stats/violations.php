<?php
require_once __DIR__ . '/../../bootstrap.php';

use App\Core\{Middleware, Response, Request, Bootstrap};

Middleware::cors('GET');
Middleware::auth();

$db = Bootstrap::db();

$type = Request::get('type', 'day'); // day, week, month

$query = "";
if ($type == 'day') {
    $query = "SELECT DATE(created_at) as label, COUNT(*) as count FROM violations GROUP BY DATE(created_at) ORDER BY DATE(created_at) DESC LIMIT 7";
} elseif ($type == 'week') {
    $query = "SELECT CONCAT(YEAR(created_at), '/', WEEK(created_at)) as label, COUNT(*) as count FROM violations GROUP BY YEAR(created_at), WEEK(created_at) ORDER BY YEAR(created_at) DESC, WEEK(created_at) DESC LIMIT 4";
} elseif ($type == 'month') {
    $query = "SELECT CONCAT(YEAR(created_at), '/', MONTH(created_at)) as label, COUNT(*) as count FROM violations GROUP BY YEAR(created_at), MONTH(created_at) ORDER BY YEAR(created_at) DESC, MONTH(created_at) DESC LIMIT 12";
} else {
    Response::error("Invalid type.", 400);
}

$stmt = $db->prepare($query);
$stmt->execute();

$data = [];
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $data[] = [
        "label" => $row['label'],
        "count" => $row['count']
    ];
}

Response::success($data);
