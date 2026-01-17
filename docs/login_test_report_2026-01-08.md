# Báo cáo kiểm thử đăng nhập

## Phạm vi
- API: login_teacher, login_student, login_parent_password, change_password.
- Dữ liệu seed: users có bcrypt/hoặc plaintext tạm thời trong quá trình chuẩn hóa.

## Kết quả chính
- Thành công:
  - Đăng nhập admin bằng 123456 → 200, nhận JWT.
  - Đổi mật khẩu admin (Bearer token) → 200, đăng nhập bằng mật khẩu mới.
- Thất bại:
  - Sai mật khẩu → 401 với message "Sai mật khẩu".
  - Tài khoản bị khóa → 403.
  - Yêu cầu CAPTCHA khi vượt ngưỡng thất bại → 401 với captcha_required=true.
- Lỗi 401 khi đổi mật khẩu do thiếu token đã được khắc phục bằng hỗ trợ Authorization: Bearer.

## Sửa lỗi
- Thêm đọc Bearer token ở change_password.php.
- Chuẩn hóa xác thực mật khẩu (fallback nếu DB chưa chuẩn hóa bcrypt).
- Seed/reset users với mật khẩu chuẩn bcrypt, cung cấp script đặt mật khẩu admin.

## Khuyến nghị
- Chuẩn hóa schema users.password, tránh password_hash.
  - Chạy migrate_users_password_column.sql.
- Không lưu plaintext; nếu dùng script đặt nhanh, đổi lại bằng API để hệ thống băm bcrypt.
- Bổ sung log chi tiết cho đăng nhập thất bại (lý do, IP).
- Thêm rate limit và CAPTCHA thống nhất giữa các endpoint.

