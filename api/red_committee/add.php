<?php
require_once __DIR__ . "/../bootstrap.php";

use App\Core\{Middleware, Response, Request, Bootstrap};

Middleware::cors("POST");

$user = Middleware::auth();

// Include utility functions
require_once __DIR__ . "/util.php";

if (!verifyAction()) {
    Response::error("Action verification required", 428);
}

$db = Bootstrap::db();
$role = $user->role;
$data = Request::all();

$userId = intval($data["user_id"] ?? 0);
$classId = isset($data["class_id"]) ? intval($data["class_id"]) : null;
$area = $data["area"] ?? null;

if ($userId <= 0) {
    Response::error("Invalid user_id", 400);
}

// Teacher validation: must be assigned to this class
if ($role === "teacher") {
    if (!$classId) {
        Response::forbidden("Forbidden");
    }
    $stmt = $db->prepare("
        SELECT 1 FROM (
            SELECT class_id FROM schedule WHERE teacher_id = :tid1
            UNION
            SELECT class_id FROM class_teacher_assignments WHERE teacher_id = :tid2
            UNION
            SELECT id AS class_id FROM classes WHERE homeroom_teacher_id = :tid3
        ) AS teacher_classes
        WHERE class_id = :cid
        LIMIT 1
    ");
    $stmt->execute([":tid1" => $user->id, ":tid2" => $user->id, ":tid3" => $user->id, ":cid" => $classId]);
    if (!$stmt->fetch()) {
        Response::forbidden("Forbidden");
    }
}

// Check if class already has an active Red Star
$replace = isset($data["replace"]) ? (bool)$data["replace"] : false;
$durationWeeks = isset($data["duration_weeks"]) ? intval($data["duration_weeks"]) : 4;
$startDate = $data["start_date"] ?? date("Y-m-d");
$expiredAt = date("Y-m-d", strtotime($startDate . " + $durationWeeks weeks"));

$stmt = $db->prepare("SELECT m.user_id, u.full_name FROM red_committee_members m JOIN users u ON m.user_id = u.id WHERE m.class_id=:cid AND m.active=1");
$stmt->execute([":cid" => $classId]);
$current = $stmt->fetch(PDO::FETCH_ASSOC);

if ($current) {
    if (!$replace) {
        http_response_code(409);
        echo json_encode([
            "message" => "Lop nay da co sao do.",
            "current_user" => $current["full_name"]
        ]);
        exit;
    } else {
        $oldUserId = $current["user_id"];

        // Deactivate old
        $update = $db->prepare("UPDATE red_committee_members SET active=0, revoked_at=NOW() WHERE class_id=:cid AND active=1");
        $update->execute([":cid" => $classId]);

        // Log the replacement (log the old user being replaced)
        $log = $db->prepare("INSERT INTO red_committee_logs(actor_id,action,target_user_id,class_id,area) VALUES(:aid,'replace',:tuid,:cid,:area)");
        $log->execute([":aid" => $user->id, ":tuid" => $oldUserId, ":cid" => $classId, ":area" => $area]);
    }
}

$h = committeeHash($userId, $classId, $area);
$stmt = $db->prepare("INSERT INTO red_committee_members(user_id,class_id,area,active,assigned_by,hash,duration_weeks,start_date,expired_at) VALUES(:uid,:cid,:area,1,:by,:hash,:dur,:start,:exp) ON DUPLICATE KEY UPDATE active=1, revoked_at=NULL, assigned_by=:by2, hash=:hash2, duration_weeks=:dur2, start_date=:start2, expired_at=:exp2");
$stmt->execute([
    ":uid" => $userId, ":cid" => $classId, ":area" => $area, ":by" => $user->id, ":hash" => $h,
    ":dur" => $durationWeeks, ":start" => $startDate, ":exp" => $expiredAt,
    ":by2" => $user->id, ":hash2" => $h, ":dur2" => $durationWeeks, ":start2" => $startDate, ":exp2" => $expiredAt
]);

$log = $db->prepare("INSERT INTO red_committee_logs(actor_id,action,target_user_id,class_id,area) VALUES(:aid,\"add\",:tuid,:cid,:area)");
$log->execute([":aid" => $user->id, ":tuid" => $userId, ":cid" => $classId, ":area" => $area]);

Response::success(["status" => "ok", "expired_at" => $expiredAt]);
