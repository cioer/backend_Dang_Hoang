#!/usr/bin/env php
<?php
/**
 * Test Student Ranking API
 * Tests: api/teacher/get_student_ranking.php
 */

$BASE_URL = 'http://103.252.136.73:8080';
$API_URL = $BASE_URL . '/api/teacher/get_student_ranking.php';

// Test credentials
$TEACHER_USERNAME = 'teacher1'; // Will try to find a teacher account
$TEACHER_PASSWORD = 'password';

// Admin credentials to create test data if needed
$ADMIN_USERNAME = 'admin';
$ADMIN_PASSWORD = 'password';

function makeRequest($url, $method = 'GET', $headers = [], $data = null) {
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

    if ($method === 'POST') {
        curl_setopt($ch, CURLOPT_POST, true);
        if ($data) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }
    }

    if (!empty($headers)) {
        $headerArray = [];
        foreach ($headers as $key => $value) {
            $headerArray[] = "$key: $value";
        }
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headerArray);
    }

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);

    curl_close($ch);

    if ($error) {
        return ['error' => $error, 'http_code' => $httpCode];
    }

    return [
        'http_code' => $httpCode,
        'body' => $response,
        'data' => json_decode($response, true)
    ];
}

function login($username, $password) {
    global $BASE_URL;

    echo "🔐 Logging in as: $username\n";

    $response = makeRequest(
        $BASE_URL . '/api/auth/login.php',
        'POST',
        ['Content-Type' => 'application/json'],
        ['username' => $username, 'password' => $password]
    );

    if ($response['http_code'] === 200 && isset($response['data']['data']['token'])) {
        echo "✓ Login successful\n";
        return $response['data']['data']['token'];
    }

    echo "✗ Login failed: HTTP {$response['http_code']}\n";
    echo "Response: {$response['body']}\n";
    return null;
}

function getTeacherClasses($token) {
    global $BASE_URL;

    echo "\n📚 Fetching teacher's classes...\n";

    $response = makeRequest(
        $BASE_URL . '/api/teacher/get_classes.php',
        'GET',
        ['Authorization' => 'Bearer ' . $token]
    );

    if ($response['http_code'] === 200 && isset($response['data']['data'])) {
        $classes = $response['data']['data'];
        echo "✓ Found " . count($classes) . " classes\n";

        foreach ($classes as $class) {
            echo "  - Class ID: {$class['id']}, Name: {$class['name']}\n";
        }

        return $classes;
    }

    echo "✗ Failed to fetch classes: HTTP {$response['http_code']}\n";
    echo "Response: {$response['body']}\n";
    return [];
}

function testStudentRanking($token, $classId = null, $startDate = null, $endDate = null) {
    global $API_URL;

    $params = [];
    if ($classId) $params[] = "class_id=$classId";
    if ($startDate) $params[] = "start_date=$startDate";
    if ($endDate) $params[] = "end_date=$endDate";

    $url = $API_URL . (count($params) > 0 ? '?' . implode('&', $params) : '');

    $testName = "Get Student Ranking";
    if ($classId) $testName .= " (Class ID: $classId)";
    if ($startDate && $endDate) $testName .= " (Date: $startDate to $endDate)";

    echo "\n🧪 Testing: $testName\n";
    echo "URL: $url\n";

    $response = makeRequest(
        $url,
        'GET',
        ['Authorization' => 'Bearer ' . $token]
    );

    echo "HTTP Code: {$response['http_code']}\n";

    if ($response['http_code'] === 200) {
        echo "✓ Success!\n";

        if (isset($response['data']['data'])) {
            $students = $response['data']['data'];
            echo "Total students: " . count($students) . "\n";

            if (count($students) > 0) {
                echo "\nTop 5 students:\n";
                echo str_repeat("-", 80) . "\n";
                printf("%-5s %-30s %-15s %-15s %-10s\n",
                    "Rank", "Full Name", "Student Code", "Deducted Pts", "Violations");
                echo str_repeat("-", 80) . "\n";

                foreach (array_slice($students, 0, 5) as $idx => $student) {
                    printf("%-5d %-30s %-15s %-15d %-10d\n",
                        $idx + 1,
                        $student['full_name'],
                        $student['student_code'],
                        $student['total_deducted'],
                        $student['violation_count']
                    );
                }
            } else {
                echo "⚠ No students found in this class\n";
            }
        }

        echo "\nFull Response:\n";
        echo json_encode($response['data'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";

        return true;
    } else {
        echo "✗ Failed\n";
        echo "Response: {$response['body']}\n";
        return false;
    }
}

function findTeacherAccount() {
    global $BASE_URL, $ADMIN_USERNAME, $ADMIN_PASSWORD;

    echo "\n🔍 Finding a teacher account...\n";

    $adminToken = login($ADMIN_USERNAME, $ADMIN_PASSWORD);
    if (!$adminToken) {
        echo "⚠ Cannot login as admin to find teacher account\n";
        return null;
    }

    $response = makeRequest(
        $BASE_URL . '/api/admin/get_users.php?role=teacher&limit=1',
        'GET',
        ['Authorization' => 'Bearer ' . $adminToken]
    );

    if ($response['http_code'] === 200 && isset($response['data']['data'][0])) {
        $teacher = $response['data']['data'][0];
        echo "✓ Found teacher: {$teacher['username']} (ID: {$teacher['id']})\n";
        return $teacher['username'];
    }

    echo "⚠ No teacher account found\n";
    return null;
}

// ==================== MAIN TEST ====================

echo "========================================\n";
echo "STUDENT RANKING API TEST\n";
echo "========================================\n";

// Try to find a teacher account
$teacherUsername = findTeacherAccount();
if (!$teacherUsername) {
    echo "\n⚠ Using default teacher username: $TEACHER_USERNAME\n";
    $teacherUsername = $TEACHER_USERNAME;
}

// Login as teacher
$token = login($teacherUsername, $TEACHER_PASSWORD);

if (!$token) {
    echo "\n❌ Cannot proceed without valid teacher login\n";
    exit(1);
}

// Get teacher's classes
$classes = getTeacherClasses($token);

if (empty($classes)) {
    echo "\n⚠ Teacher has no assigned classes, testing without class_id\n";

    // Test 1: No parameters (should use homeroom class)
    testStudentRanking($token);

} else {
    $firstClass = $classes[0];
    $classId = $firstClass['id'];

    // Test 1: Get ranking for first class
    testStudentRanking($token, $classId);

    // Test 2: Get ranking with date filter (last 30 days)
    $endDate = date('Y-m-d');
    $startDate = date('Y-m-d', strtotime('-30 days'));
    testStudentRanking($token, $classId, $startDate, $endDate);

    // Test 3: Get ranking for current month
    $startDate = date('Y-m-01');
    $endDate = date('Y-m-t');
    testStudentRanking($token, $classId, $startDate, $endDate);

    // Test 4: No class_id (should fallback to homeroom class)
    testStudentRanking($token);
}

echo "\n========================================\n";
echo "TEST COMPLETED\n";
echo "========================================\n";
