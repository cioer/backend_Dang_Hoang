<?php
require_once __DIR__ . '/../../bootstrap.php';

use App\Core\{Middleware, Response, Request, Bootstrap};

Middleware::cors('GET');
Middleware::auth();

$db = Bootstrap::db();

$type = Request::get('type', 'day'); // day, week, month

$query = "";
if ($type == 'day') {
    $query = "SELECT DATE(created_at) as label, COUNT(*) as count
              FROM violations
              WHERE created_at IS NOT NULL
              GROUP BY label
              ORDER BY label DESC
              LIMIT 7";
} elseif ($type == 'week') {
    $query = "SELECT
                CONCAT(YEAR(created_at), '/', LPAD(WEEK(created_at, 1), 2, '0')) as label,
                COUNT(*) as count
              FROM violations
              WHERE created_at IS NOT NULL
              GROUP BY label
              ORDER BY label DESC
              LIMIT 8";
} elseif ($type == 'month') {
    $query = "SELECT
                CONCAT(YEAR(created_at), '/', LPAD(MONTH(created_at), 2, '0')) as label,
                COUNT(*) as count
              FROM violations
              WHERE created_at IS NOT NULL
              GROUP BY label
              ORDER BY label DESC
              LIMIT 12";
} else {
    Response::error("Invalid type.", 400);
}

$stmt = $db->prepare($query);
$stmt->execute();

$data = [];
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $data[] = [
        "label" => $row['label'],
        "count" => (int)$row['count']
    ];
}

Response::success($data);
