import requests
import json

API_URL = "http://103.252.136.73:8080/api"

def print_json_structure(name, data):
    print(f"--- {name} ---")
    if isinstance(data, list):
        if len(data) > 0:
            print("List of objects with keys:", data[0].keys())
        else:
            print("Empty list")
    elif isinstance(data, dict):
        print("Object with keys:", data.keys())
    else:
        print("Type:", type(data))
    print("")

def check_models():
    # Login to get token
    login_url = f"{API_URL}/auth/login_teacher.php"
    login_payload = {"username": "admin", "password": "password"}
    try:
        resp = requests.post(login_url, json=login_payload)
        token = resp.json().get("token")
        print(f"Token obtained: {token[:10]}...")
    except Exception as e:
        print(f"Login failed: {e}")
        return

    # Check News
    try:
        resp = requests.get(f"{API_URL}/news/get_latest.php")
        print_json_structure("News", resp.json())
    except Exception as e:
        print(f"News check failed: {e}")

    # Check Banners
    try:
        resp = requests.get(f"{API_URL}/banners/get_active.php")
        print_json_structure("Banners", resp.json())
    except Exception as e:
        print(f"Banners check failed: {e}")

    # Check Profile
    try:
        headers = {"Authorization": f"Bearer {token}"}
        resp = requests.post(f"{API_URL}/profile/get.php", headers=headers)
        print_json_structure("Profile", resp.json())
    except Exception as e:
        print(f"Profile check failed: {e}")

if __name__ == "__main__":
    check_models()
