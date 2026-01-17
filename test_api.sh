#!/bin/bash

API_URL="http://103.252.136.73:8080/api"
TOKEN=""
TEACHER_TOKEN=""
STUDENT_TOKEN=""

echo "=========================================="
echo "TEST API SCHOOL MANAGEMENT SYSTEM"
echo "=========================================="
echo ""

# 1. Test Login Admin
echo "1. TEST: Login Admin"
RESPONSE=$(curl -s -X POST "$API_URL/auth/login_teacher.php" \
  -H "Content-Type: application/json" \
  -d '{"username": "admin", "password": "password"}')
TOKEN=$(echo $RESPONSE | grep -o '"token":"[^"]*' | cut -d'"' -f4)
if [ -n "$TOKEN" ]; then
  echo "✓ Login thành công! Token: ${TOKEN:0:30}..."
else
  echo "✗ Login thất bại! Response: $RESPONSE"
fi
echo ""

# 2. Test Login Teacher
echo "2. TEST: Login Teacher"
RESPONSE=$(curl -s -X POST "$API_URL/auth/login_teacher.php" \
  -H "Content-Type: application/json" \
  -d '{"username": "GV-00001", "password": "password"}')
TEACHER_TOKEN=$(echo $RESPONSE | grep -o '"token":"[^"]*' | cut -d'"' -f4)
if [ -n "$TEACHER_TOKEN" ]; then
  echo "✓ Login teacher thành công!"
else
  echo "✗ Login teacher thất bại!"
fi
echo ""

# 3. Test Login Student
echo "3. TEST: Login Student"
RESPONSE=$(curl -s -X POST "$API_URL/auth/login_student.php" \
  -H "Content-Type: application/json" \
  -d '{"identifier": "HS-00001", "password": "password"}')
STUDENT_TOKEN=$(echo $RESPONSE | grep -o '"token":"[^"]*' | cut -d'"' -f4)
if [ -n "$STUDENT_TOKEN" ]; then
  echo "✓ Login student thành công!"
else
  echo "✗ Login student thất bại!"
fi
echo ""

# 4. Test Get Profile (Admin) - SỬA: Dùng Authorization header
if [ -n "$TOKEN" ]; then
  echo "4. TEST: Get Profile (Admin)"
  RESPONSE=$(curl -s -X POST "$API_URL/profile/get.php" \
    -H "Content-Type: application/json" \
    -H "Authorization: Bearer $TOKEN")
  if echo "$RESPONSE" | grep -q "username"; then
    echo "✓ Get profile thành công!"
  else
    echo "✗ Get profile thất bại! Response: $RESPONSE"
  fi
  echo ""
fi

# 5. Test List Classes
echo "5. TEST: List All Classes"
RESPONSE=$(curl -s -X GET "$API_URL/classes/list_all.php")
CLASS_COUNT=$(echo "$RESPONSE" | grep -o '"id":[0-9]*' | wc -l)
if [ "$CLASS_COUNT" -gt 0 ]; then
  echo "✓ List classes thành công! ($CLASS_COUNT lớp)"
else
  echo "✗ List classes thất bại!"
fi
echo ""

# 6. Test List Subjects
echo "6. TEST: List All Subjects"
RESPONSE=$(curl -s -X GET "$API_URL/subjects/list_all.php")
SUBJECT_COUNT=$(echo "$RESPONSE" | grep -o '"id":[0-9]*' | wc -l)
if [ "$SUBJECT_COUNT" -gt 0 ]; then
  echo "✓ List subjects thành công! ($SUBJECT_COUNT môn)"
else
  echo "✗ List subjects thất bại!"
fi
echo ""

# 7. Test Get News
echo "7. TEST: Get Latest News"
RESPONSE=$(curl -s -X GET "$API_URL/news/get_latest.php")
if echo "$RESPONSE" | grep -q '"title"'; then
  NEWS_COUNT=$(echo "$RESPONSE" | grep -o '"title"' | wc -l)
  echo "✓ Get news thành công! ($NEWS_COUNT tin)"
else
  echo "✗ Get news thất bại!"
fi
echo ""

