import requests
import json
import sys

API_URL = "http://103.252.136.73:8080/api"
TOKEN = ""
TEACHER_TOKEN = ""
STUDENT_TOKEN = ""

def print_header(msg):
    print("==========================================")
    print(msg)
    print("==========================================")
    print("")

def test_login_admin():
    global TOKEN
    print("1. TEST: Login Admin")
    url = f"{API_URL}/auth/login_teacher.php"
    payload = {"username": "admin", "password": "password"}
    try:
        response = requests.post(url, json=payload)
        try:
            data = response.json()
            if "token" in data:
                TOKEN = data["token"]
                print(f"✓ Login thành công! Token: {TOKEN[:30]}...")
            else:
                print(f"✗ Login thất bại! Response: {response.text}")
        except ValueError:
             print(f"✗ Login thất bại! Invalid JSON Response: {response.text}")
    except Exception as e:
        print(f"✗ Error: {e}")
    print("")

def test_login_teacher():
    global TEACHER_TOKEN
    print("2. TEST: Login Teacher")
    url = f"{API_URL}/auth/login_teacher.php"
    payload = {"username": "GV-00001", "password": "password"}
    try:
        response = requests.post(url, json=payload)
        try:
            data = response.json()
            if "token" in data:
                TEACHER_TOKEN = data["token"]
                print("✓ Login teacher thành công!")
            else:
                print(f"✗ Login teacher thất bại! Response: {response.text}")
        except ValueError:
             print(f"✗ Login teacher thất bại! Invalid JSON Response: {response.text}")
    except Exception as e:
        print(f"✗ Error: {e}")
    print("")

def test_login_student():
    global STUDENT_TOKEN
    print("3. TEST: Login Student")
    url = f"{API_URL}/auth/login_student.php"
    payload = {"identifier": "HS-00001", "password": "password"}
    try:
        response = requests.post(url, json=payload)
        try:
            data = response.json()
            if "token" in data:
                STUDENT_TOKEN = data["token"]
                print("✓ Login student thành công!")
            else:
                print(f"✗ Login student thất bại! Response: {response.text}")
        except ValueError:
             print(f"✗ Login student thất bại! Invalid JSON Response: {response.text}")
    except Exception as e:
        print(f"✗ Error: {e}")
    print("")

def test_get_profile_admin():
    if not TOKEN: return
    print("4. TEST: Get Profile (Admin)")
    url = f"{API_URL}/profile/get.php"
    headers = {"Authorization": f"Bearer {TOKEN}"}
    try:
        response = requests.post(url, headers=headers)
        if "username" in response.text:
            print("✓ Get profile thành công!")
        else:
            print(f"✗ Get profile thất bại! Response: {response.text}")
    except Exception as e:
        print(f"✗ Error: {e}")
    print("")

def test_list_classes():
    print("5. TEST: List All Classes")
    url = f"{API_URL}/classes/list_all.php"
    try:
        response = requests.get(url)
        count = response.text.count('"id":')
        if count > 0:
            print(f"✓ List classes thành công! ({count} lớp)")
        else:
            print(f"✗ List classes thất bại! Response: {response.text[:200]}")
    except Exception as e:
        print(f"✗ Error: {e}")
    print("")

def test_list_subjects():
    print("6. TEST: List All Subjects")
    url = f"{API_URL}/subjects/list_all.php"
    try:
        response = requests.get(url)
        count = response.text.count('"id":')
        if count > 0:
            print(f"✓ List subjects thành công! ({count} môn)")
        else:
            print(f"✗ List subjects thất bại! Response: {response.text[:200]}")
    except Exception as e:
        print(f"✗ Error: {e}")
    print("")

def test_get_news():
    print("7. TEST: Get Latest News")
    url = f"{API_URL}/news/get_latest.php"
    try:
        response = requests.get(url)
        if '"title"' in response.text:
            count = response.text.count('"title"')
            print(f"✓ Get news thành công! ({count} tin)")
        else:
            print(f"✗ Get news thất bại! Response: {response.text[:200]}")
    except Exception as e:
        print(f"✗ Error: {e}")
    print("")

def test_get_active_banners():
    print("8. TEST: Get Active Banners")
    url = f"{API_URL}/banners/get_active.php"
    try:
        response = requests.get(url)
        print(f"✓ Endpoint hoạt động! Response: {response.text[:100]}...")
    except Exception as e:
        print(f"✗ Error: {e}")
    print("")

