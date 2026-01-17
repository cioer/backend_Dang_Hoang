# Luồng đăng nhập end-to-end

## Front-end
- Người dùng nhập username/password (hoặc identifier đối với học sinh, student_code+password cho phụ huynh).
- Ứng dụng gửi JSON tới API:
  - Giáo viên/Admin: POST api/auth/login_teacher.php { username, password, captcha_* nếu cần }
  - Học sinh: POST api/auth/login_student.php { identifier(email hoặc mã HS-xxxxx), password, captcha_* nếu cần }
  - Phụ huynh: POST api/auth/login_parent_password.php { student_code, password }
- Nhận phản hồi:
  - Thành công: { message, token, user{ id, username, role, ... } }
  - Thất bại: { message, [captcha_required, captcha_question, captcha_token] }
- Lưu token JWT vào storage và kèm Authorization: Bearer trong các yêu cầu tiếp theo.

## Back-end
- Xác thực:
  - Tra cứu người dùng theo input và role.
  - Kiểm tra is_locked; kiểm tra ngưỡng thất bại để yêu cầu CAPTCHA.
  - So khớp mật khẩu:
    - Nếu hash bắt đầu bằng $2y/$2a/$2b/argon2*: dùng password_verify.
    - Nếu không: so sánh plaintext bằng hash_equals (fallback trong quá trình chuẩn hóa).
  - Ghi login_attempts, cập nhật last_login.
  - Phát JWT HS256 với payload: sub(id), username, role, exp.
- Đổi mật khẩu:
  - Nhận token từ body hoặc Authorization Bearer.
  - Xác minh old_password, băm new_password (bcrypt), cập nhật.

## Token/Session
- JWT lưu phía client, không có session server-side; mọi API yêu cầu bảo vệ cần gửi Authorization: Bearer <token>.
- Token mặc định hết hạn sau 24h.

## Thông báo lỗi tiêu biểu
- 400 Thiếu dữ liệu/định danh không hợp lệ.
- 401 Sai tài khoản/mật khẩu hoặc CAPTCHA yêu cầu.
- 403 Tài khoản bị khóa, phụ huynh chưa xác minh số điện thoại.

