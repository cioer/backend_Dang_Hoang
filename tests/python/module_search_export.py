# -*- coding: utf-8 -*-
# Module: Search/filter and export coverage placeholders
from common import TestResult

def test_search_unavailable() -> TestResult:
    steps = [
        "Kiểm tra API tìm kiếm nề nếp toàn hệ thống",
        "Chưa thấy endpoint search toàn cục"
    ]
    return {
        "name": "Tìm kiếm dữ liệu",
        "description": "Không kiểm thử được do thiếu endpoint search",
        "passed": False,
        "error": "Endpoint search chưa có",
        "steps": steps,
        "input": {},
        "expected": {"endpoint": "search.php"},
        "notes": "Đề xuất thêm API search với tham số từ khóa/lớp/thời gian"
    }
