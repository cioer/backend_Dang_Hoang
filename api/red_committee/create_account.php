<?php
require_once '../red_committee/util.php';
$database = new Database();
$db = $database->getConnection();
$u = auth();

// Allow both admin and teacher to create Red Star accounts
if (!$u || !in_array($u->role, ['admin', 'teacher'])) { http_response_code(403); exit; }
if (!verifyAction()) { http_response_code(428); exit; }

$body = json_decode(file_get_contents('php://input'), true);
$classId = isset($body['class_id']) ? intval($body['class_id']) : 0;
$username = isset($body['username']) ? trim($body['username']) : '';
$password = isset($body['password']) ? trim($body['password']) : '';
$durationWeeks = isset($body['duration_weeks']) ? intval($body['duration_weeks']) : 52;

// Teacher validation: must teach this class
if ($u->role === 'teacher') {
  $stmt = $db->prepare("SELECT 1 FROM schedule WHERE teacher_id=:tid AND class_id=:cid LIMIT 1");
  $stmt->execute([':tid'=>$u->id, ':cid'=>$classId]);
  if (!$stmt->fetch()) { http_response_code(403); exit; }
}

if ($classId <= 0 || empty($username) || empty($password)) {
    http_response_code(400);
    echo json_encode(['message' => 'Missing fields']);
    exit;
}

// Check if username exists
$stmt = $db->prepare("SELECT id FROM users WHERE username = :u");
$stmt->execute([':u' => $username]);
if ($stmt->fetch()) {
    http_response_code(409);
    echo json_encode(['message' => 'Tên đăng nhập đã tồn tại']);
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
$fullName = "Sao đỏ " . $className; 
if ($stmt->execute([':u' => $username, ':p' => $hashed_password, ':fn' => $fullName, ':cid' => $classId])) {
    $newUserId = $db->lastInsertId();

    // Add to red_committee_members
    $area = isset($body['area']) ? $body['area'] : 'Phân công ' . date('Y');
    $h = committeeHash($newUserId, $classId, $area);
    
    // Calculate expiration
    $startDate = date('Y-m-d');
    $expiredAt = date('Y-m-d', strtotime($startDate . " + $durationWeeks weeks"));
    
    $stmt2 = $db->prepare("INSERT INTO red_committee_members(user_id,class_id,area,active,assigned_by,hash,duration_weeks,start_date,expired_at) VALUES(:uid,:cid,:area,1,:by,:hash,:dur,:start,:exp)");
    $stmt2->execute([
        ':uid'=>$newUserId, ':cid'=>$classId, ':area'=>$area, ':by'=>$u->id, ':hash'=>$h,
        ':dur'=>$durationWeeks, ':start'=>$startDate, ':exp'=>$expiredAt
    ]);

    // Log
    $log = $db->prepare("INSERT INTO red_committee_logs(actor_id,action,target_user_id,class_id,area) VALUES(:aid,'create_account',:tuid,:cid,:area)");
    $log->execute([':aid'=>$u->id, ':tuid'=>$newUserId, ':cid'=>$classId, ':area'=>$area]);

    echo json_encode(['status' => 'success', 'user_id' => $newUserId]);
} else {
    http_response_code(500);
    echo json_encode(['message' => 'Database error']);
}
?>
