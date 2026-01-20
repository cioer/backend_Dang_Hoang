# Tính năng Xếp hạng Học sinh (Student Ranking)

## Tổng quan

Tính năng xếp hạng học sinh cho phép giáo viên xem danh sách học sinh trong lớp được sắp xếp theo điểm trừ (từ ít đến nhiều), giúp nhận diện học sinh có hành vi tốt nhất.

## API Endpoint

**File**: `api/teacher/get_student_ranking.php`

**Method**: `GET`

**Authentication**: Required (Bearer Token)

**Roles**: `teacher`, `admin`

## Tham số (Query Parameters)

| Tham số | Kiểu | Bắt buộc | Mô tả |
|---------|------|----------|-------|
| `class_id` | integer | Không | ID của lớp học cần xem. Nếu không truyền, tự động lấy lớp chủ nhiệm của giáo viên |
| `start_date` | string (YYYY-MM-DD) | Không | Ngày bắt đầu lọc vi phạm |
| `end_date` | string (YYYY-MM-DD) | Không | Ngày kết thúc lọc vi phạm |

## Logic Xử lý

### 1. Xác thực quyền truy cập (Authorization)

```php
// Chỉ teacher và admin có quyền xem
if ($user->role !== 'teacher' && $user->role !== 'admin') {
    Response::error('Chỉ giáo viên mới có quyền xem.', 403);
}
```

### 2. Xác định lớp học

**Trường hợp 1**: Có `class_id` trong query params
- Sử dụng `class_id` được truyền vào

**Trường hợp 2**: Không có `class_id`
- Tự động tìm lớp chủ nhiệm của giáo viên:
```php
SELECT id FROM classes WHERE homeroom_teacher_id = :teacher_id LIMIT 1
```

### 3. Kiểm tra quyền truy cập lớp (Teacher only)

Giáo viên chỉ có thể xem lớp nếu họ là:
- **Giáo viên chủ nhiệm** (`homeroom_teacher_id`)
- **Giáo viên được phân công** (`class_teacher_assignments.teacher_id`)
- **Giáo viên dạy theo thời khóa biểu** (`schedule.teacher_id`)

```php
SELECT COUNT(*) FROM classes c
LEFT JOIN class_teacher_assignments cta ON c.id = cta.class_id
LEFT JOIN schedule s ON c.id = s.class_id
WHERE c.id = :class_id
  AND (c.homeroom_teacher_id = :teacher_id
    OR cta.teacher_id = :teacher_id
    OR s.teacher_id = :teacher_id)
```

### 4. Lọc theo thời gian (Optional)

Nếu có `start_date` và `end_date`:
```php
WHERE v.created_at BETWEEN :start_date AND :end_date
```

### 5. Truy vấn dữ liệu xếp hạng

```sql
SELECT
    u.id as student_id,
    u.full_name,
    u.username as student_code,
    COALESCE(SUM(cr.points), 0) as total_deducted,
    COUNT(v.id) as violation_count
FROM student_details sd
JOIN users u ON sd.user_id = u.id
LEFT JOIN violations v ON u.id = v.student_id [date_filter]
LEFT JOIN conduct_rules cr ON v.rule_id = cr.id
WHERE sd.class_id = :class_id
GROUP BY u.id, u.full_name, u.username
ORDER BY total_deducted ASC, violation_count ASC, u.full_name ASC
```

**Giải thích**:
- Lấy tất cả học sinh trong lớp (`student_details`)
- `LEFT JOIN violations`: Bao gồm cả học sinh không có vi phạm (điểm trừ = 0)
- `COALESCE(SUM(cr.points), 0)`: Tổng điểm trừ, nếu NULL thì = 0
- `COUNT(v.id)`: Đếm số lần vi phạm
- **Sắp xếp**: Ưu tiên học sinh có điểm trừ ít nhất, nếu bằng nhau thì xét số lần vi phạm, cuối cùng là tên

## Response Format

### Success (200 OK)

```json
{
    "success": true,
    "data": [
        {
            "student_id": 123,
            "full_name": "Nguyễn Văn A",
            "student_code": "HS001",
            "total_deducted": 0,
            "violation_count": 0
        },
        {
            "student_id": 124,
            "full_name": "Trần Thị B",
            "student_code": "HS002",
            "total_deducted": 5,
            "violation_count": 1
        },
        {
            "student_id": 125,
            "full_name": "Lê Văn C",
            "student_code": "HS003",
            "total_deducted": 10,
            "violation_count": 2
        }
    ]
}
```

### Error Responses

#### 403 Forbidden - Không có quyền
```json
{
    "success": false,
    "message": "Chỉ giáo viên mới có quyền xem."
}
```

#### 403 Forbidden - Không phải lớp của giáo viên
```json
{
    "success": false,
    "message": "Bạn không có quyền xem thống kê lớp này."
}
```

#### 404 Not Found - Không tìm thấy lớp
```json
{
    "success": false,
    "message": "Không tìm thấy lớp học quản lý."
}
```

#### 500 Internal Server Error
```json
{
    "success": false,
    "message": "Lỗi Database: [error details]"
}
```

## Ví dụ sử dụng

### 1. Xem xếp hạng lớp chủ nhiệm (không truyền class_id)

