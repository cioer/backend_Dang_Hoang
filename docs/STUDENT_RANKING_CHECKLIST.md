# Student Ranking Feature - Checklist kiểm tra

## ✅ Các vấn đề đã được phân tích

### 1. Code Structure (api/teacher/get_student_ranking.php)

#### ✓ Xác thực và phân quyền
- [x] Kiểm tra role (teacher/admin) - Line 16-18
- [x] Kiểm tra quyền truy cập lớp với 3 điều kiện:
  - Giáo viên chủ nhiệm (`homeroom_teacher_id`)
  - Giáo viên được phân công (`class_teacher_assignments`)
  - Giáo viên dạy theo lịch (`schedule`)

#### ✓ Xử lý tham số
- [x] `class_id`: Có fallback về lớp chủ nhiệm nếu không truyền
- [x] `start_date`, `end_date`: Lọc vi phạm theo khoảng thời gian
- [x] Validate cả hai ngày phải có để filter hoạt động

#### ✓ Query Database
- [x] Sử dụng `LEFT JOIN` để bao gồm học sinh không vi phạm
- [x] `COALESCE(SUM(cr.points), 0)` xử lý NULL
- [x] Sắp xếp đúng: điểm trừ ASC → số vi phạm ASC → tên ASC
- [x] GROUP BY đầy đủ các cột non-aggregate

#### ✓ Error Handling
- [x] PDOException catch - Line 80
- [x] Throwable catch (lỗi hệ thống) - Line 82-84
- [x] Hiển thị lỗi chi tiết (có thể tắt khi production) - Line 2-5

## ⚠️ Các vấn đề tiềm ẩn cần kiểm tra

### 1. Performance Issues

#### Vấn đề: Query có thể chậm với lớp đông học sinh
```sql
-- Query hiện tại không có index optimization hint
LEFT JOIN violations v ON u.id = v.student_id
LEFT JOIN conduct_rules cr ON v.rule_id = cr.id
```

**Kiểm tra cần làm**:
- [ ] Kiểm tra index trên `violations.student_id`
- [ ] Kiểm tra index trên `violations.created_at` (cho date filter)
- [ ] Kiểm tra index trên `student_details.class_id`
- [ ] Test performance với lớp >50 học sinh

**SQL kiểm tra index**:
```sql
SHOW INDEX FROM violations WHERE Key_name LIKE '%student%';
SHOW INDEX FROM violations WHERE Key_name LIKE '%created%';
SHOW INDEX FROM student_details WHERE Key_name LIKE '%class%';
```

### 2. Logic Edge Cases

#### Case 1: Giáo viên không có lớp chủ nhiệm
```php
// Line 26-30: Nếu không tìm thấy lớp chủ nhiệm
if (!$class_id) {
    Response::error('Không tìm thấy lớp học quản lý.', 404);
}
```

**Vấn đề**: Giáo viên chỉ dạy theo lịch (không phải chủ nhiệm) sẽ bị lỗi 404 khi không truyền `class_id`.

**Kiểm tra**:
- [ ] Test với tài khoản giáo viên không phải chủ nhiệm
- [ ] Xem có nên fallback về lớp được phân công đầu tiên không

#### Case 2: Date filter chỉ có một tham số
```php
// Line 52-56: Chỉ apply filter khi CẢ HAI ngày đều có
if ($start_date && $end_date) {
    $dateCondition = " AND (v.created_at BETWEEN :start_date AND :end_date) ";
}
```

**Vấn đề**: Nếu client chỉ gửi `start_date` hoặc `end_date`, filter bị bỏ qua im lặng.

**Kiểm tra**:
- [ ] Test với chỉ `start_date`
- [ ] Test với chỉ `end_date`
- [ ] Xem có nên báo lỗi validation không

#### Case 3: Lớp rỗng (không có học sinh)
**Kiểm tra**:
- [ ] Test với lớp mới chưa có học sinh
- [ ] Verify trả về empty array thay vì lỗi

#### Case 4: Violation không có rule_id hợp lệ
```sql
LEFT JOIN conduct_rules cr ON v.rule_id = cr.id
```

**Vấn đề**: Nếu có violation với `rule_id` không tồn tại, `cr.points` sẽ NULL.

**Kiểm tra**:
- [ ] Kiểm tra ràng buộc foreign key `violations.rule_id → conduct_rules.id`
- [ ] Test với data có orphan violation records

### 3. Security Issues

#### Debug Mode trong Production
```php
// Line 2-5: BẬT hiển thị lỗi
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
```

**⚠️ NGUY HIỂM**: Lộ thông tin database, đường dẫn file khi production

**Kiểm tra**:
- [ ] Tắt debug mode khi deploy production
- [ ] Sử dụng error logging thay vì display

#### SQL Injection (Đã được bảo vệ)
✅ Sử dụng prepared statements với parameterized queries
✅ Không có string concatenation trong SQL

### 4. Data Consistency

#### Xung đột giữa student_details.class_id và users.class_id
**Kiểm tra**:
- [ ] Xem table `users` có cột `class_id` không (cho Red Star)
- [ ] Verify học sinh chỉ dùng `student_details.class_id`
- [ ] Kiểm tra không có conflict giữa hai nguồn dữ liệu

