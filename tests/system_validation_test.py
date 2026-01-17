#!/usr/bin/env python3
"""
System validation test suite for Nenep Management System
Kiểm tra toàn bộ hệ thống sau khi cập nhật dữ liệu mới
"""

import requests
import json
import time
import random
from datetime import datetime

# Configuration
BASE_URL = "http://localhost/Backend/api"
TEST_RESULTS = {
    "passed": 0,
    "failed": 0,
    "total": 0
}

def log_test(test_name, status, message=""):
    """Log test results with color coding and update statistics"""
    timestamp = datetime.now().strftime("%H:%M:%S")
    TEST_RESULTS["total"] += 1
    
    if status == "PASS":
        TEST_RESULTS["passed"] += 1
        print(f"✅ [{timestamp}] {test_name}: {message}")
    elif status == "FAIL":
        TEST_RESULTS["failed"] += 1
        print(f"❌ [{timestamp}] {test_name}: {message}")
    else:
        print(f"⚠️  [{timestamp}] {test_name}: {message}")

def login_user(username, password, role):
    """Login and return token"""
    try:
        if role == 'teacher':
            response = requests.post(f"{BASE_URL}/auth/login_teacher.php", 
                                   json={"username": username, "password": password})
        elif role == 'admin':
            response = requests.post(f"{BASE_URL}/login.php", 
                                   json={"username": username, "password": password})
        else:
            response = requests.post(f"{BASE_URL}/auth/login_student.php", 
                                   json={"username": username, "password": password})
        
        if response.status_code == 200:
            data = response.json()
            return data.get('token')
        return None
    except Exception as e:
        log_test(f"Login {username}", "FAIL", str(e))
        return None

def test_login_functionality():
    """Test all login scenarios"""
    log_test("=== TESTING LOGIN FUNCTIONALITY ===", "INFO")
    
    # Test admin login
    token = login_user("admin", "123456", "admin")
    if token:
        log_test("Admin login", "PASS", "Login successful")
    else:
        log_test("Admin login", "FAIL", "Login failed")
    
    # Test teacher login
    token = login_user("gv00001", "123456", "teacher")
    if token:
        log_test("Teacher login", "PASS", "Login successful")
    else:
        log_test("Teacher login", "FAIL", "Login failed")
    
    # Test student login
    token = login_user("hs00001", "123456", "student")
    if token:
        log_test("Student login", "PASS", "Login successful")
    else:
        log_test("Student login", "FAIL", "Login failed")
    
    # Test invalid login
    response = requests.post(f"{BASE_URL}/login.php", 
                           json={"username": "invalid", "password": "wrong"})
    if response.status_code == 401:
        log_test("Invalid login", "PASS", "Correctly rejected")
    else:
        log_test("Invalid login", "FAIL", f"Expected 401, got {response.status_code}")

def test_database_integrity():
    """Test database integrity"""
    log_test("=== TESTING DATABASE INTEGRITY ===", "INFO")
    
    try:
        # Test admin login to get token
        admin_token = login_user("admin", "123456", "admin")
        if not admin_token:
            log_test("Database integrity", "FAIL", "Cannot login as admin")
            return
        
        # Test user list
        response = requests.get(f"{BASE_URL}/admin/users/list.php", 
                              headers={"Authorization": f"Bearer {admin_token}"})
        if response.status_code == 200:
            users = response.json()
            log_test("User list", "PASS", f"Found {len(users)} users")
        else:
            log_test("User list", "FAIL", f"Status: {response.status_code}")
        
        # Test class list
        response = requests.get(f"{BASE_URL}/classes/list_all.php")
        if response.status_code == 200:
            classes = response.json()
            log_test("Class list", "PASS", f"Found {len(classes)} classes")
        else:
            log_test("Class list", "FAIL", f"Status: {response.status_code}")
        
        # Test violation rules
        response = requests.get(f"{BASE_URL}/violations/get_rules.php")
        if response.status_code == 200:
            rules = response.json()
            log_test("Violation rules", "PASS", f"Found {len(rules)} rules")
        else:
            log_test("Violation rules", "FAIL", f"Status: {response.status_code}")
            
    except Exception as e:
        log_test("Database integrity", "FAIL", str(e))

