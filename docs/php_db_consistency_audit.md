# Báo cáo đối chiếu PHP ↔ CSDL và kiểm thử đăng nhập admin

## 1) Đối chiếu cấu trúc bảng và truy vấn
- Bảng users trong code PHP sử dụng cột password để đọc/ghi mật khẩu.
- Schema database.sql định nghĩa users.password VARCHAR(255) NOT NULL và được seed với bcrypt.
- Schema school_management_seed.sql định nghĩa users.password_hash và không seed mật khẩu, dẫn đến đăng nhập thất bại nếu dùng schema này.
- Các API PHP dùng các cột: id, username, password, full_name, role, email, phone, phone_verified, is_locked, password_must_change, last_login, avatar.
- Các truy vấn tiêu biểu:
  - SELECT id, username, password, full_name, role, is_locked FROM users WHERE username=:u AND role IN ('teacher','admin') LIMIT 1
  - SELECT id, username, password, full_name, role, is_locked, password_must_change FROM users WHERE email=:e AND role='student' LIMIT 1
  - UPDATE users SET last_login=NOW() WHERE id=:id
  - UPDATE users SET password=:password WHERE id=:id
- Phát hiện bất nhất:
  - school_management_seed.sql dùng password_hash thay vì password và không có dữ liệu mật khẩu.
  - Tất cả PHP đều kỳ vọng cột password; nếu CSDL không có password hoặc giá trị rỗng, đăng nhập sẽ luôn sai.

## 2) Cơ chế mã hóa mật khẩu
- PHP sử dụng password_hash (PASSWORD_BCRYPT/PASSWORD_DEFAULT) để băm mật khẩu khi tạo/cập nhật người dùng.
- Xác thực dùng password_verify so sánh plaintext đầu vào với bcrypt trong CSDL.
- Seed trong database.sql đặt bcrypt hợp lệ cho các tài khoản mẫu.

## 3) Kiểm thử luồng đăng nhập admin với “123456”
- Luồng xử lý:
  - Form gửi JSON {username: "admin", password: "123456"} tới API đăng nhập (login.php hoặc login_teacher.php).
  - API truy vấn users lấy các trường username/password/role và thực hiện password_verify("123456", users.password).
  - Nếu thành công: ghi login_attempts thành công, cập nhật last_login và phát JWT.
- Điểm kiểm tra giá trị:
  - Đầu vào form: kiểm chứng chuỗi “123456” gửi đúng payload.
  - Biến PHP trước so sánh: password trong $data và password từ $user['password'] đã nạp từ CSDL.
  - Giá trị trong CSDL: SELECT username,password FROM users WHERE username='admin' để xem bcrypt hiện tại.
- Xác minh hàm so sánh/hash:
  - password_verify được gọi với plaintext và hash bcrypt trong CSDL.
  - Việc đổi mật khẩu dùng password_hash khi ghi lại vào users.password.
- Cách đặt mật khẩu admin thành “123456” (khuyến nghị):
  - Tạo hash bằng PHP: php -r "echo password_hash('123456', PASSWORD_BCRYPT), PHP_EOL;"
  - Cập nhật CSDL: UPDATE users SET password='<hash_vừa_tạo>' WHERE username='admin';

## 4) Kết quả và phát hiện bất thường
- Nếu dùng database.sql: nhất quán, đăng nhập hoạt động khi mật khẩu admin là bcrypt của “123456”.
- Nếu dùng school_management_seed.sql: thiếu cột password và thiếu dữ liệu mật khẩu; đăng nhập sẽ thất bại.
- Khuyến nghị sửa:
  - Chuẩn hóa CSDL về cột users.password VARCHAR(255) NOT NULL.
  - Seed đầy đủ bcrypt cho admin/teacher/student/parent.
  - Tránh dùng schema có cột password_hash trừ khi cập nhật đồng bộ code để đọc password_hash.

## 5) Khác biệt môi trường
- Hiện tại config Backend/config/database.php cố định host=localhost, db=school_management, user=root, password rỗng.
- Không phát hiện cấu hình riêng cho production; cần đảm bảo cùng một schema (users.password) giữa các môi trường.

