<?php
require_once __DIR__ . "/../../bootstrap.php";

use App\Core\{Middleware, Response, Request, Bootstrap};

Middleware::cors("POST");
$user = Middleware::requireAdmin();

$db = Bootstrap::db();
$body = Request::all();

$request_id = $body["request_id"] ?? null;
$approve = $body["approve"] ?? true;

if (!$request_id) {
    Response::error("Missing request_id", 400);
}

$q = $db->prepare("SELECT * FROM teacher_class_requests WHERE id=:id");
$q->execute([":id" => $request_id]);
$req = $q->fetch(PDO::FETCH_ASSOC);

if (!$req) {
    Response::notFound("Request not found");
}

// Get class name for notification
$classQ = $db->prepare("SELECT name FROM classes WHERE id=:cid");
$classQ->execute([":cid" => $req["class_id"]]);
$classRow = $classQ->fetch(PDO::FETCH_ASSOC);
$className = $classRow ? $classRow["name"] : $req["class_id"];

if ($approve) {
    $count = $db->prepare("SELECT COUNT(*) AS c FROM class_registrations WHERE class_id=:cid");
    $count->execute([":cid" => $req["class_id"]]);
    $has = $count->fetch(PDO::FETCH_ASSOC);
    if (!$has || (int)$has["c"] == 0) {
        Response::error("Lop chua co hoc sinh", 400);
    }

    $chkHm = $db->prepare("SELECT id,name FROM classes WHERE homeroom_teacher_id=:tid");
    $chkHm->execute([":tid" => $req["teacher_id"]]);
    $hm = $chkHm->fetch(PDO::FETCH_ASSOC);
    if ($hm) {
        Response::error("Giao vien da la chu nhiem lop " . $hm["name"], 400);
    }

    $db->prepare("UPDATE teacher_class_requests SET status=\"approved\", approved_at=NOW(), admin_id=:aid WHERE id=:id")->execute([":aid" => $user->id, ":id" => $request_id]);
    $db->prepare("INSERT IGNORE INTO class_teacher_assignments(class_id, teacher_id) VALUES (:cid,:tid)")->execute([":cid" => $req["class_id"], ":tid" => $req["teacher_id"]]);
    $db->prepare("UPDATE classes SET homeroom_teacher_id=:tid WHERE id=:cid")->execute([":tid" => $req["teacher_id"], ":cid" => $req["class_id"]]);
    $msg = "Yeu cau quan ly lop $className da duoc phe duyet. Ban da la chu nhiem lop $className.";
} else {
    $db->prepare("UPDATE teacher_class_requests SET status=\"rejected\", approved_at=NOW(), admin_id=:aid WHERE id=:id")->execute([":aid" => $user->id, ":id" => $request_id]);
    $msg = "Yeu cau quan ly lop $className da bi tu choi.";
}

// Send notification to specific teacher (not all teachers)
$notify = $db->prepare("INSERT INTO notifications(title, content, sender_id, target_user_id) VALUES (:t,:c,:s,:tid)");
$notify->execute([":t" => "Ket qua yeu cau phan lop", ":c" => $msg, ":s" => $user->id, ":tid" => $req["teacher_id"]]);

Response::message("OK");