def test_role_based_access():
    """Test role-based access control"""
    log_test("=== TESTING ROLE-BASED ACCESS ===", "INFO")
    
    # Login as different roles
    admin_token = login_user("admin", "123456", "admin")
    teacher_token = login_user("gv00001", "123456", "teacher")
    student_token = login_user("hs00001", "123456", "student")
    
    # Test admin access to admin endpoints
    if admin_token:
        response = requests.get(f"{BASE_URL}/admin/users/list.php", 
                              headers={"Authorization": f"Bearer {admin_token}"})
        if response.status_code == 200:
            log_test("Admin access to admin endpoints", "PASS")
        else:
            log_test("Admin access to admin endpoints", "FAIL", f"Status: {response.status_code}")
    
    # Test teacher access to teacher endpoints
    if teacher_token:
        response = requests.get(f"{BASE_URL}/teacher/get_classes.php", 
                              headers={"Authorization": f"Bearer {teacher_token}"})
        if response.status_code == 200:
            log_test("Teacher access to teacher endpoints", "PASS")
        else:
            log_test("Teacher access to teacher endpoints", "FAIL", f"Status: {response.status_code}")
    
    # Test student access to restricted endpoints (should fail)
    if student_token:
        response = requests.get(f"{BASE_URL}/admin/users/list.php", 
                              headers={"Authorization": f"Bearer {student_token}"})
        if response.status_code == 403 or response.status_code == 401:
            log_test("Student access to admin endpoints", "PASS", "Correctly denied")
        else:
            log_test("Student access to admin endpoints", "FAIL", f"Expected 403/401, got {response.status_code}")

def test_violation_workflow():
    """Test complete violation workflow"""
    log_test("=== TESTING VIOLATION WORKFLOW ===", "INFO")
    
    # Login as teacher
    teacher_token = login_user("gv00001", "123456", "teacher")
    if not teacher_token:
        log_test("Violation workflow", "FAIL", "Cannot login as teacher")
        return
    
    try:
        # Get students
        response = requests.get(f"{BASE_URL}/classes/list_all.php")
        if response.status_code != 200:
            log_test("Get classes", "FAIL", "Cannot get classes")
            return
        
        classes = response.json()
        if not classes:
            log_test("Violation workflow", "FAIL", "No classes available")
            return
        
        # Get violation rules
        response = requests.get(f"{BASE_URL}/violations/get_rules.php")
        if response.status_code != 200:
            log_test("Get violation rules", "FAIL", "Cannot get rules")
            return
        
        rules = response.json()
        if not rules:
            log_test("Violation workflow", "FAIL", "No rules available")
            return
        
        # Get students in first class
        class_id = classes[0]['id']
        response = requests.get(f"{BASE_URL}/teacher/get_students.php?class_id={class_id}", 
                              headers={"Authorization": f"Bearer {teacher_token}"})
        
        if response.status_code != 200:
            log_test("Get students", "FAIL", "Cannot get students")
            return
        
        students = response.json()
        if not students:
            log_test("Violation workflow", "FAIL", "No students in class")
            return
        
        # Submit violation
        student = students[0]
        rule = rules[0]
        
        payload = {
            "student_id": student['id'],
            "rule_id": rule['id'],
            "note": "Test violation from system validation"
        }
        
        response = requests.post(f"{BASE_URL}/violations/submit.php",
                               headers={"Authorization": f"Bearer {teacher_token}"},
                               json=payload)
        
        if response.status_code == 200:
            result = response.json()
            log_test("Submit violation", "PASS", f"Points: {result.get('points', 'N/A')}")
        else:
            log_test("Submit violation", "FAIL", f"Status: {response.status_code}")
        
        # Check violation stats
        response = requests.get(f"{BASE_URL}/violations/get_stats.php",
                              headers={"Authorization": f"Bearer {teacher_token}"})
        
        if response.status_code == 200:
            stats = response.json()
            log_test("Get violation stats", "PASS", f"Found {len(stats)} stats")
        else:
            log_test("Get violation stats", "FAIL", f"Status: {response.status_code}")
            
    except Exception as e:
        log_test("Violation workflow", "FAIL", str(e))

