<?php
class Captcha {
    private $secret = 'THPT_TRH_CAPTCHA_256';
    private function b64url($data) { return rtrim(strtr(base64_encode($data), '+/', '-_'), '='); }
    private function b64url_decode($data) { return base64_decode(strtr($data, '-_', '+/')); }
    public function generate() {
        $a = random_int(1, 9);
        $b = random_int(1, 9);
        $ts = time();
        $payload = $a . '|' . $b . '|' . $ts;
        $sig = hash_hmac('sha256', $payload, $this->secret, true);
        $token = $this->b64url($payload . '|' . $this->b64url($sig));
        return ['question' => $a . ' + ' . $b, 'token' => $token];
    }
    public function verify($token, $answer) {
        $raw = $this->b64url_decode($token);
        $parts = explode('|', $raw);
        if (count($parts) !== 4) return false;
        $a = intval($parts[0]);
        $b = intval($parts[1]);
        $ts = intval($parts[2]);
        $sig = $parts[3];
        if (time() - $ts > 600) return false;
        $check = $this->b64url(hash_hmac('sha256', $parts[0] . '|' . $parts[1] . '|' . $parts[2], $this->secret, true));
        if (!hash_equals($check, $sig)) return false;
        return intval($answer) === ($a + $b);
    }
}
?>
