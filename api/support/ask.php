<?php
require_once __DIR__ . '/../bootstrap.php';
use App\Core\{Middleware, Response};
Middleware::cors('POST');
$user = Middleware::auth();
$input = json_decode(file_get_contents('php://input'), true);
$q = isset($input['question']) ? trim($input['question']) : '';
if (!$q) { Response::error('Thiếu câu hỏi', 400); }
$apiKey = getenv('VECTARA_API_KEY');
$customerId = getenv('VECTARA_CUSTOMER_ID');
$corpusId = getenv('VECTARA_CORPUS_ID');
if (!$apiKey || !$customerId || !$corpusId) { Response::error('Server chưa cấu hình Vectara', 500); }
$payload = json_encode([
    'query' => [[
        'query' => $q,
        'num_results' => 5,
        'corpus_key' => [[ 'customer_id' => (int)$customerId, 'corpus_id' => (int)$corpusId ]]
    ]]
]);
$ch = curl_init('https://api.vectara.io/v1/query');
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'x-api-key: ' . $apiKey,
    'customer-id: ' . $customerId
]);
curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
$resp = curl_exec($ch);
$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$err = curl_error($ch);
curl_close($ch);
if ($resp === false || $code >= 400) { Response::error('Lỗi gọi Vectara', 502); }
$data = json_decode($resp, true);
$answer = null;
if (isset($data['responseSet'][0]['response'][0]['text'])) {
    $answer = $data['responseSet'][0]['response'][0]['text'];
} elseif (isset($data['summary'][0]['text'])) {
    $answer = $data['summary'][0]['text'];
}
if (!$answer) { $answer = 'Xin lỗi, chưa tìm thấy nội dung phù hợp trong dữ liệu.'; }
Response::success(['answer' => $answer]);