def test_attendance_workflow():
    """Test attendance workflow"""
    log_test("=== TESTING ATTENDANCE WORKFLOW ===", "INFO")
    
    # Login as teacher
    teacher_token = login_user("gv00001", "123456", "teacher")
    if not teacher_token:
        log_test("Attendance workflow", "FAIL", "Cannot login as teacher")
        return
    
    try:
        # Get classes
        response = requests.get(f"{BASE_URL}/teacher/get_classes.php", 
                              headers={"Authorization": f"Bearer {teacher_token}"})
        
        if response.status_code != 200:
            log_test("Get teacher classes", "FAIL", "Cannot get classes")
            return
        
        classes = response.json()
        if not classes:
            log_test("Attendance workflow", "FAIL", "No classes assigned to teacher")
            return
        
        # Get students in first class
        class_id = classes[0]['id']
        response = requests.get(f"{BASE_URL}/teacher/get_students.php?class_id={class_id}", 
                              headers={"Authorization": f"Bearer {teacher_token}"})
        
        if response.status_code != 200:
            log_test("Get students", "FAIL", "Cannot get students")
            return
        
        students = response.json()
        if not students:
            log_test("Attendance workflow", "FAIL", "No students in class")
            return
        
        # Submit attendance
        attendance_list = []
        for student in students[:3]:  # Test with 3 students
            attendance_list.append({
                "student_id": student['id'],
                "status": random.choice(["present", "absent", "late"]),
                "note": "Test attendance"
            })
        
        payload = {
            "token": teacher_token,
            "class_id": class_id,
            "date": datetime.now().strftime("%Y-%m-%d"),
            "attendance_list": attendance_list
        }
        
        response = requests.post(f"{BASE_URL}/attendance/submit.php",
                               json=payload)
        
        if response.status_code == 200:
            log_test("Submit attendance", "PASS", f"Students: {len(attendance_list)}")
        else:
            log_test("Submit attendance", "FAIL", f"Status: {response.status_code}")
            
    except Exception as e:
        log_test("Attendance workflow", "FAIL", str(e))

def test_notification_system():
    """Test notification system"""
    log_test("=== TESTING NOTIFICATION SYSTEM ===", "INFO")
    
    # Login as admin
    admin_token = login_user("admin", "123456", "admin")
    if not admin_token:
        log_test("Notification system", "FAIL", "Cannot login as admin")
        return
    
    try:
        # Get notifications
        response = requests.get(f"{BASE_URL}/notifications/get_all.php",
                              headers={"Authorization": f"Bearer {admin_token}"})
        
        if response.status_code == 200:
            notifications = response.json()
            log_test("Get notifications", "PASS", f"Found {len(notifications)} notifications")
        else:
            log_test("Get notifications", "FAIL", f"Status: {response.status_code}")
        
        # Create notification
        payload = {
            "title": "Test notification",
            "content": "This is a test notification from system validation",
            "target_role": "student"
        }
        
        response = requests.post(f"{BASE_URL}/notifications/create.php",
                               headers={"Authorization": f"Bearer {admin_token}"},
                               json=payload)
        
        if response.status_code == 200:
            log_test("Create notification", "PASS")
        else:
            log_test("Create notification", "FAIL", f"Status: {response.status_code}")
            
    except Exception as e:
        log_test("Notification system", "FAIL", str(e))

def test_red_committee_functionality():
    """Test Red Committee (Sao Đỏ) functionality"""
    log_test("=== TESTING RED COMMITTEE FUNCTIONALITY ===", "INFO")
    
    # Login as teacher
    teacher_token = login_user("gv00001", "123456", "teacher")
    if not teacher_token:
        log_test("Red committee", "FAIL", "Cannot login as teacher")
        return
    
    try:
        # Get red committee list
        response = requests.get(f"{BASE_URL}/red_committee/list.php",
                              headers={"Authorization": f"Bearer {teacher_token}"})
        
        if response.status_code == 200:
            committee = response.json()
            log_test("Get red committee list", "PASS", f"Found {len(committee)} members")
        else:
            log_test("Get red committee list", "FAIL", f"Status: {response.status_code}")
        
        # Test adding member (if students available)
        response = requests.get(f"{BASE_URL}/classes/list_all.php")
        if response.status_code == 200:
            classes = response.json()
            if classes:
                # Get students from first class
                class_id = classes[0]['id']
                response = requests.get(f"{BASE_URL}/teacher/get_students.php?class_id={class_id}", 
                                      headers={"Authorization": f"Bearer {teacher_token}"})
                
                if response.status_code == 200:
                    students = response.json()
                    if students:
                        student = students[0]
                        payload = {
                            "student_id": student['id'],
                            "position": "Member",
                            "note": "Test member from system validation"
                        }
                        
                        response = requests.post(f"{BASE_URL}/red_committee/add.php",
                                               headers={"Authorization": f"Bearer {teacher_token}"},
                                               json=payload)
                        
                        if response.status_code == 200:
                            log_test("Add red committee member", "PASS")
                        else:
                            log_test("Add red committee member", "FAIL", f"Status: {response.status_code}")
                        
    except Exception as e:
        log_test("Red committee functionality", "FAIL", str(e))

