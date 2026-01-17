<?php
require_once '../../config/jwt.php';
require_once '../../config/database.php';
function auth() {
  $h = getallheaders();
  $a = isset($h['Authorization']) ? $h['Authorization'] : (isset($h['authorization']) ? $h['authorization'] : null);
  if (!$a || stripos($a, 'Bearer ') !== 0) return null;
  $t = substr($a,7);
  $p = validateJwt($t);
  if (!$p) return null;
  return $p['data'];
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
function committeeHash($userId,$classId,$area) {
  return hash('sha256', $userId . '|' . ($classId ?? 'null') . '|' . ($area ?? 'null'));
}
?>
