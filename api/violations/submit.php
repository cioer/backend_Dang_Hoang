<?php
include_once '../../config/database.php';
include_once '../../config/jwt.php';

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$headers = getallheaders();
$token = isset($headers['Authorization']) ? str_replace('Bearer ', '', $headers['Authorization']) : null;

if (!$token) {
    http_response_code(401);
    echo json_encode(["message" => "Unauthorized access."]);
    exit;
}

$decoded = validateJWT($token);
if (!$decoded) {
    http_response_code(401);
    echo json_encode(["message" => "Invalid token."]);
    exit;
}

$reporter_id = $decoded['data']->id;
$role = $decoded['data']->role;

$database = new Database();
$db = $database->getConnection();

// Check if student is red star
$is_red_star = false;
if ($role == 'red_star') {
    $is_red_star = true;
} else if ($role == 'student') {
    $stmt = $db->prepare("SELECT COUNT(*) FROM red_committee_members WHERE user_id = :uid AND active = 1");
    $stmt->execute([':uid' => $reporter_id]);
    if ($stmt->fetchColumn() > 0) {
        $is_red_star = true;
    }
}

// Only teacher and admin can report violations (maybe monitor too if implemented)
if ($role != 'teacher' && $role != 'admin' && !$is_red_star) {
     http_response_code(403);
    echo json_encode(["message" => "Access denied."]);
    exit;
}

$data = json_decode(file_get_contents("php://input"));

if (
    !isset($data->student_id) ||
    !isset($data->rule_id)
) {
    http_response_code(400);
    echo json_encode(["message" => "Incomplete data."]);
    exit;
}

// assignment check for teacher: must be assigned to the student's class (homeroom or schedule)
// Red Star and Admin can grade anyone
if ($role == 'teacher') {
    $stc = $db->prepare("SELECT cr.class_id, c.homeroom_teacher_id FROM class_registrations cr JOIN classes c ON c.id=cr.class_id WHERE cr.student_id=:sid");
    $stc->execute([":sid"=>$data->student_id]);
    $cls = $stc->fetch(PDO::FETCH_ASSOC);
    if (!$cls) {
        $stc = $db->prepare("SELECT sd.class_id, c.homeroom_teacher_id FROM student_details sd JOIN classes c ON c.id=sd.class_id WHERE sd.user_id=:sid");
        $stc->execute([":sid"=>$data->student_id]);
        $cls = $stc->fetch(PDO::FETCH_ASSOC);
    }
    $assigned = false;
    if ($cls) {
        if ((int)$cls['homeroom_teacher_id'] === (int)$reporter_id) {
            $assigned = true;
        } else {
            $chk = $db->prepare("SELECT 1 FROM schedule WHERE class_id=:cid AND teacher_id=:tid LIMIT 1");
            $chk->execute([":cid"=>$cls['class_id'],":tid"=>$reporter_id]);
            if ($chk->fetch()) $assigned = true;
            if (!$assigned) {
                $chk2 = $db->prepare("SELECT 1 FROM class_teacher_assignments WHERE class_id=:cid AND teacher_id=:tid LIMIT 1");
                $chk2->execute([":cid"=>$cls['class_id'],":tid"=>$reporter_id]);
                if ($chk2->fetch()) $assigned = true;
            }
        }
    }
    if (!$assigned) {
        http_response_code(403);
        echo json_encode(["message" => "Bạn không được phân công dạy lớp này."]);
        exit;
    }
}

$query = "INSERT INTO violations (student_id, rule_id, reporter_id, note, created_at) VALUES (:student_id, :rule_id, :reporter_id, :note, NOW())";
$stmt = $db->prepare($query);

$note = isset($data->note) ? $data->note : "";

$stmt->bindParam(":student_id", $data->student_id);
$stmt->bindParam(":rule_id", $data->rule_id);
$stmt->bindParam(":reporter_id", $reporter_id);
$stmt->bindParam(":note", $note);

$ok = $stmt->execute();
if (!$ok) {
    http_response_code(503);
    echo json_encode(["message" => "Unable to record violation."]);
    exit;
}

$stmtPts = $db->prepare("SELECT points, type FROM conduct_rules WHERE id=:rid");
$stmtPts->bindParam(":rid", $data->rule_id);
$stmtPts->execute();
$rule = $stmtPts->fetch(PDO::FETCH_ASSOC);

