# -*- coding: utf-8 -*-
# Module: Violation CRUD, search/filter, and points checks
from typing import List, Dict, Any
from common import post, get, auth_header, TestResult

def test_add_violation(student_id: int, rule_id: int, reporter_id: int) -> TestResult:
    steps = [
        "Gọi API violations/submit.php với bearer hợp lệ",
        "Truyền student_id, rule_id, note",
        "Mong đợi 200 OK và cập nhật điểm nề nếp"
    ]
    body = {"student_id": student_id, "rule_id": rule_id, "note": "TEST ADD"}
    r = post("violations/submit.php", headers=auth_header(), body=body)
    passed = r.status_code == 200
    err = None if passed else f"HTTP {r.status_code} body={r.text}"
    return {
        "name": "Thêm vi phạm",
        "description": "Kiểm thử thêm bản ghi vi phạm mới",
        "passed": passed,
        "error": err,
        "steps": steps,
        "input": body,
        "expected": {"status": 200}
    }

def test_get_points(student_id: int) -> TestResult:
    steps = [
        "Gọi API violations/get_points.php",
        "Truyền student_id để lấy điểm nề nếp",
        "Mong đợi JSON có trường points"
    ]
    body = {"student_id": student_id}
    r = post("violations/get_points.php", headers=auth_header(), body=body)
    ok = r.status_code == 200
    try:
        js = r.json() if ok else {}
        passed = ok and isinstance(js.get("points", None), int)
        err = None if passed else f"HTTP {r.status_code} body={r.text}"
    except Exception as e:
        passed = False
        err = str(e)
    return {
        "name": "Xem điểm nề nếp",
        "description": "Kiểm thử lấy điểm nề nếp hiện tại của học sinh",
        "passed": passed,
        "error": err,
        "steps": steps,
        "input": body,
        "expected": {"points": "int"}
    }

def test_list_students_by_class(class_id: int) -> TestResult:
    steps = [
        "Gọi API violations/list_students_by_class.php",
        "Truyền class_id để lọc theo lớp",
        "Mong đợi danh sách học sinh (JSON array)"
    ]
    body = {"class_id": class_id}
    r = post("violations/list_students_by_class.php", headers=auth_header(), body=body)
    ok = r.status_code == 200
    try:
        arr = r.json() if ok else []
        passed = ok and isinstance(arr, list)
        err = None if passed else f"HTTP {r.status_code} body={r.text}"
    except Exception as e:
        passed = False
        err = str(e)
    return {
        "name": "Tìm kiếm/lọc theo lớp",
        "description": "Kiểm thử lọc danh sách học sinh theo lớp",
        "passed": passed,
        "error": err,
        "steps": steps,
        "input": body,
        "expected": {"list": "array"}
    }

def test_edit_violation_unavailable() -> TestResult:
    steps = [
        "Kiểm tra API update violation",
        "Không thấy endpoint chỉnh sửa vi phạm"
    ]
    return {
        "name": "Sửa vi phạm",
        "description": "Không kiểm thử được do chưa có endpoint update",
        "passed": False,
        "error": "Endpoint update chưa có",
        "steps": steps,
        "input": {},
        "expected": {"endpoint": "violations/update.php"},
        "notes": "Đề xuất bổ sung API update để sửa ghi chú/ảnh hoặc rule_id"
    }

def test_delete_violation_unavailable() -> TestResult:
    steps = [
        "Kiểm tra API delete violation",
        "Không thấy endpoint xóa vi phạm"
    ]
    return {
        "name": "Xóa vi phạm",
        "description": "Không kiểm thử được do chưa có endpoint delete",
        "passed": False,
        "error": "Endpoint delete chưa có",
        "steps": steps,
        "input": {},
        "expected": {"endpoint": "violations/delete.php"},
        "notes": "Đề xuất bổ sung API delete để xóa bản ghi sai"
    }