### 5. Frontend Integration

#### Response format
```json
{
    "success": true,
    "data": [...]
}
```

**Kiểm tra**:
- [ ] Verify Android app parse đúng format
- [ ] Test với empty result
- [ ] Test với lớp đông (>100 học sinh)

## 🔧 Các test cần chạy

### Test 1: Teacher có lớp chủ nhiệm
```bash
# Login as teacher with homeroom class
TOKEN=$(curl -s -X POST http://103.252.136.73:8080/api/auth/login.php \
  -H "Content-Type: application/json" \
  -d '{"username":"teacher1","password":"password"}' | jq -r '.data.token')

# Get ranking without class_id (should use homeroom class)
curl -X GET "http://103.252.136.73:8080/api/teacher/get_student_ranking.php" \
  -H "Authorization: Bearer $TOKEN"
```

### Test 2: Teacher không có lớp chủ nhiệm
```bash
# Should return 404 or fallback to assigned class
curl -X GET "http://103.252.136.73:8080/api/teacher/get_student_ranking.php" \
  -H "Authorization: Bearer $TOKEN"
```

### Test 3: Date filter
```bash
# Current month
START_DATE=$(date +%Y-%m-01)
END_DATE=$(date +%Y-%m-%d)

curl -X GET "http://103.252.136.73:8080/api/teacher/get_student_ranking.php?class_id=5&start_date=$START_DATE&end_date=$END_DATE" \
  -H "Authorization: Bearer $TOKEN"
```

### Test 4: Invalid date filter (only start_date)
```bash
# Should ignore filter or return error
curl -X GET "http://103.252.136.73:8080/api/teacher/get_student_ranking.php?class_id=5&start_date=2026-01-01" \
  -H "Authorization: Bearer $TOKEN"
```

### Test 5: Permission check
```bash
# Teacher tries to access other teacher's class
curl -X GET "http://103.252.136.73:8080/api/teacher/get_student_ranking.php?class_id=999" \
  -H "Authorization: Bearer $TOKEN"
# Expected: 403 Forbidden
```

### Test 6: Performance test
```bash
# Class with many students (>50)
time curl -X GET "http://103.252.136.73:8080/api/teacher/get_student_ranking.php?class_id=5" \
  -H "Authorization: Bearer $TOKEN"
# Should complete in <1 second
```

## 📊 Database Checks

### Check 1: Index existence
```sql
-- Violations table indexes
SHOW INDEX FROM violations;

-- Expected indexes:
-- - student_id (for JOIN)
-- - rule_id (for JOIN)
-- - created_at (for date filter)

-- Student details indexes
SHOW INDEX FROM student_details;

-- Expected:
-- - user_id
-- - class_id
```

### Check 2: Foreign key constraints
```sql
-- Check violations.rule_id constraint
SELECT
    CONSTRAINT_NAME,
    REFERENCED_TABLE_NAME,
    REFERENCED_COLUMN_NAME
FROM information_schema.KEY_COLUMN_USAGE
WHERE TABLE_NAME = 'violations'
  AND REFERENCED_TABLE_NAME IS NOT NULL;

-- Should show FK to conduct_rules(id)
```

### Check 3: Data consistency
```sql
-- Check for orphan violations (rule_id not exists)
SELECT COUNT(*) as orphan_violations
FROM violations v
LEFT JOIN conduct_rules cr ON v.rule_id = cr.id
WHERE cr.id IS NULL;

-- Should be 0
```

### Check 4: Sample data verification
```sql
-- Get a student with violations
SELECT
    u.id,
    u.full_name,
    COUNT(v.id) as violation_count,
    SUM(cr.points) as total_deducted
FROM users u
JOIN student_details sd ON u.id = sd.user_id
LEFT JOIN violations v ON u.id = v.student_id
LEFT JOIN conduct_rules cr ON v.rule_id = cr.id
WHERE sd.class_id = 5
GROUP BY u.id, u.full_name
LIMIT 5;

-- Manually verify the calculation
```

## 📝 Recommendations

### Immediate Actions
1. ⚠️ **TẮT DEBUG MODE trong production** (Line 2-5)
2. 🔍 Kiểm tra indexes trên `violations` table
3. ✅ Test với teacher không có lớp chủ nhiệm

### Future Improvements
1. **Pagination**: Thêm `limit` và `offset` để xử lý lớp đông
2. **Caching**: Cache kết quả trong 5-10 phút cho lớp đông
3. **Validation**: Validate format ngày tháng
4. **Logging**: Log access để audit

## 🎯 Kết luận

**Điểm mạnh**:
- ✅ Code structure tốt, sử dụng prepared statements
- ✅ Authorization logic đầy đủ
- ✅ Error handling comprehensive
- ✅ LEFT JOIN đúng để bao gồm học sinh không vi phạm

**Điểm cần cải thiện**:
- ⚠️ Debug mode cần tắt khi production
- ⚠️ Cần kiểm tra performance với data lớn
- ⚠️ Cần handle edge case: teacher không có lớp chủ nhiệm
- ⚠️ Cần validate date parameters

**Tổng thể**: Tính năng hoạt động tốt, cần kiểm tra một số edge cases và tối ưu performance.
