import requests
import json
import sys

API_URL = "http://103.252.136.73:8080/api"
TOKEN = ""

def print_step(msg):
    print(f"\n[STEP] {msg}")

def login_admin():
    global TOKEN
    print_step("Login Admin")
    url = f"{API_URL}/auth/login_teacher.php"
    payload = {"username": "admin", "password": "password"}
    try:
        resp = requests.post(url, json=payload)
        data = resp.json()
        if "token" in data:
            TOKEN = data["token"]
            print("✓ Login successful")
            return True
        else:
            print(f"✗ Login failed: {resp.text}")
            return False
    except Exception as e:
        print(f"✗ Error: {e}")
        return False

def get_classes():
    print_step("Get Classes")
    url = f"{API_URL}/teacher/get_classes.php"
    payload = {"token": TOKEN}
    try:
        resp = requests.post(url, json=payload)
        classes = resp.json()
        if isinstance(classes, list) and len(classes) > 0:
            print(f"✓ Found {len(classes)} classes")
            return classes
        else:
            print(f"✗ No classes found: {resp.text}")
            return []
    except Exception as e:
        print(f"✗ Error: {e}")
        return []

def get_red_committee(class_id):
    print_step(f"Get Red Committee for Class {class_id}")
    url = f"{API_URL}/red_committee/list.php"
    headers = {"Authorization": f"Bearer {TOKEN}"}
    params = {"class_id": class_id}
    try:
        resp = requests.get(url, headers=headers, params=params)
        members = resp.json()
        print(f"✓ Found {len(members)} members")
        return members
    except Exception as e:
        print(f"✗ Error: {e}")
        return []

def get_students(class_id):
    print_step(f"Get Students for Class {class_id}")
    url = f"{API_URL}/teacher/get_students.php"
    payload = {"token": TOKEN, "class_id": class_id}
    try:
        resp = requests.post(url, json=payload)
        students = resp.json()
        if isinstance(students, list):
            print(f"✓ Found {len(students)} students")
            return students
        else:
            print(f"✗ Failed to get students: {resp.text}")
            return []
    except Exception as e:
        print(f"✗ Error: {e}")
        return []

def get_challenge():
    print_step("Get Action Challenge")
    url = f"{API_URL}/security/challenge.php"
    headers = {"Authorization": f"Bearer {TOKEN}"}
    try:
        resp = requests.get(url, headers=headers)
        data = resp.json()
        if "code" in data and "ticket" in data:
            print(f"✓ Challenge received: {data['code']}")
            return data["code"], data["ticket"]
        else:
            print(f"✗ Failed to get challenge: {resp.text}")
            return None, None
    except Exception as e:
        print(f"✗ Error: {e}")
        return None, None

def add_red_star(class_id, user_id):
    print_step(f"Add Red Star (User {user_id} to Class {class_id})")
    code, ticket = get_challenge()
    if not code: return False

    url = f"{API_URL}/red_committee/add.php"
    headers = {
        "Authorization": f"Bearer {TOKEN}",
        "X-Action-Code": str(code),
        "X-Action-Ticket": ticket
    }
    payload = {"class_id": class_id, "user_id": user_id}
    
    try:
        resp = requests.post(url, headers=headers, json=payload)
        if resp.status_code == 200:
            print("✓ Add successful")
            return True
        elif resp.status_code == 409:
            print("! Class already has Red Star (Conflict)")
            # Try replace
            print("  -> Trying to replace...")
            payload["replace"] = True
            resp2 = requests.post(url, headers=headers, json=payload)
            if resp2.status_code == 200:
                print("✓ Replace successful")
                return True
            else:
                print(f"✗ Replace failed: {resp2.text}")
                return False
        else:
            print(f"✗ Add failed: {resp.status_code} - {resp.text}")
            return False
    except Exception as e:
        print(f"✗ Error: {e}")
        return False

def remove_red_star(class_id, user_id):
    print_step(f"Remove Red Star (User {user_id} from Class {class_id})")
    code, ticket = get_challenge()
    if not code: return False

    url = f"{API_URL}/red_committee/remove.php"
    headers = {
        "Authorization": f"Bearer {TOKEN}",
        "X-Action-Code": str(code),
        "X-Action-Ticket": ticket
    }
    payload = {"class_id": class_id, "user_id": user_id}
    
    try:
        resp = requests.post(url, headers=headers, json=payload)
        if resp.status_code == 200:
            print("✓ Remove successful")
            return True
        else:
            print(f"✗ Remove failed: {resp.status_code} - {resp.text}")
            return False
    except Exception as e:
        print(f"✗ Error: {e}")
        return False

def run_test():
    if not login_admin(): return

    classes = get_classes()
    if not classes: return

    # Pick first class
    target_class = classes[0]
    class_id = target_class["id"]
    print(f"Target Class: {target_class['name']} (ID: {class_id})")

    # Get students
    students = get_students(class_id)
    if not students: return
    
    # Pick a student
    target_student = students[0]
    user_id = target_student["id"]
    print(f"Target Student: {target_student['full_name']} (ID: {user_id})")

    # 1. Add
    if add_red_star(class_id, user_id):
        # 2. Verify
        members = get_red_committee(class_id)
        found = False
        for m in members:
            if m["user_id"] == user_id:
                found = True
                break
        
        if found:
            print("✓ Verification: Student IS in Red Committee list")
            
            # 3. Remove
            if remove_red_star(class_id, user_id):
                # 4. Verify removal
                members = get_red_committee(class_id)
                found_after = False
                for m in members:
                    if m["user_id"] == user_id:
                        found_after = True
                        break
                if not found_after:
                     print("✓ Verification: Student IS REMOVED from Red Committee list")
                else:
                     print("✗ Verification Failed: Student still in list")
        else:
            print("✗ Verification Failed: Student NOT found in list after add")

if __name__ == "__main__":
    run_test()
