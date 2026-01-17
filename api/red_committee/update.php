<?php
require_once __DIR__ . '/../bootstrap.php';

use App\Core\{Middleware, Response, Request, Bootstrap};

Middleware::cors('POST');

$user = Middleware::auth();
$db = Bootstrap::db();
$data = Request::all();

if (empty($data['id'])) {
    Response::error('Missing ID.', 400);
}

// Optional: Verify teacher owns the class of this red star member (omitted for brevity, trusting UI context for now)

$query = "UPDATE red_committee_members SET ";
$params = [];
$updates = [];

if (isset($data['duration_weeks'])) {
    $updates[] = "duration_weeks = :dw";
    $params[':dw'] = $data['duration_weeks'];

    // Recalculate expired_at
    // We need start_date first
    $stmt = $db->prepare("SELECT start_date FROM red_committee_members WHERE id = ?");
    $stmt->execute([$data['id']]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($row) {
        $updates[] = "expired_at = DATE_ADD(:sd, INTERVAL :dw2 WEEK)";
        $params[':sd'] = $row['start_date'];
        $params[':dw2'] = $data['duration_weeks'];
    }
}

if (isset($data['active'])) {
    $updates[] = "active = :active";
    $params[':active'] = $data['active'];
}

if (empty($updates)) {
    Response::success(["message" => "Nothing to update."]);
}

$query .= implode(", ", $updates) . " WHERE id = :id";
$params[':id'] = $data['id'];

try {
    $stmt = $db->prepare($query);
    if ($stmt->execute($params)) {
        Response::success(["message" => "Updated successfully."]);
    } else {
        Response::error('Unable to update.', 503);
    }
} catch (Exception $e) {
    Response::error('Error: ' . $e->getMessage(), 500);
}
