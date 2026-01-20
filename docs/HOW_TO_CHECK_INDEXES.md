# Hướng dẫn Kiểm tra Database Indexes

## Tại sao cần kiểm tra indexes?

Indexes giúp database truy vấn dữ liệu nhanh hơn. Nếu thiếu indexes, API `get_student_ranking.php` có thể chậm khi lớp có nhiều học sinh hoặc nhiều vi phạm.

## Cách 1: Chạy trực tiếp trên VPS (Khuyến nghị)

### Bước 1: SSH vào VPS

```bash
ssh deploy@huuthang.online
```

### Bước 2: Đi đến thư mục project

```bash
cd /home/deploy/backend_Dang_Hoang
```

### Bước 3: Pull code mới nhất

```bash
git pull origin main
```

### Bước 4: Chạy script kiểm tra

```bash
bash scripts/check_indexes.sh
```

### Bước 5: Đọc kết quả

Script sẽ hiển thị:
- ✓ OK: Index đã tồn tại
- ✗ MISSING: Thiếu index, cần tạo
- ⚠ MISSING: Khuyến nghị nên có nhưng không bắt buộc

## Cách 2: Chạy từ máy local (qua SSH)

```bash
# Từ thư mục project trên máy local
bash scripts/remote_check_indexes.sh
```

## Cách 3: Kiểm tra thủ công bằng MySQL

### Bước 1: Kết nối vào MySQL container

```bash
ssh deploy@huuthang.online
docker exec -it school_management_db mysql -uroot -proot_password school_management
```

### Bước 2: Chạy queries kiểm tra

```sql
-- Kiểm tra indexes của bảng violations
SHOW INDEX FROM violations;

-- Kiểm tra indexes của bảng student_details
SHOW INDEX FROM student_details;

-- Kiểm tra indexes của bảng conduct_rules
SHOW INDEX FROM conduct_rules;
```

### Bước 3: Kiểm tra query performance

```sql
-- Test query execution plan
EXPLAIN SELECT
    u.id as student_id,
    u.full_name,
    u.username as student_code,
    COALESCE(SUM(cr.points), 0) as total_deducted,
    COUNT(v.id) as violation_count
FROM student_details sd
JOIN users u ON sd.user_id = u.id
LEFT JOIN violations v ON u.id = v.student_id
LEFT JOIN conduct_rules cr ON v.rule_id = cr.id
WHERE sd.class_id = 1
GROUP BY u.id, u.full_name, u.username
ORDER BY total_deducted ASC, violation_count ASC, u.full_name ASC;
```

**Kết quả tốt**:
- `type`: `ref` hoặc `eq_ref` (không phải `ALL`)
- `possible_keys`: Có danh sách indexes
- `key`: Index được sử dụng
- `rows`: Số lượng hàng quét (càng ít càng tốt)

**Kết quả xấu**:
- `type`: `ALL` (full table scan)
- `key`: `NULL` (không dùng index)
- `Extra`: `Using filesort`, `Using temporary`

## Nếu thiếu indexes, làm gì?

### Option 1: Chạy migration tự động (Khuyến nghị)

```bash
# SSH vào VPS
ssh deploy@huuthang.online
cd /home/deploy/backend_Dang_Hoang

# Chạy migration
docker exec -i school_management_db mysql -uroot -proot_password school_management < migrations/add_missing_indexes.sql
```

Migration này sẽ:
- Tự động kiểm tra index đã tồn tại chưa
- Chỉ tạo index nếu chưa có
- An toàn chạy nhiều lần

### Option 2: Tạo indexes thủ công

```bash
# Kết nối MySQL
docker exec -it school_management_db mysql -uroot -proot_password school_management
```

```sql
-- Tạo index cho violations.student_id
CREATE INDEX idx_violations_student_id ON violations(student_id);

-- Tạo index cho violations.created_at (date filtering)
CREATE INDEX idx_violations_created_at ON violations(created_at);

-- Tạo composite index (tối ưu nhất cho query)
CREATE INDEX idx_violations_student_date ON violations(student_id, created_at);

-- Tạo index cho violations.rule_id
CREATE INDEX idx_violations_rule_id ON violations(rule_id);

-- Tạo index cho student_details.class_id
CREATE INDEX idx_student_details_class_id ON student_details(class_id);

-- Tạo index cho student_details.user_id
CREATE INDEX idx_student_details_user_id ON student_details(user_id);

-- Verify indexes đã được tạo
SHOW INDEX FROM violations;
SHOW INDEX FROM student_details;
```

