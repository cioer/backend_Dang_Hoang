# Nhật ký Thay đổi Backend (Backend Changelog)

File này ghi lại tất cả các thay đổi được thực hiện trên Backend (PHP/SQL) để phục vụ việc đồng bộ lên Server VPS.

## Ngày: 2026-01-12

### 1. Thay đổi Code (PHP)
*(Chưa có thay đổi nào trong phiên làm việc hiện tại)*

### 2. Thay đổi Cơ sở dữ liệu (SQL)
- **CẬP NHẬT QUAN TRỌNG**: Cần chạy file `backend/2_update_red_committee.sql` để thêm cột `area` và `hash` vào bảng `red_committee_members`. Nếu không chạy file này, tính năng thêm Sao đỏ sẽ báo lỗi 500.

### 3. File mới tạo
- `backend/run_tests.py`: Script Python để kiểm thử API tự động.
- `backend/check_models.py`: Script kiểm tra cấu trúc JSON trả về.
- `backend/test_red_star.py`: Script kiểm thử logic Sao đỏ (phát hiện lỗi 500).
- `backend/2_update_red_committee.sql`: Script SQL sửa lỗi bảng `red_committee_members`.

---
**Hướng dẫn Deploy:**
1. Upload các file PHP mới (nếu có).
2. Mở phpMyAdmin hoặc CLI MySQL trên VPS.
3. Import file `backend/2_update_red_committee.sql` vào database `school_management`.
