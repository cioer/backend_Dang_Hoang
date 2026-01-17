<?php
include_once '../../config/database.php';
include_once '../../config/jwt.php';
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
$headers = getallheaders();
$token = isset($headers['Authorization']) ? str_replace('Bearer ', '', $headers['Authorization']) : null;
$decoded = $token ? validateJWT($token) : null;
if (!$decoded || $decoded['data']->role!='teacher') { http_response_code(403); echo json_encode(["message"=>"Forbidden"]); exit; }
$db = (new Database())->getConnection();
$body = json_decode(file_get_contents('php://input'), true);
$class_ids = $body['class_ids'] ?? [];
if (!is_array($class_ids) || empty($class_ids)) { http_response_code(400); echo json_encode(["message"=>"Missing class_ids"]); exit; }
$tid = $decoded['data']->id;
$stmt = $db->prepare("INSERT INTO teacher_class_requests(teacher_id, class_id, status, requested_at) VALUES (:tid, :cid, 'pending', NOW())");
$names = [];
foreach ($class_ids as $cid) {
  $cid = (int)$cid;
  $chk = $db->prepare("SELECT name FROM classes WHERE id=:cid");
  $chk->execute([":cid"=>$cid]);
  $cls = $chk->fetch(PDO::FETCH_ASSOC);
  if ($cls) {
    $stmt->execute([":tid"=>$tid, ":cid"=>$cid]);
    $rid = $db->lastInsertId();
    $names[] = $cls['name'];
    // per-class message with request id tag
    $msgContent = "Yêu cầu quản lý lớp bởi ".($decoded['data']->full_name ?? ('GV-'.$tid)).": ".$cls['name']." - Thời gian: ".date('Y-m-d H:i:s')." [REQ:".$rid."]";
    $admins = $db->query("SELECT id FROM users WHERE role='admin'")->fetchAll(PDO::FETCH_ASSOC);
    if ($admins) {
      $insMsg = $db->prepare("INSERT INTO messages(sender_id, receiver_id, content, is_read, created_at) VALUES (:s,:r,:c,0,NOW())");
      foreach ($admins as $a) { $insMsg->execute([":s"=>$tid, ":r"=>$a['id'], ":c"=>$msgContent]); }
    }
  }
}
$tname = $decoded['data']->full_name ?? ('GV-'.$tid);
$content = "Yêu cầu quản lý lớp bởi ".$tname.": ".implode(', ', $names)." - Thời gian: ".date('Y-m-d H:i:s');
$notify = $db->prepare("INSERT INTO notifications(title, content, sender_id, target_role) VALUES (:t,:c,:s,'admin')");
$notify->execute([":t"=>"Yêu cầu phân lớp",":c"=>$content,":s"=>$tid]);
echo json_encode(["message"=>"OK"]);
