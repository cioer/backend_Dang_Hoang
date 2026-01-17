<?php
require_once __DIR__ . '/../bootstrap.php';

use App\Core\{Middleware, Response, Bootstrap};

Middleware::cors('GET');
Middleware::auth();

$db = Bootstrap::db();

$query = "SELECT id, rule_name, points, description FROM conduct_rules ORDER BY id ASC";
$stmt = $db->prepare($query);
$stmt->execute();

$rules = $stmt->fetchAll(PDO::FETCH_ASSOC);

Response::success($rules);
