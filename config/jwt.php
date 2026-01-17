<?php
class JWT {
    private $secret = 'THPT_TRH_SECRET_256';
    private function b64url($data) { return rtrim(strtr(base64_encode($data), '+/', '-_'), '='); }
    private function b64url_decode($data) { return base64_decode(strtr($data, '-_', '+/')); }
    public function encode($payload, $expSeconds = 86400) {
        $header = ['alg' => 'HS256', 'typ' => 'JWT'];
        $payload['exp'] = time() + $expSeconds;
        $h = $this->b64url(json_encode($header));
        $p = $this->b64url(json_encode($payload));
        $s = $this->b64url(hash_hmac('sha256', $h . '.' . $p, $this->secret, true));
        return $h . '.' . $p . '.' . $s;
    }
    public function decode($jwt) {
        $parts = explode('.', $jwt);
        if (count($parts) !== 3) return null;
        [$h, $p, $s] = $parts;
        $check = $this->b64url(hash_hmac('sha256', $h . '.' . $p, $this->secret, true));
        if (!hash_equals($check, $s)) return null;
        $payload = json_decode($this->b64url_decode($p), true);
        if (!$payload || !isset($payload['exp']) || $payload['exp'] < time()) return null;
        return $payload;
    }
}

if (!function_exists('validateJwt')) {
    function validateJwt($token) {
        if (!$token) return false;
        $jwt = new JWT();
        $payload = $jwt->decode($token);
        if (!$payload) return false;
        $data = new stdClass();
        $data->id = $payload['sub'] ?? null;
        $data->role = $payload['role'] ?? null;
        $data->username = $payload['username'] ?? null;
        return ['data' => $data];
    }
}

if (!function_exists('validateJWT')) {
    function validateJWT($token) { return validateJwt($token); }
}
?>