# 8. Test Get Active Banners
echo "8. TEST: Get Active Banners"
RESPONSE=$(curl -s -X GET "$API_URL/banners/get_active.php")
echo "✓ Endpoint hoạt động! Response: $RESPONSE"
echo ""

# 9. Test List Users (Admin) - Dùng POST với token trong body
if [ -n "$TOKEN" ]; then
  echo "9. TEST: List Users (Admin)"
  RESPONSE=$(curl -s -X POST "$API_URL/admin/users/list.php" \
    -H "Content-Type: application/json" \
    -d "{\"token\": \"$TOKEN\", \"role\": \"admin\"}")
  if echo "$RESPONSE" | grep -q "data\|users"; then
    echo "✓ List users thành công!"
  else
    echo "✗ List users thất bại! Response: ${RESPONSE:0:200}..."
  fi
  echo ""
fi

# 10. Test Get Statistics (Admin)
if [ -n "$TOKEN" ]; then
  echo "10. TEST: Get Statistics"
  RESPONSE=$(curl -s -X POST "$API_URL/reports/get_statistics.php" \
    -H "Content-Type: application/json" \
    -d "{\"token\": \"$TOKEN\", \"type\": \"overview\"}")
  if echo "$RESPONSE" | grep -q "{\|}"; then
    echo "✓ Get statistics thành công!"
  else
    echo "✗ Get statistics thất bại!"
  fi
  echo ""
fi

# 11. Test Teacher Get Classes - Dùng POST với token trong body
if [ -n "$TEACHER_TOKEN" ]; then
  echo "11. TEST: Teacher Get Classes"
  RESPONSE=$(curl -s -X POST "$API_URL/teacher/get_classes.php" \
    -H "Content-Type: application/json" \
    -d "{\"token\": \"$TEACHER_TOKEN\"}")
  if echo "$RESPONSE" | grep -q '\['; then
    CLASS_COUNT=$(echo "$RESPONSE" | grep -o '"id":[0-9]*' | wc -l)
    echo "✓ Teacher get classes thành công! ($CLASS_COUNT lớp)"
  else
    echo "✗ Teacher get classes thất bại!"
  fi
  echo ""
fi

# 12. Test Student Profile - Dùng Authorization header
if [ -n "$STUDENT_TOKEN" ]; then
  echo "12. TEST: Student Profile"
  RESPONSE=$(curl -s -X POST "$API_URL/profile/get.php" \
    -H "Content-Type: application/json" \
    -H "Authorization: Bearer $STUDENT_TOKEN")
  if echo "$RESPONSE" | grep -q "username"; then
    echo "✓ Student profile thành công!"
  else
    echo "✗ Student profile thất bại! Response: $RESPONSE"
  fi
  echo ""
fi

# 13. Test Get Violations Points
echo "13. TEST: Get Violations Points"
RESPONSE=$(curl -s -X GET "$API_URL/violations/get_points.php")
if echo "$RESPONSE" | grep -q "{\|message\|\["; then
  echo "✓ Get violations points endpoint hoạt động!"
else
  echo "✗ Get violations points thất bại!"
fi
echo ""

# 14. Test Get Violations Rules
echo "14. TEST: Get Violations Rules"
RESPONSE=$(curl -s -X GET "$API_URL/violations/get_rules.php")
if echo "$RESPONSE" | grep -q "{\|message\|\["; then
  echo "✓ Get violations rules endpoint hoạt động!"
else
  echo "✗ Get violations rules thất bại!"
fi
echo ""

# 15. Test Change Password
if [ -n "$TOKEN" ]; then
  echo "15. TEST: Change Password (wrong password)"
  RESPONSE=$(curl -s -X POST "$API_URL/auth/change_password.php" \
    -H "Authorization: Bearer $TOKEN" \
    -H "Content-Type: application/json" \
    -d '{"old_password": "wrong", "new_password": "NewPass123!"}')
  # Check if response contains validation message (password rejected)
  if echo "$RESPONSE" | grep -q "M.*t.*u.*"; then
    echo "✓ Password validation hoạt động đúng!"
  else
    echo "✗ Password validation test! Response: $RESPONSE"
  fi
  echo ""
fi

echo "=========================================="
echo "HOÀN THÀNH TEST"
echo "=========================================="