if (!$rule) {
    http_response_code(400);
    echo json_encode(["message" => "Quy tắc vi phạm không tồn tại."]);
    exit;
}

$points = (int)$rule['points'];
$type = $rule['type'] ?? 'minus'; // Default to minus if null

$stmtInit = $db->prepare("INSERT IGNORE INTO discipline_points(student_id, points) VALUES (:sid, 100)");
$stmtInit->execute([':sid' => $data->student_id]);

$stmtCur = $db->prepare("SELECT points FROM discipline_points WHERE student_id=:sid");
$stmtCur->bindParam(":sid", $data->student_id);
$stmtCur->execute();
$cur = $stmtCur->fetch(PDO::FETCH_ASSOC);
$currentPoints = $cur ? (int)$cur['points'] : 100;

if ($type == 'plus') {
    $newPoints = min(100, $currentPoints + $points);
} else {
    $newPoints = max(0, $currentPoints - $points);
}

// Only update if points changed (though updated_at updates automatically usually)
if ($newPoints !== $currentPoints) {
    $stmtUpd = $db->prepare("UPDATE discipline_points SET points=:p WHERE student_id=:sid");
    $stmtUpd->bindParam(":p", $newPoints);
    $stmtUpd->bindParam(":sid", $data->student_id);
    if (!$stmtUpd->execute()) {
        http_response_code(500);
        echo json_encode(["message" => "Lỗi cập nhật điểm."]);
        exit;
    }
}

$thresholds = [];
foreach (['discipline_threshold_warn','discipline_threshold_conduct','discipline_threshold_class_change','discipline_class_name'] as $k) {
    $st = $db->prepare("SELECT setting_value FROM system_settings WHERE setting_key=:k");
    $st->bindParam(":k", $k);
    $st->execute();
    $v = $st->fetch(PDO::FETCH_ASSOC);
    $thresholds[$k] = $v ? $v['setting_value'] : null;
}

if ($thresholds['discipline_threshold_warn'] !== null && $newPoints <= (int)$thresholds['discipline_threshold_warn']) {
    $msg = "Cảnh cáo: điểm nề nếp còn ".$newPoints;
    $notify = $db->prepare("INSERT INTO notifications(title, content, sender_id, target_role) VALUES (:t,:c,:s,'student')");
    $notify->bindValue(":t", "Cảnh cáo vi phạm");
    $notify->bindValue(":c", $msg);
    $notify->bindValue(":s", $reporter_id);
    $notify->execute();
}
if ($thresholds['discipline_threshold_conduct'] !== null && $newPoints <= (int)$thresholds['discipline_threshold_conduct']) {
    $msg = "Hạ hạnh kiểm: điểm nề nếp còn ".$newPoints;
    $notify = $db->prepare("INSERT INTO notifications(title, content, sender_id, target_role) VALUES (:t,:c,:s,'student')");
    $notify->bindValue(":t", "Hạ hạnh kiểm");
    $notify->bindValue(":c", $msg);
    $notify->bindValue(":s", $reporter_id);
    $notify->execute();
}
if ($thresholds['discipline_threshold_class_change'] !== null && $newPoints <= (int)$thresholds['discipline_threshold_class_change']) {
    $clsName = $thresholds['discipline_class_name'] ?: 'KL-01';
    $stc = $db->prepare("SELECT id FROM classes WHERE name=:n");
    $stc->bindParam(":n", $clsName);
    $stc->execute();
    $cls = $stc->fetch(PDO::FETCH_ASSOC);
    if ($cls) {
        $stmtMove = $db->prepare("UPDATE student_details SET class_id=:cid WHERE user_id=:sid");
        $stmtMove->bindParam(":cid", $cls['id']);
        $stmtMove->bindParam(":sid", $data->student_id);
        $stmtMove->execute();
    }
    $msg = "Đổi lớp do đạt ngưỡng vi phạm. Điểm: ".$newPoints;
    $notify = $db->prepare("INSERT INTO notifications(title, content, sender_id, target_role) VALUES (:t,:c,:s,'parent')");
    $notify->bindValue(":t", "Thông báo đổi lớp");
    $notify->bindValue(":c", $msg);
    $notify->bindValue(":s", $reporter_id);
    $notify->execute();
}

echo json_encode([
    "message" => "OK", 
    "points" => $newPoints, 
    "deduct" => ($type == 'minus' ? $points : -$points),
    "prev_points" => $currentPoints
]);
?>