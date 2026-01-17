<?php
require_once __DIR__ . '/../bootstrap.php';

use App\Core\{Middleware, Response, Request, Bootstrap};

Middleware::cors('POST');

$user = Middleware::auth();

// Include utility functions
require_once __DIR__ . '/util.php';

// Allow both admin and teacher to create Red Star accounts
if (!in_array($user->role, ['admin', 'teacher'])) {
    Response::forbidden('Forbidden');
}

if (!verifyAction()) {
    Response::error('Action verification required', 428);
}

$db = Bootstrap::db();
$data = Request::all();

$classId = isset($data['class_id']) ? intval($data['class_id']) : 0;
$username = isset($data['username']) ? trim($data['username']) : '';
$password = isset($data['password']) ? trim($data['password']) : '';
$durationWeeks = isset($data['duration_weeks']) ? intval($data['duration_weeks']) : 52;

// Teacher validation: must teach this class
if ($user->role === 'teacher') {
    $stmt = $db->prepare("SELECT 1 FROM schedule WHERE teacher_id=:tid AND class_id=:cid LIMIT 1");
    $stmt->execute([':tid' => $user->id, ':cid' => $classId]);
    if (!$stmt->fetch()) {
        Response::forbidden('Forbidden');
    }
}

if ($classId <= 0 || empty($username) || empty($password)) {
    Response::error('Missing fields', 400);
}

// Check if username exists
$stmt = $db->prepare("SELECT id FROM users WHERE username = :u");
$stmt->execute([':u' => $username]);
if ($stmt->fetch()) {
    http_response_code(409);
    echo json_encode(['message' => 'Ten dang nhap da ton tai']);
    exit;
}

// Get Class Name for Full Name
$stmt = $db->prepare("SELECT name FROM classes WHERE id = :cid");
$stmt->execute([':cid' => $classId]);
$classRow = $stmt->fetch(PDO::FETCH_ASSOC);
$className = $classRow ? $classRow['name'] : $classId;

// Create User
$hashed_password = password_hash($password, PASSWORD_BCRYPT);
$stmt = $db->prepare("INSERT INTO users (username, password, full_name, role, class_id, is_red_star) VALUES (:u, :p, :fn, 'red_star', :cid, 1)");
$fullName = "Sao do " . $className;

if ($stmt->execute([':u' => $username, ':p' => $hashed_password, ':fn' => $fullName, ':cid' => $classId])) {
    $newUserId = $db->lastInsertId();

    // Add to red_committee_members
    $area = $data['area'] ?? 'Phan cong ' . date('Y');
    $h = committeeHash($newUserId, $classId, $area);

    // Calculate expiration
    $startDate = date('Y-m-d');
    $expiredAt = date('Y-m-d', strtotime($startDate . " + $durationWeeks weeks"));

    $stmt2 = $db->prepare("INSERT INTO red_committee_members(user_id,class_id,area,active,assigned_by,hash,duration_weeks,start_date,expired_at) VALUES(:uid,:cid,:area,1,:by,:hash,:dur,:start,:exp)");
    $stmt2->execute([
        ':uid' => $newUserId, ':cid' => $classId, ':area' => $area, ':by' => $user->id, ':hash' => $h,
        ':dur' => $durationWeeks, ':start' => $startDate, ':exp' => $expiredAt
    ]);

    // Log
    $log = $db->prepare("INSERT INTO red_committee_logs(actor_id,action,target_user_id,class_id,area) VALUES(:aid,'create_account',:tuid,:cid,:area)");
    $log->execute([':aid' => $user->id, ':tuid' => $newUserId, ':cid' => $classId, ':area' => $area]);

    Response::success(['status' => 'success', 'user_id' => $newUserId]);
} else {
    Response::error('Database error', 500);
}
