<?php
require_once __DIR__ . '/../bootstrap.php';

use App\Core\{Middleware, Response, Bootstrap};

Middleware::cors('GET');

$captcha = Bootstrap::captcha();
$c = $captcha->generate();

Response::success(['captcha_question' => $c['question'], 'captcha_token' => $c['token']]);
