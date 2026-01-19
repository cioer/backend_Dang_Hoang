<?php
/**
 * Test Red Star Creation API with proper action verification
 * This script properly generates X-Action-Code and X-Action-Ticket
 */

require_once __DIR__ . '/config/jwt.php';

$BASE_URL = "http://103.252.136.73:8080";
$ADMIN_USERNAME = "admin";
$ADMIN_PASSWORD = "password";
$TEST_CLASS_ID = 1;

function apiCall($method, $url, $headers = [], $data = null) {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);

    if ($data) {
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        $headers[] = 'Content-Type: application/json';
    }

    if (!empty($headers)) {
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    }

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    return ['code' => $httpCode, 'body' => $response];
}

function generateActionVerification() {
    $jwt = new JWT();

    // Generate random code
    $code = bin2hex(random_bytes(16));

    // Create ticket (JWT with the code inside)
    $ticket = $jwt->encode(['code' => $code], 300); // 5 minutes expiry

    return ['code' => $code, 'ticket' => $ticket];
}

echo "============================================\n";
echo "RED STAR CREATION API TEST (WITH VERIFICATION)\n";
echo "============================================\n\n";

// Step 1: Login
echo "🔐 Step 1: Login as admin...\n";
$loginResponse = apiCall('POST', "$BASE_URL/api/login.php", [], [
    'username' => $ADMIN_USERNAME,
    'password' => $ADMIN_PASSWORD
]);

echo "Status: {$loginResponse['code']}\n";
echo "Response: {$loginResponse['body']}\n\n";

if ($loginResponse['code'] != 200) {
    die("❌ Login failed!\n");
}

$loginData = json_decode($loginResponse['body'], true);
$token = $loginData['token'] ?? null;

if (!$token) {
    die("❌ No token in response!\n");
}

echo "✅ Login successful!\n";
echo "Token: " . substr($token, 0, 30) . "...\n\n";

// Step 2: Generate action verification
echo "🔑 Step 2: Generate action verification...\n";
$verification = generateActionVerification();
echo "X-Action-Code: {$verification['code']}\n";
echo "X-Action-Ticket: " . substr($verification['ticket'], 0, 50) . "...\n\n";

// Step 3: Create Red Star account WITH verification
echo "📝 Step 3: Create Red Star account WITH verification...\n";

$testUsername = "saodo_test_" . time();
$headers = [
    "Authorization: Bearer $token",
    "X-Action-Code: {$verification['code']}",
    "X-Action-Ticket: {$verification['ticket']}"
];

$createData = [
    'class_id' => $TEST_CLASS_ID,
    'username' => $testUsername,
    'password' => 'test123',
    'duration_weeks' => 4,
    'area' => 'Test Area PHP'
];

echo "Creating account: $testUsername\n";
echo "Class ID: $TEST_CLASS_ID\n\n";

$createResponse = apiCall('POST', "$BASE_URL/api/red_committee/create_account.php", $headers, $createData);

echo "Status: {$createResponse['code']}\n";
echo "Response: {$createResponse['body']}\n\n";

// Analyze result
if ($createResponse['code'] == 200) {
    echo "✅ SUCCESS: Red Star account created!\n";
    $responseData = json_decode($createResponse['body'], true);
    if (isset($responseData['user_id'])) {
        echo "New User ID: {$responseData['user_id']}\n";
    }
} elseif ($createResponse['code'] == 428) {
    echo "⚠️  VERIFICATION FAILED: Action verification still not valid\n";
    echo "This means the JWT ticket or code is incorrect\n";
} elseif ($createResponse['code'] == 409) {
    echo "⚠️  CONFLICT: Username already exists\n";
} elseif ($createResponse['code'] == 500) {
    echo "❌ ERROR 500: Internal Server Error\n";
    echo "Need to check server logs\n";

    // Try to get more details
    $errorBody = json_decode($createResponse['body'], true);
    if ($errorBody && isset($errorBody['message'])) {
        echo "Error message: {$errorBody['message']}\n";
    }
} else {
    echo "⚠️  UNEXPECTED STATUS: {$createResponse['code']}\n";
}

echo "\n============================================\n";
echo "Test completed\n";
echo "============================================\n";