## Kiểm tra sau khi tạo indexes

### 1. Verify indexes đã được tạo

```bash
bash scripts/check_indexes.sh
```

Tất cả phải hiển thị ✓ OK

### 2. Test performance trước và sau

```bash
# Đo thời gian response của API
time curl -X GET "http://103.252.136.73:8080/api/teacher/get_student_ranking.php?class_id=5" \
  -H "Authorization: Bearer YOUR_TOKEN"
```

**Trước khi có indexes**: 2-5 giây (với lớp đông)
**Sau khi có indexes**: <500ms

### 3. Kiểm tra EXPLAIN plan

```sql
-- Chạy lại query EXPLAIN
EXPLAIN SELECT ...;
```

Kết quả phải có:
- `type`: `ref` thay vì `ALL`
- `key`: Tên của index được sử dụng
- `rows`: Giảm đáng kể

## Indexes nào quan trọng nhất?

### 🔴 CỰC KỲ QUAN TRỌNG (Phải có)

1. **`violations.student_id`**
   - Dùng cho JOIN với users
   - Không có → Full table scan → Cực chậm

2. **`student_details.class_id`**
   - Dùng cho WHERE clause
   - Không có → Scan toàn bộ student_details

### 🟡 QUAN TRỌNG (Nên có)

3. **`violations.created_at`**
   - Dùng cho date range filter
   - Không có → Lọc ngày tháng sẽ chậm

4. **`violations.rule_id`**
   - Dùng cho JOIN với conduct_rules
   - Không có → Tính điểm trừ chậm

### 🟢 TỐI ƯU (Nice to have)

5. **`violations(student_id, created_at)` composite index**
   - Tối ưu cho query có cả filter date
   - Không bắt buộc nhưng performance tốt hơn

## Troubleshooting

### Lỗi: "Permission denied"

```bash
# Đảm bảo script executable
chmod +x scripts/check_indexes.sh
```

### Lỗi: "docker: command not found"

```bash
# Chạy từ VPS, không phải máy local
ssh deploy@huuthang.online
cd /home/deploy/backend_Dang_Hoang
bash scripts/check_indexes.sh
```

### Lỗi: "Access denied for user"

```bash
# Kiểm tra MySQL credentials trong script
# Mặc định: root / root_password
# Sửa trong scripts/check_indexes.sh nếu khác
```

### Query vẫn chậm sau khi thêm indexes

1. Kiểm tra lại indexes đã được tạo:
   ```sql
   SHOW INDEX FROM violations;
   ```

2. Analyze tables để MySQL cập nhật statistics:
   ```sql
   ANALYZE TABLE violations;
   ANALYZE TABLE student_details;
   ANALYZE TABLE conduct_rules;
   ```

3. Kiểm tra số lượng data:
   ```sql
   SELECT COUNT(*) FROM violations;
   SELECT COUNT(*) FROM student_details;
   ```

   Nếu >100,000 records → Cần xem xét thêm cache hoặc pagination

## Best Practices

1. **Luôn test trên staging trước khi production**
2. **Backup database trước khi tạo indexes lớn**
3. **Tạo indexes trong giờ thấp điểm** (nếu data lớn)
4. **Monitor query performance** sau khi thêm indexes
5. **Định kỳ ANALYZE tables** để MySQL cập nhật statistics

## Kết luận

- Chạy `scripts/check_indexes.sh` để kiểm tra
- Nếu thiếu indexes → Chạy `migrations/add_missing_indexes.sql`
- Verify lại bằng cách chạy check script một lần nữa
- Test API performance để đảm bảo cải thiện

Nếu có vấn đề, tham khảo thêm:
- `docs/STUDENT_RANKING_CHECKLIST.md`
- `tests/check_student_ranking_db.sql`
