# -*- coding: utf-8 -*-
# Module: Reports viewing and export placeholders
from common import post, get, auth_header, TestResult

def test_get_conduct_report() -> TestResult:
    steps = [
        "Gọi API reports/get_conduct.php",
        "Mong đợi JSON danh sách kết quả hạnh kiểm"
    ]
    r = post("reports/get_conduct.php", headers=auth_header(), body={"token": "unused"})
    ok = r.status_code == 200
    try:
        arr = r.json() if ok else []
        passed = ok and isinstance(arr, list)
        err = None if passed else f"HTTP {r.status_code} body={r.text}"
    except Exception as e:
        passed = False
        err = str(e)
    return {
        "name": "Xem báo cáo hạnh kiểm",
        "description": "Kiểm thử API tổng hợp hạnh kiểm",
        "passed": passed,
        "error": err,
        "steps": steps,
        "input": {},
        "expected": {"list": "array"}
    }

def test_get_competition_stats() -> TestResult:
    steps = [
        "Gọi API reports/get_competition_stats.php",
        "Mong đợi JSON thống kê thi đua"
    ]
    r = get("reports/get_competition_stats.php", headers=auth_header())
    ok = r.status_code == 200
    try:
        obj = r.json() if ok else {}
        passed = ok and isinstance(obj, dict)
        err = None if passed else f"HTTP {r.status_code} body={r.text}"
    except Exception as e:
        passed = False
        err = str(e)
    return {
        "name": "Xem báo cáo thi đua",
        "description": "Kiểm thử API thống kê thi đua theo thời gian",
        "passed": passed,
        "error": err,
        "steps": steps,
        "input": {},
        "expected": {"object": "dict"}
    }

def test_export_report_unavailable() -> TestResult:
    steps = [
        "Kiểm tra API export báo cáo (CSV/PDF)",
        "Chưa có endpoint xuất báo cáo"
    ]
    return {
        "name": "Xuất báo cáo",
        "description": "Không kiểm thử được do chưa có endpoint export",
        "passed": False,
        "error": "Endpoint export chưa có",
        "steps": steps,
        "input": {},
        "expected": {"endpoint": "reports/export.php"},
        "notes": "Đề xuất thêm endpoint xuất CSV/PDF kèm tham số thời gian/lớp"
    }
