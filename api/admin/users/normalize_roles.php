<?php
include_once '../../../config/database.php';
include_once '../../../config/jwt.php';
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
error_reporting(0);
ini_set('display_errors', '0');
$db = (new Database())->getConnection();
$data = json_decode(file_get_contents("php://input"));
$token = isset($_SERVER['HTTP_AUTHORIZATION']) ? str_replace('Bearer ', '', $_SERVER['HTTP_AUTHORIZATION']) : ($data->token ?? "");
$decoded = validateJWT($token);
if (!$decoded || ($decoded['data']->role ?? '') !== 'admin') {
    http_response_code(401);
    echo json_encode(["message"=>"Unauthorized"]);
    exit;
}
$updated = 0;
$stmtSel = $db->query("SELECT id, username FROM users WHERE is_locked=0");
$users = $stmtSel->fetchAll(PDO::FETCH_ASSOC);
foreach ($users as $u) {
    $req = null;
    $pref = substr($u['username'],0,3);
    if ($pref === 'GV-') $req = 'teacher';
    elseif ($pref === 'HS-') $req = 'student';
    elseif ($pref === 'PH-') $req = 'parent';
    if ($req) {
        $stmtUpd = $db->prepare("UPDATE users SET role=:r WHERE id=:id");
        if ($stmtUpd->execute([":r"=>$req, ":id"=>$u['id']])) $updated++;
    }
}
echo json_encode(["message"=>"Roles normalized","updated"=>$updated]);
