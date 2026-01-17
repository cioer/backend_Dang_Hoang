<?php
require_once __DIR__ . '/../bootstrap.php';

use App\Core\{Middleware, Response, Request, Bootstrap};

Middleware::cors('POST');

$user = Middleware::auth();
$db = Bootstrap::db();
$data = Request::all();

$contact_id = $data['contact_id'] ?? 0;
$user_id = $user->id;

$query = "SELECT m.*, u.full_name as sender_name
          FROM messages m
          JOIN users u ON m.sender_id = u.id
          WHERE (sender_id = :user_id AND receiver_id = :contact_id)
             OR (sender_id = :contact_id2 AND receiver_id = :user_id2)
          ORDER BY created_at ASC";

$stmt = $db->prepare($query);
$stmt->bindParam(":user_id", $user_id);
$stmt->bindParam(":contact_id", $contact_id);
$stmt->bindParam(":contact_id2", $contact_id);
$stmt->bindParam(":user_id2", $user_id);
$stmt->execute();

$messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

Response::success($messages);
