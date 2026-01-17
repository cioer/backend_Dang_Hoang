import requests
import json
import time

BASE_URL = "http://localhost/Backend/api"

# Màu sắc cho log
class BColors:
    HEADER = '\033[95m'
    OKBLUE = '\033[94m'
    OKGREEN = '\033[92m'
    WARNING = '\033[93m'
    FAIL = '\033[91m'
    ENDC = '\033[0m'

def log(type, message):
    if type == "INFO":
        print(f"{BColors.OKBLUE}[INFO]{BColors.ENDC} {message}")
    elif type == "SUCCESS":
        print(f"{BColors.OKGREEN}[PASS]{BColors.ENDC} {message}")
    elif type == "ERROR":
        print(f"{BColors.FAIL}[FAIL]{BColors.ENDC} {message}")
    elif type == "WARN":
        print(f"{BColors.WARNING}[WARN]{BColors.ENDC} {message}")

def test_login_flow():
    log("INFO", "--- TESTING LOGIN FLOW ---")
    
    # 1. Login đúng
    resp = requests.post(f"{BASE_URL}/login.php", json={"username": "GV-00001", "password": "password"})
    if resp.status_code == 200 and "token" in resp.json():
        log("SUCCESS", "Login valid credentials")
        return resp.json()["token"]
    else:
        log("ERROR", f"Login valid failed: {resp.text}")
        return None

    # 2. Login sai
    resp = requests.post(f"{BASE_URL}/login.php", json={"username": "GV-00001", "password": "wrongpassword"})
    if resp.status_code == 401:
        log("SUCCESS", "Login invalid credentials rejected (401)")
    else:
        log("ERROR", f"Login invalid should be 401, got {resp.status_code}")

def test_permission_check(student_token):
    log("INFO", "--- TESTING PERMISSION (RBAC) ---")
    
    # Dùng token học sinh gọi API lấy danh sách lớp của giáo viên
    headers = {"Authorization": f"Bearer {student_token}"}
    resp = requests.post(f"{BASE_URL}/teacher/get_classes.php", json={"token": student_token}, headers=headers)
    
    # Mong đợi: 401 hoặc 403, hoặc trả về lỗi message
    if resp.status_code in [401, 403]:
        log("SUCCESS", "Student accessing Teacher API blocked")
    else:
        # Nếu API tự xử lý trả về 200 nhưng nội dung báo lỗi
        data = resp.json()
        if "message" in data and ("từ chối" in data["message"] or "denied" in data["message"]):
             log("SUCCESS", "Student accessing Teacher API blocked (Logic check)")
        else:
             log("ERROR", f"Student accessed Teacher API! Status: {resp.status_code}")

def test_data_validation(teacher_token):
    log("INFO", "--- TESTING DATA VALIDATION ---")
    
    # Thử nhập điểm không hợp lệ (ví dụ: điểm 15)
    # Giả sử có API update_score.php
    payload = {
        "student_id": 3,
        "subject_id": 1,
        "score_15m": 15, # Invalid
        "semester": "HK1-2025"
    }
    headers = {"Authorization": f"Bearer {teacher_token}"}
    # Note: Đây là giả lập, cần thay bằng endpoint thật nếu có
    # resp = requests.post(f"{BASE_URL}/teacher/update_score.php", json=payload, headers=headers)
    # log("WARN", "Skipping score validation test (Endpoint not confirmed)")

def test_performance(teacher_token):
    log("INFO", "--- TESTING PERFORMANCE ---")
    
    # Đo thời gian tải danh sách học sinh của lớp đông (100+ học sinh)
    # Lấy ID lớp 10A1 (giả sử ID=1)
    start_time = time.time()
    payload = {"class_id": 1}
    headers = {"Authorization": f"Bearer {teacher_token}"}
    
    try:
        resp = requests.post(f"{BASE_URL}/violations/list_students_by_class.php", json=payload, headers=headers)
        duration = (time.time() - start_time) * 1000 # ms
        
        if resp.status_code == 200:
            students = resp.json()
            count = len(students)
            log("INFO", f"Loaded {count} students in {duration:.2f}ms")
            
            if duration < 500:
                log("SUCCESS", "Performance is good (<500ms)")
            else:
                log("WARN", "Performance is slow (>500ms)")
        else:
            log("ERROR", f"Failed to load students: {resp.status_code}")
            
    except Exception as e:
        log("ERROR", f"Performance test failed: {e}")

if __name__ == "__main__":
    # 1. Get Teacher Token
    teacher_token = test_login_flow()
    
    # 2. Get Student Token for RBAC test
    resp = requests.post(f"{BASE_URL}/login.php", json={"username": "HS-00001", "password": "password"})
    student_token = resp.json().get("token") if resp.status_code == 200 else None
    
    if teacher_token and student_token:
        test_permission_check(student_token)
        test_performance(teacher_token)
    else:
        log("ERROR", "Cannot proceed tests without tokens")
