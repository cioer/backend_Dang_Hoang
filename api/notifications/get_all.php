<?php
require_once __DIR__ . '/../bootstrap.php';

use App\Core\{Middleware, Response, Bootstrap};

Middleware::cors('GET');

$user = Middleware::auth();
$db = Bootstrap::db();

// Get notifications for this specific user:
// 1. receiver_id matches user's id (targeted notification), OR
// 2. target_role matches user's role and receiver_id is NULL (broadcast to role), OR
// 3. target_role is 'all' and receiver_id is NULL (broadcast to all)
$query = "SELECT id, title, content, created_at, priority
          FROM notifications
          WHERE receiver_id = :user_id
             OR (receiver_id IS NULL AND (target_role = :role OR target_role = 'all'))
          ORDER BY created_at DESC
          LIMIT 100";
$stmt = $db->prepare($query);
$stmt->execute([':user_id' => $user->id, ':role' => $user->role]);

$notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);

Response::success($notifications);
