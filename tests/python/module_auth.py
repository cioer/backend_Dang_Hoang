import os
import requests

BASE = os.environ.get("BASE_URL", "http://localhost")

def url(path: str) -> str:
    return BASE.rstrip("/") + "/" + path.lstrip("/")

def login_teacher(username: str, password: str):
    r = requests.post(url("Backend/api/auth/login_teacher.php"),
                      json={"username": username, "password": password}, timeout=10)
    return r

def login_student(identifier: str, password: str):
    r = requests.post(url("Backend/api/auth/login_student.php"),
                      json={"identifier": identifier, "password": password}, timeout=10)
    return r

def login_parent(student_code: str, password: str):
    r = requests.post(url("Backend/api/auth/login_parent_password.php"),
                      json={"student_code": student_code, "password": password}, timeout=10)
    return r

def change_password(token: str, old_password: str, new_password: str):
    headers = {"Authorization": f"Bearer {token}"}
    r = requests.post(url("Backend/api/auth/change_password.php"),
                      headers=headers,
                      json={"old_password": old_password, "new_password": new_password},
                      timeout=10)
    return r