def test_banner_system():
    """Test banner system"""
    log_test("=== TESTING BANNER SYSTEM ===", "INFO")
    
    try:
        # Get active banners
        response = requests.get(f"{BASE_URL}/banners/get_active.php")
        
        if response.status_code == 200:
            banners = response.json()
            log_test("Get active banners", "PASS", f"Found {len(banners)} banners")
        else:
            log_test("Get active banners", "FAIL", f"Status: {response.status_code}")
        
        # Test SSE stream
        response = requests.get(f"{BASE_URL}/banners/stream.php", timeout=5)
        if response.status_code == 200:
            log_test("Banner SSE stream", "PASS", "Stream accessible")
        else:
            log_test("Banner SSE stream", "FAIL", f"Status: {response.status_code}")
            
    except Exception as e:
        log_test("Banner system", "FAIL", str(e))

def test_api_endpoints():
    """Test all major API endpoints"""
    log_test("=== TESTING API ENDPOINTS ===", "INFO")
    
    # Login as admin
    admin_token = login_user("admin", "123456", "admin")
    if not admin_token:
        log_test("API endpoints", "FAIL", "Cannot login as admin")
        return
    
    endpoints = [
        ("Classes", f"{BASE_URL}/classes/list_all.php"),
        ("Subjects", f"{BASE_URL}/subjects/list_all.php"),
        ("News", f"{BASE_URL}/news/get_all.php"),
        ("Schedule", f"{BASE_URL}/schedule/get_weekly.php"),
        ("Reports - Statistics", f"{BASE_URL}/reports/get_statistics.php", admin_token),
        ("Reports - Conduct", f"{BASE_URL}/reports/get_conduct.php", admin_token),
        ("Reports - Academic", f"{BASE_URL}/reports/get_academic.php", admin_token)
    ]
    
    for endpoint in endpoints:
        name = endpoint[0]
        url = endpoint[1]
        token = endpoint[2] if len(endpoint) > 2 else None
        
        try:
            headers = {"Authorization": f"Bearer {token}"} if token else {}
            response = requests.get(url, headers=headers)
            
            if response.status_code == 200:
                log_test(f"Endpoint: {name}", "PASS")
            else:
                log_test(f"Endpoint: {name}", "FAIL", f"Status: {response.status_code}")
                
        except Exception as e:
            log_test(f"Endpoint: {name}", "FAIL", str(e))

def test_error_handling():
    """Test error handling and edge cases"""
    log_test("=== TESTING ERROR HANDLING ===", "INFO")
    
    # Test malformed JSON
    response = requests.post(f"{BASE_URL}/login.php", 
                           data="invalid json",
                           headers={"Content-Type": "application/json"})
    
    if response.status_code == 400:
        log_test("Malformed JSON handling", "PASS")
    else:
        log_test("Malformed JSON handling", "FAIL", f"Expected 400, got {response.status_code}")
    
    # Test missing required fields
    response = requests.post(f"{BASE_URL}/login.php", 
                           json={})
    
    if response.status_code == 400:
        log_test("Missing fields handling", "PASS")
    else:
        log_test("Missing fields handling", "FAIL", f"Expected 400, got {response.status_code}")
    
    # Test SQL injection attempt (should be safely handled)
    response = requests.post(f"{BASE_URL}/login.php", 
                           json={"username": "admin' OR '1'='1", "password": "anything"})
    
    if response.status_code == 401:
        log_test("SQL injection protection", "PASS", "Correctly rejected")
    else:
        log_test("SQL injection protection", "FAIL", f"Expected 401, got {response.status_code}")

def print_test_summary():
    """Print test summary"""
    print("\n" + "=" * 60)
    print("🧪 SYSTEM VALIDATION TEST SUMMARY")
    print("=" * 60)
    print(f"Total Tests: {TEST_RESULTS['total']}")
    print(f"✅ Passed: {TEST_RESULTS['passed']}")
    print(f"❌ Failed: {TEST_RESULTS['failed']}")
    
    if TEST_RESULTS['failed'] == 0:
        print("\n🎉 ALL TESTS PASSED! System is working correctly.")
    else:
        print(f"\n⚠️  {TEST_RESULTS['failed']} tests failed. Please review the logs above.")
    
    print("=" * 60)

def main():
    """Main test execution"""
    print("🚀 SYSTEM VALIDATION TEST SUITE")
    print("=" * 60)
    print("Testing Nenep Management System with new database data...")
    print("=" * 60)
    
    # Run all test suites
    test_login_functionality()
    test_database_integrity()
    test_role_based_access()
    test_violation_workflow()
    test_attendance_workflow()
    test_notification_system()
    test_red_committee_functionality()
    test_banner_system()
    test_api_endpoints()
    test_error_handling()
    
    # Print summary
    print_test_summary()

if __name__ == "__main__":
    main()