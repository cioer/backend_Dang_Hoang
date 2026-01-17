<?php
require_once '../../config/jwt.php';
require_once '../../config/database.php';
$headers = getallheaders();
$auth = isset($headers['Authorization']) ? $headers['Authorization'] : (isset($headers['authorization']) ? $headers['authorization'] : null);
if (!$auth || stripos($auth, 'Bearer ') !== 0) { http_response_code(401); exit; }
$token = substr($auth, 7);
$payload = validateJwt($token);
if (!$payload) { http_response_code(401); exit; }
$user = $payload['data'];
if (!$user || !$user->role) { http_response_code(403); exit; }
$code = str_pad(strval(random_int(0,999999)), 6, '0', STR_PAD_LEFT);
$jwt = new JWT();
$ticket = $jwt->encode(['sub'=>$user->id,'role'=>$user->role,'code'=>$code], 120);
header('Content-Type: application/json');
echo json_encode(['code'=>$code,'ticket'=>$ticket,'expires_in'=>120]);
?>
