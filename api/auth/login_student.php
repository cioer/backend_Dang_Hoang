<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
include_once '../../config/database.php';
include_once '../../config/jwt.php';
include_once '../../config/captcha.php';
$database = new Database();
$db = $database->getConnection();
$jwt = new JWT();
$captcha = new Captcha();
$ip = $_SERVER['REMOTE_ADDR'] ?? null;
$data = json_decode(file_get_contents("php://input"), true);
function log_attempt($db,$u,$s,$r,$ip){$q=$db->prepare("INSERT INTO login_attempts(username,success,reason,ip) VALUES(:u,:s,:r,:i)");$q->execute([':u'=>$u,':s'=>$s,':r'=>$r,':i'=>$ip]);}
if(!$data || !isset($data['identifier']) || !isset($data['password'])){echo json_encode(['message'=>'Thiếu dữ liệu']);http_response_code(400);exit;}
$identifier = trim($data['identifier']);
$password = $data['password'];
$byEmail = filter_var($identifier,FILTER_VALIDATE_EMAIL) ? true : false;
// Regex check removed to support flexible usernames (e.g. HS-10A1-10)
// if(!$byEmail && !preg_match('/^HS-\d{5}$/',$identifier)){echo json_encode(['message'=>'Mã học sinh không hợp lệ']);http_response_code(400);exit;}
$stmt = $byEmail ? $db->prepare("SELECT id,username,password,full_name,role,is_locked,password_must_change FROM users WHERE email=:e AND role IN ('student', 'red_star') LIMIT 1") : $db->prepare("SELECT id,username,password,full_name,role,is_locked,password_must_change FROM users WHERE username=:u AND role IN ('student', 'red_star') LIMIT 1");
$stmt->execute($byEmail ? [':e'=>$identifier] : [':u'=>$identifier]);
if($stmt->rowCount()===0){log_attempt($db,$identifier,0,'not_found',$ip);echo json_encode(['message'=>'Tài khoản không tồn tại']);http_response_code(401);exit;}
$user = $stmt->fetch(PDO::FETCH_ASSOC);
if(intval($user['is_locked'])===1){log_attempt($db,$identifier,0,'locked',$ip);echo json_encode(['message'=>'Tài khoản đã bị khóa']);http_response_code(403);exit;}
$failsStmt = $db->prepare("SELECT COUNT(*) c FROM login_attempts WHERE username=:u AND success=0 AND created_at> (NOW() - INTERVAL 15 MINUTE)");
$failsStmt->execute([':u'=>$byEmail ? $user['username'] : $identifier]);
$fails = intval($failsStmt->fetch(PDO::FETCH_ASSOC)['c']);
if($fails>=3){
    if(!isset($data['captcha_token']) || !isset($data['captcha_answer']) || !$captcha->verify($data['captcha_token'],$data['captcha_answer'])){
        $challenge = $captcha->generate();
        echo json_encode(['message'=>'Yêu cầu xác minh CAPTCHA','captcha_required'=>true,'captcha_question'=>$challenge['question'],'captcha_token'=>$challenge['token']]);
        http_response_code(401);
        exit;
    }
}
if(!password_verify($password,$user['password'])){log_attempt($db,$identifier,0,'wrong_password',$ip);echo json_encode(['message'=>'Sai mật khẩu']);http_response_code(401);exit;}
log_attempt($db,$identifier,1,'success',$ip);
$db->prepare("UPDATE users SET last_login=NOW() WHERE id=:id")->execute([':id'=>$user['id']]);

$stmt = $db->prepare("SELECT COUNT(*) FROM red_committee_members WHERE user_id = :uid AND active = 1");
$stmt->execute([':uid' => $user['id']]);
$is_red_star = $stmt->fetchColumn() > 0;

$token = $jwt->encode(['sub'=>$user['id'],'username'=>$user['username'],'role'=>$user['role']]);
$mustChange = intval($user['password_must_change'])===1;
echo json_encode(['message'=>$mustChange?'Đăng nhập thành công, yêu cầu đổi mật khẩu lần đầu':'Đăng nhập thành công','token'=>$token,'require_change'=>$mustChange,'user'=>['id'=>$user['id'],'username'=>$user['username'],'full_name'=>$user['full_name'],'role'=>$user['role'],'is_red_star'=>$is_red_star?1:0]]);
http_response_code(200);
?>