```bash
curl -X GET "http://103.252.136.73:8080/api/teacher/get_student_ranking.php" \
  -H "Authorization: Bearer YOUR_TOKEN"
```

### 2. Xem xếp hạng lớp cụ thể

```bash
curl -X GET "http://103.252.136.73:8080/api/teacher/get_student_ranking.php?class_id=5" \
  -H "Authorization: Bearer YOUR_TOKEN"
```

### 3. Xem xếp hạng tháng hiện tại

```bash
curl -X GET "http://103.252.136.73:8080/api/teacher/get_student_ranking.php?class_id=5&start_date=2026-01-01&end_date=2026-01-31" \
  -H "Authorization: Bearer YOUR_TOKEN"
```

### 4. Xem xếp hạng 30 ngày gần nhất

```bash
curl -X GET "http://103.252.136.73:8080/api/teacher/get_student_ranking.php?class_id=5&start_date=2025-12-21&end_date=2026-01-20" \
  -H "Authorization: Bearer YOUR_TOKEN"
```

## Điểm cần lưu ý

### 1. Quyền truy cập
- Admin có thể xem mọi lớp
- Teacher chỉ xem được lớp mà mình quản lý (chủ nhiệm, phân công, hoặc dạy)

### 2. Xử lý học sinh không vi phạm
- Sử dụng `LEFT JOIN` để đảm bảo học sinh không vi phạm vẫn hiển thị
- `total_deducted = 0` và `violation_count = 0` cho học sinh không vi phạm

### 3. Thứ tự ưu tiên xếp hạng
1. Điểm trừ thấp hơn → Xếp hạng cao hơn
2. Nếu điểm trừ bằng nhau → Số lần vi phạm ít hơn → Xếp hạng cao hơn
3. Nếu cả hai bằng nhau → Sắp xếp theo tên (alphabet)

### 4. Lọc theo thời gian
- Nếu chỉ truyền một trong hai (`start_date` hoặc `end_date`), filter không được áp dụng
- Cần cả hai tham số để filter hoạt động
- Format ngày: `YYYY-MM-DD`
- Thời gian: `start_date 00:00:00` đến `end_date 23:59:59`

## Test Cases

### Test Case 1: Giáo viên xem lớp chủ nhiệm
- **Input**: Không truyền `class_id`
- **Expected**: Trả về danh sách học sinh lớp chủ nhiệm, sắp xếp theo điểm trừ

### Test Case 2: Giáo viên xem lớp được phân công
- **Input**: `class_id` của lớp được phân công
- **Expected**: Success, trả về danh sách học sinh

### Test Case 3: Giáo viên xem lớp không được phân công
- **Input**: `class_id` của lớp khác
- **Expected**: 403 Forbidden

### Test Case 4: Admin xem bất kỳ lớp nào
- **Input**: `class_id` bất kỳ
- **Expected**: Success, trả về danh sách học sinh

### Test Case 5: Lọc theo tháng hiện tại
- **Input**: `start_date=2026-01-01`, `end_date=2026-01-31`
- **Expected**: Chỉ tính vi phạm trong tháng 1/2026

### Test Case 6: Lớp không có vi phạm
- **Input**: Lớp mới, chưa có vi phạm nào
- **Expected**: Tất cả học sinh có `total_deducted=0`, `violation_count=0`

### Test Case 7: Học sinh role (không phải teacher)
- **Input**: Token của student
- **Expected**: 403 Forbidden

## Cải tiến có thể thực hiện

1. **Phân trang**: Thêm `limit` và `offset` cho lớp đông học sinh
2. **Xuất Excel**: Thêm endpoint để xuất danh sách xếp hạng ra file Excel
3. **So sánh theo thời gian**: Thêm chức năng so sánh xếp hạng giữa các tháng
4. **Thống kê chi tiết**: Thêm thông tin loại vi phạm phổ biến của từng học sinh
5. **Cache**: Cache kết quả cho lớp có nhiều học sinh

## Database Schema Liên quan

### Table: `users`
- `id`: User ID
- `full_name`: Tên đầy đủ
- `username`: Mã học sinh
- `role`: Vai trò (student, teacher, admin)

### Table: `student_details`
- `user_id`: Foreign key → users.id
- `class_id`: Foreign key → classes.id

### Table: `classes`
- `id`: Class ID
- `homeroom_teacher_id`: Foreign key → users.id (giáo viên chủ nhiệm)

### Table: `violations`
- `id`: Violation ID
- `student_id`: Foreign key → users.id
- `rule_id`: Foreign key → conduct_rules.id
- `created_at`: Thời gian vi phạm

### Table: `conduct_rules`
- `id`: Rule ID
- `points`: Điểm trừ

### Table: `class_teacher_assignments`
- `class_id`: Foreign key → classes.id
- `teacher_id`: Foreign key → users.id

### Table: `schedule`
- `class_id`: Foreign key → classes.id
- `teacher_id`: Foreign key → users.id

## File Reference

**Location**: `api/teacher/get_student_ranking.php:1-86`

**Key Functions**:
- Authorization check: Line 16-18
- Class determination: Line 26-34
- Permission validation: Line 37-46
- Date filtering: Line 49-56
- Main query: Line 61-77
