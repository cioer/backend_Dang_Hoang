# -*- coding: utf-8 -*-
from typing import List, Dict, Any
from common import post, get, auth_header, TestResult
import json

def test_violation_stats_week() -> TestResult:
    steps = [
        "Gọi API admin/stats/violations.php với type=week",
        "Mong đợi danh sách label dạng YYYY/WW và count"
    ]
    r = get("admin/stats/violations.php", headers=auth_header(), params={"type": "week"})
    ok = r.status_code == 200
    try:
        arr = r.json() if ok else []
        passed = ok and isinstance(arr, list) and all(("label" in x and "count" in x) for x in arr)
        err = None if passed else f"HTTP {r.status_code} body={r.text}"
    except Exception as e:
        passed = False
        err = str(e)
    return {
        "name": "Thống kê vi phạm theo tuần",
        "description": "Kiểm thử API thống kê vi phạm theo tuần (admin)",
        "passed": passed,
        "error": err,
        "steps": steps,
        "input": {"type": "week"},
        "expected": {"list": "array[label,count]"}
    }

def test_violation_details_valid() -> TestResult:
    steps = [
        "Gọi API admin/stats/violations.php để lấy label đầu tiên",
        "Gọi API admin/stats/violation_details.php với {type,label}",
        "Mong đợi JSON array chi tiết vi phạm"
    ]
    s = get("admin/stats/violations.php", headers=auth_header(), params={"type": "week"})
    if s.status_code != 200:
        return {
            "name": "Chi tiết vi phạm (valid)",
            "description": "Không lấy được thống kê tuần để kiểm thử chi tiết",
            "passed": False,
            "error": f"HTTP {s.status_code} body={s.text}",
            "steps": steps,
            "input": {"type": "week"},
            "expected": {"list": "array"}
        }
    stats = s.json()
    label = stats[0]["label"] if stats else ""
    body = {"type": "week", "label": label}
    r = post("admin/stats/violation_details.php", headers=auth_header(), body=body)
    ok = r.status_code == 200
    try:
        arr = r.json() if ok else []
        passed = ok and isinstance(arr, list)
        err = None if passed else f"HTTP {r.status_code} body={r.text}"
    except Exception as e:
        passed = False
        err = str(e)
    return {
        "name": "Chi tiết vi phạm (valid)",
        "description": "Kiểm thử lấy chi tiết vi phạm với nhãn tuần hợp lệ",
        "passed": passed,
        "error": err,
        "steps": steps,
        "input": body,
        "expected": {"list": "array"}
    }

def test_violation_details_invalid_type() -> TestResult:
    steps = [
        "Gọi API admin/stats/violation_details.php với type không hợp lệ",
        "Mong đợi HTTP 400 và message lỗi"
    ]
    body = {"type": "year", "label": "2026"}
    r = post("admin/stats/violation_details.php", headers=auth_header(), body=body)
    passed = r.status_code == 400
    err = None if passed else f"HTTP {r.status_code} body={r.text}"
    return {
        "name": "Chi tiết vi phạm (type invalid)",
        "description": "Kiểm thử trả về 400 khi type không hợp lệ",
        "passed": passed,
        "error": err,
        "steps": steps,
        "input": body,
        "expected": {"status": 400}
    }

def test_violation_details_missing_auth() -> TestResult:
    steps = [
        "Gọi API admin/stats/violation_details.php thiếu Bearer",
        "Mong đợi HTTP 401"
    ]
    body = {"type": "day", "label": "2026-01-01"}
    r = post("admin/stats/violation_details.php", headers={}, body=body)
    passed = r.status_code == 401
    err = None if passed else f"HTTP {r.status_code} body={r.text}"
    return {
        "name": "Chi tiết vi phạm (thiếu auth)",
        "description": "Kiểm thử trả về 401 khi thiếu Bearer token",
        "passed": passed,
        "error": err,
        "steps": steps,
        "input": body,
        "expected": {"status": 401}
    }