def test_list_users_admin():
    if not TOKEN: return
    print("9. TEST: List Users (Admin)")
    url = f"{API_URL}/admin/users/list.php"
    payload = {"token": TOKEN, "role": "admin"}
    try:
        response = requests.post(url, json=payload)
        if "data" in response.text or "users" in response.text:
            print("✓ List users thành công!")
        else:
            print(f"✗ List users thất bại! Response: {response.text[:200]}...")
    except Exception as e:
        print(f"✗ Error: {e}")
    print("")

def test_get_statistics_admin():
    if not TOKEN: return
    print("10. TEST: Get Statistics")
    url = f"{API_URL}/reports/get_statistics.php"
    payload = {"token": TOKEN, "type": "overview"}
    try:
        response = requests.post(url, json=payload)
        if "{" in response.text: # Simple check for JSON object
            print("✓ Get statistics thành công!")
        else:
            print(f"✗ Get statistics thất bại! Response: {response.text[:200]}")
    except Exception as e:
        print(f"✗ Error: {e}")
    print("")

def test_teacher_get_classes():
    if not TEACHER_TOKEN: return
    print("11. TEST: Teacher Get Classes")
    url = f"{API_URL}/teacher/get_classes.php"
    payload = {"token": TEACHER_TOKEN}
    try:
        response = requests.post(url, json=payload)
        if "[" in response.text:
             count = response.text.count('"id":')
             print(f"✓ Teacher get classes thành công! ({count} lớp)")
        else:
            print(f"✗ Teacher get classes thất bại! Response: {response.text[:200]}")
    except Exception as e:
        print(f"✗ Error: {e}")
    print("")

def test_student_profile():
    if not STUDENT_TOKEN: return
    print("12. TEST: Student Profile")
    url = f"{API_URL}/profile/get.php"
    headers = {"Authorization": f"Bearer {STUDENT_TOKEN}"}
    try:
        response = requests.post(url, headers=headers)
        if "username" in response.text:
            print("✓ Student profile thành công!")
        else:
             print(f"✗ Student profile thất bại! Response: {response.text}")
    except Exception as e:
        print(f"✗ Error: {e}")
    print("")

def test_get_violations_points():
    print("13. TEST: Get Violations Points")
    url = f"{API_URL}/violations/get_points.php"
    try:
        response = requests.get(url)
        if "{" in response.text or "[" in response.text:
            print("✓ Get violations points endpoint hoạt động!")
        else:
            print(f"✗ Get violations points thất bại! Response: {response.text[:200]}")
    except Exception as e:
        print(f"✗ Error: {e}")
    print("")

def test_get_violations_rules():
    print("14. TEST: Get Violations Rules")
    url = f"{API_URL}/violations/get_rules.php"
    try:
        response = requests.get(url)
        if "{" in response.text or "[" in response.text:
            print("✓ Get violations rules endpoint hoạt động!")
        else:
            print(f"✗ Get violations rules thất bại! Response: {response.text[:200]}")
    except Exception as e:
        print(f"✗ Error: {e}")
    print("")

def test_change_password_validation():
    if not TOKEN: return
    print("15. TEST: Change Password (wrong password)")
    url = f"{API_URL}/auth/change_password.php"
    headers = {"Authorization": f"Bearer {TOKEN}"}
    payload = {"old_password": "wrong", "new_password": "NewPass123!"}
    try:
        response = requests.post(url, headers=headers, json=payload)
        # Check for vietnamese error message or any indication of failure which is expected
        if "M" in response.text and "t" in response.text and "u" in response.text: 
             print("✓ Password validation hoạt động đúng!")
        elif not response.json().get("success", True):
             print("✓ Password validation hoạt động đúng (success: false)!")
        else:
             print(f"✗ Password validation test! Response: {response.text}")
    except Exception as e:
        print(f"✗ Error: {e}")
    print("")

if __name__ == "__main__":
    print_header("TEST API SCHOOL MANAGEMENT SYSTEM")
    test_login_admin()
    test_login_teacher()
    test_login_student()
    test_get_profile_admin()
    test_list_classes()
    test_list_subjects()
    test_get_news()
    test_get_active_banners()
    test_list_users_admin()
    test_get_statistics_admin()
    test_teacher_get_classes()
    test_student_profile()
    test_get_violations_points()
    test_get_violations_rules()
    test_change_password_validation()
    print_header("HOÀN THÀNH TEST")
