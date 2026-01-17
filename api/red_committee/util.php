<?php
require_once __DIR__ . '/../bootstrap.php';

use App\Core\Middleware;

function auth() {
    try {
        $user = Middleware::authOrNull();
        return $user;
    } catch (Exception $e) {
        return null;
    }
}

function verifyAction() {
    $h = getallheaders();
    $code = $h['X-Action-Code'] ?? $h['x-action-code'] ?? null;
    $ticket = $h['X-Action-Ticket'] ?? $h['x-action-ticket'] ?? null;
    if (!$code || !$ticket) return false;
    $jwt = new JWT();
    $payload = $jwt->decode($ticket);
    if (!$payload) return false;
    return isset($payload['code']) && hash_equals($payload['code'], $code);
}

function committeeHash($userId, $classId, $area) {
    return hash('sha256', $userId . '|' . ($classId ?? 'null') . '|' . ($area ?? 'null'));
}
