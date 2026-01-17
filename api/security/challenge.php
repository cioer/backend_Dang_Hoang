<?php
require_once __DIR__ . '/../bootstrap.php';

use App\Core\{Middleware, Response};

Middleware::cors('GET,POST');

$user = Middleware::auth();

if (!$user || !$user->role) {
    Response::forbidden('Forbidden');
}

$code = str_pad(strval(random_int(0, 999999)), 6, '0', STR_PAD_LEFT);
$jwt = new JWT();
$ticket = $jwt->encode(['sub' => $user->id, 'role' => $user->role, 'code' => $code], 120);

Response::success(['code' => $code, 'ticket' => $ticket, 'expires_in' => 120]);
