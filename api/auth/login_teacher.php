<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
include_once '../../config/database.php';
include_once '../../config/jwt.php';
include_once '../../config/captcha.php';
include_once '../../lib/UserRepository.php';
$database = new Database();
$db = $database->getConnection();
$jwt = new JWT();
$captcha = new Captcha();
$repo = new UserRepository($db);
$ip = $_SERVER['REMOTE_ADDR'] ?? null;
$data = json_decode(file_get_contents("php://input"), true);
function log_attempt($db,$u,$s,$r,$ip){$q=$db->prepare("INSERT INTO login_attempts(username,success,reason,ip) VALUES(:u,:s,:r,:i)");$q->execute([':u'=>$u,':s'=>$s,':r'=>$r,':i'=>$ip]);}
if(!$data || !isset($data['username']) || !isset($data['password'])){echo json_encode(['message'=>'Thiếu dữ liệu']);http_response_code(400);exit;}
$username = trim($data['username']);
$password = $data['password'];
$username = $username; // chấp nhận mọi mã giáo viên theo DB (vd: GV01, gv21001)
$user = $repo->getByUsernameRoleIn($username, ['teacher','admin']);
if(!$user){log_attempt($db,$username,0,'not_found',$ip);echo json_encode(['message'=>'Tài khoản không tồn tại','code'=>'not_found']);http_response_code(401);exit;}
if($repo->isLocked($user)){log_attempt($db,$username,0,'locked',$ip);echo json_encode(['message'=>'Tài khoản đã bị khóa','code'=>'locked']);http_response_code(403);exit;}
$failsStmt = $db->prepare("SELECT COUNT(*) c FROM login_attempts WHERE username=:u AND success=0 AND created_at> (NOW() - INTERVAL 15 MINUTE)");
$failsStmt->execute([':u'=>$username]);
$fails = intval($failsStmt->fetch(PDO::FETCH_ASSOC)['c']);
if($fails>=3){
    if (strtolower($user['role']) !== 'admin') {
        if(!isset($data['captcha_token']) || !isset($data['captcha_answer']) || !$captcha->verify($data['captcha_token'],$data['captcha_answer'])){
            $challenge = $captcha->generate();
            echo json_encode(['message'=>'Yêu cầu xác minh CAPTCHA','code'=>'captcha_required','captcha_required'=>true,'captcha_question'=>$challenge['question'],'captcha_token'=>$challenge['token']]);
            http_response_code(401);
            exit;
        }
    }
}
if(!$repo->verifyPassword($user['password'],$password)){log_attempt($db,$username,0,'wrong_password',$ip);echo json_encode(['message'=>'Sai mật khẩu','code'=>'wrong_password']);http_response_code(401);exit;}
log_attempt($db,$username,1,'success',$ip);
$repo->updateLastLogin(intval($user['id']));
$token = $jwt->encode(['sub'=>$user['id'],'username'=>$user['username'],'role'=>$user['role']]);
echo json_encode(['message'=>'Đăng nhập thành công','token'=>$token,'user'=>['id'=>$user['id'],'username'=>$user['username'],'full_name'=>$user['full_name'],'role'=>$user['role']]]);
http_response_code(200);
?>
