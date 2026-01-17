# Hệ Thống Kiểm Thử Quản Lý Nề Nếp

## Tổng Quan

Tài liệu này mô tả toàn bộ hệ thống kiểm thử cho ứng dụng Quản Lý Nề Nếp, bao gồm các test case, phương pháp kiểm thử, và hướng dẫn sử dụng.

## Cấu Trúc Test Suite

### 1. Test Categories

#### 1.1 Unit Tests (PHP)
- **Location**: `Backend/tests/`
- **Purpose**: Kiểm thử các thành phần riêng lẻ của backend
- **Files**:
  - `banners_get_active_test.php` - Test hệ thống banner
  - `stream_test.php` - Test SSE streaming

#### 1.2 Integration Tests (Python)
- **Location**: `Backend/tests/`
- **Purpose**: Kiểm thử tích hợp giữa các thành phần
- **Files**:
  - `test_violations_comprehensive.py` - Test chức năng violations đầy đủ
  - `system_validation_test.py` - Test validation toàn hệ thống

#### 1.3 Auto-fix Scripts (PHP)
- **Location**: `Backend/scripts/`
- **Purpose**: Tự động phát hiện và sửa lỗi
- **Files**:
  - `auto_fix_errors.php` - Script tự động sửa lỗi database

### 2. Test Scenarios

#### 2.1 Login & Authentication
- ✅ Login thành công cho admin, teacher, student
- ✅ Login với thông tin sai
- ✅ Login với thiếu thông tin
- ✅ Protection chống SQL injection
- ✅ Token validation

#### 2.2 Violation Management
- ✅ Submit violation hợp lệ
- ✅ Submit violation với student không hợp lệ
- ✅ Submit violation với thiếu thông tin
- ✅ Permission check (chỉ teacher/admin được submit)
- ✅ Violation thresholds và notifications
- ✅ Violation statistics

#### 2.3 Conduct Scoring
- ✅ Submit điểm hạnh kiểm
- ✅ Validation dữ liệu đầu vào
- ✅ Permission check

#### 2.4 Attendance
- ✅ Submit điểm danh
- ✅ Cập nhật điểm danh đã tồn tại
- ✅ Validation dữ liệu

#### 2.5 Role-Based Access Control
- ✅ Admin access to admin endpoints
- ✅ Teacher access to teacher endpoints
- ✅ Student access restrictions
- ✅ Cross-role access prevention

#### 2.6 Red Committee (Sao Đỏ)
- ✅ Get committee list
- ✅ Add committee member
- ✅ Teacher access to committee features

#### 2.7 Notification System
- ✅ Get notifications
- ✅ Create notifications
- ✅ Target role filtering

#### 2.8 Banner System
- ✅ Get active banners
- ✅ SSE streaming functionality

#### 2.9 Error Handling
- ✅ Malformed JSON handling
- ✅ Missing required fields
- ✅ SQL injection protection
- ✅ Proper HTTP status codes

### 3. Auto-fix Features

#### 3.1 Foreign Key Constraints
- Tự động phát hiện orphaned records
- Fix bằng cách set NULL hoặc delete
- Kiểm tra referential integrity

#### 3.2 Duplicate Key Errors
- Tìm duplicate primary keys
- Giữ lại record đầu tiên, xóa duplicates
- Reset auto-increment values

#### 3.3 Character Encoding
- Convert tables sang utf8mb4
- Fix Vietnamese character issues
- Đảm bảo proper collation

#### 3.4 Missing Columns
- Tự động thêm columns thiếu
- Định nghĩa schema chuẩn
- Validation column structure

#### 3.5 Auto-increment Issues
- Reset auto-increment values
- Sửa lỗi sequence
- Optimize table performance

## Hướng Dẫn Sử Dụng

### Prerequisites
```bash
# Install Python dependencies
pip install requests mysql-connector-python

# Ensure PHP CLI is available
php --version

# MySQL server running
mysql --version
```

### Chạy Toàn Bộ Test Suite
```bash
cd Backend/tests
python run_all_tests.py
```

