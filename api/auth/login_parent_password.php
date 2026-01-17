<?php
require_once __DIR__ . '/../bootstrap.php';

use App\Core\{Middleware, Response, Request, Bootstrap};

Middleware::cors('POST');

$db = Bootstrap::db();
$jwt = Bootstrap::jwt();
$ip = Request::ip();
$data = Request::all();

function log_attempt($db, $u, $s, $r, $ip) {
    $q = $db->prepare("INSERT INTO login_attempts(username,success,reason,ip) VALUES(:u,:s,:r,:i)");
    $q->execute([':u' => $u, ':s' => $s, ':r' => $r, ':i' => $ip]);
}

if (!$data || !isset($data['student_code']) || !isset($data['password'])) {
    Response::error('Thiếu dữ liệu', 400);
}

$studentCode = trim($data['student_code']);
$password = $data['password'];

$stu = $db->prepare("SELECT id, password, full_name FROM users WHERE username=:u AND role='student' LIMIT 1");
$stu->execute([':u' => $studentCode]);
if ($stu->rowCount() === 0) {
    log_attempt($db, $studentCode, 0, 'student_not_found', $ip);
    Response::error('Không tìm thấy học sinh', 401);
}
$student = $stu->fetch(PDO::FETCH_ASSOC);

// Check if parent account exists
$par = $db->prepare("SELECT u.id,u.username,u.password,u.full_name,u.role,u.phone_verified FROM parent_student_links p JOIN users u ON p.parent_id=u.id WHERE p.student_id=:sid AND u.role='parent' LIMIT 1");
$par->execute([':sid' => $student['id']]);

if ($par->rowCount() === 0) {
    // Parent account doesn't exist. Check if password matches STUDENT password
    if (password_verify($password, $student['password'])) {
        // Create new Parent Account
        $phUsername = "PH-" . $studentCode;
        $phFullName = "Phụ huynh em " . $student['full_name'];
        $phPass = $student['password'];

        try {
            $db->beginTransaction();

            $stmtInsert = $db->prepare("INSERT INTO users(username, password, full_name, role, phone_verified, created_at) VALUES(:u, :p, :n, 'parent', 1, NOW())");
            $stmtInsert->execute([':u' => $phUsername, ':p' => $phPass, ':n' => $phFullName]);
            $parentId = $db->lastInsertId();

            $stmtLink = $db->prepare("INSERT INTO parent_student_links(parent_id, student_id) VALUES(:pid, :sid)");
            $stmtLink->execute([':pid' => $parentId, ':sid' => $student['id']]);

            $db->commit();

            $token = $jwt->encode(['sub' => $parentId, 'username' => $phUsername, 'role' => 'parent']);

            log_attempt($db, $phUsername, 1, 'first_login_auto_create', $ip);

            Response::success([
                'message' => 'Đăng nhập lần đầu thành công (Tài khoản phụ huynh đã được tạo)',
                'token' => $token,
                'user' => [
                    'id' => $parentId,
                    'username' => $phUsername,
                    'full_name' => $phFullName,
                    'role' => 'parent'
                ],
                'is_new_account' => true
            ]);

        } catch (Exception $e) {
            $db->rollBack();
            log_attempt($db, $studentCode, 0, 'auto_create_failed', $ip);
            Response::error('Lỗi tạo tài khoản phụ huynh: ' . $e->getMessage(), 500);
        }
    } else {
        log_attempt($db, $studentCode, 0, 'parent_not_found_wrong_pass', $ip);
        Response::error('Tài khoản phụ huynh chưa được kích hoạt hoặc sai mật khẩu', 401);
    }
}

$parent = $par->fetch(PDO::FETCH_ASSOC);
if (intval($parent['phone_verified']) !== 1) {
    log_attempt($db, $parent['username'], 0, 'phone_not_verified', $ip);
    Response::error('Số điện thoại chưa xác minh', 403);
}

if (!password_verify($password, $parent['password'])) {
    log_attempt($db, $parent['username'], 0, 'wrong_password', $ip);
    Response::error('Sai mật khẩu phụ huynh', 401);
}

log_attempt($db, $parent['username'], 1, 'success', $ip);
$db->prepare("UPDATE users SET last_login=NOW() WHERE id=:id")->execute([':id' => $parent['id']]);

$token = $jwt->encode(['sub' => $parent['id'], 'username' => $parent['username'], 'role' => $parent['role']]);

Response::success([
    'message' => 'Đăng nhập thành công',
    'token' => $token,
    'user' => [
        'id' => $parent['id'],
        'username' => $parent['username'],
        'full_name' => $parent['full_name'],
        'role' => $parent['role']
    ]
]);