### Chạy Test Cụ Thể
```bash
# Python tests
python test_violations_comprehensive.py
python system_validation_test.py

# PHP tests
php banners_get_active_test.php
php stream_test.php
```

### Chạy Auto-fix Scripts
```bash
cd Backend/scripts

# Run all fixes
php auto_fix_errors.php

# Run specific fixes
php auto_fix_errors.php foreign_keys
php auto_fix_errors.php duplicates
php auto_fix_errors.php encoding
php auto_fix_errors.php columns
php auto_fix_errors.php auto_increment
php auto_fix_errors.php report
```

### Check Database Status
```bash
cd Backend
php check_records.php
# or
./check_db.bat  # Windows
```

## Test Data

### Sample Users (10 records each role)
- **Admin**: admin / 123456
- **Teachers**: gv00001-gv00008 / 123456
- **Students**: hs00001-hs00008 / 123456

### Sample Classes
- 10A1, 10A2, 10A3, 11A1, 11A2, 12A1, 12A2, 12A3

### Sample Violations
- Đi muộn, Không làm bài tập, Quên sách vở, Nói chuyện riêng, Không mặc đồng phục

## Expected Results

### Login Tests
- ✅ All valid logins return 200 + token
- ✅ Invalid logins return 401
- ✅ Missing credentials return 400

### Violation Tests
- ✅ Valid submissions return 200 + points
- ✅ Invalid student ID returns 403
- ✅ Missing fields return 400
- ✅ Unauthorized access returns 403

### Database Tests
- ✅ All tables have 10 records each
- ✅ No duplicate key errors
- ✅ Foreign key constraints working
- ✅ Character encoding proper

## Error Codes

| Code | Meaning | Test Case |
|------|---------|-----------|
| 200 | Success | Valid operations |
| 400 | Bad Request | Missing/invalid data |
| 401 | Unauthorized | Invalid credentials |
| 403 | Forbidden | Insufficient permissions |
| 503 | Service Error | Database/system error |

## Troubleshooting

### Common Issues

1. **Server not accessible**
   - Check Apache/Nginx running
   - Verify localhost:80 accessible
   - Check PHP module loaded

2. **Database connection failed**
   - Ensure MySQL running
   - Check database exists: `nenep_management`
   - Verify user/password: root/''

3. **Permission denied errors**
   - Check file permissions
   - Ensure PHP has DB access
   - Verify web server user permissions

4. **Character encoding issues**
   - Run encoding fix: `php auto_fix_errors.php encoding`
   - Check database collation
   - Verify PHP charset settings

### Debug Mode
```bash
# Enable debug output
export DEBUG=1
python system_validation_test.py

# Check PHP errors
php -d display_errors=1 test_file.php
```

## Performance Metrics

### Test Execution Time
- **Full suite**: ~2-3 minutes
- **Violations only**: ~30 seconds
- **Login tests**: ~10 seconds

### Database Performance
- **10 records/table**: Optimal for testing
- **Query response**: <100ms expected
- **Concurrent users**: 10+ simultaneous

## Maintenance

### Regular Tasks
1. **Weekly**: Run full test suite
2. **Monthly**: Check database integrity
3. **After updates**: Run relevant tests
4. **Before deployment**: Complete validation

### Test Data Refresh
```sql
-- Reset test data if needed
source Backend/sql/seed_10_each.sql
```

### Log Analysis
- Check `error_report_*.json` files
- Review test output for patterns
- Monitor failed test trends

## Future Enhancements

### Planned Features
1. **Load Testing**: Test performance với 1000+ users
2. **Security Testing**: Penetration testing
3. **API Documentation**: Swagger/OpenAPI
4. **CI/CD Integration**: GitHub Actions
5. **Test Coverage**: Code coverage analysis

### Additional Test Cases
- Mobile app testing
- Browser compatibility
- Network failure scenarios
- Data corruption recovery
- Backup/restore procedures

---

**Note**: Hệ thống kiểm thử này được thiết kế để đảm bảo chất lượng và độ tin cậy của ứng dụng Quản Lý Nề Nếp. Mọi thay đổi trong codebase nên được validate qua test suite trước khi deployment.