# -*- coding: utf-8 -*-
# Common helpers and configuration for discipline management API tests
import os
import json
import time
import typing as t
import requests

# Base URL of backend APIs
BASE_URL = os.environ.get("SM_BASE_URL", "http://localhost/Backend/api")
# Bearer token for authorized endpoints; set via environment or hardcode for local
BEARER = os.environ.get("SM_BEARER", None)

# Simple result structure
class TestResult(t.TypedDict):
    name: str
    description: str
    passed: bool
    error: t.Optional[str]
    steps: t.List[str]
    input: t.Dict[str, t.Any]
    expected: t.Dict[str, t.Any]
    notes: t.Optional[str]

def auth_header() -> dict:
    if not BEARER:
        return {}
    return {"Authorization": f"Bearer {BEARER}"}

def get(path: str, headers: dict = None, params: dict = None) -> requests.Response:
    url = f"{BASE_URL}/{path}"
    h = headers.copy() if headers else {}
    return requests.get(url, headers=h, params=params, timeout=10)

def post(path: str, headers: dict = None, body: t.Union[dict, list] = None) -> requests.Response:
    url = f"{BASE_URL}/{path}"
    h = headers.copy() if headers else {}
    h.setdefault("Content-Type", "application/json")
    data = json.dumps(body or {})
    return requests.post(url, headers=h, data=data, timeout=10)

def report_line(res: TestResult) -> str:
    status = "PASS" if res["passed"] else "FAIL"
    return f"- [{status}] {res['name']}: {res['description']}"

def write_markdown_report(filename: str, results: t.List[TestResult]) -> None:
    lines = ["# Báo cáo kiểm thử quản lý nề nếp", "", "## Kết quả tổng hợp"]
    for r in results:
        lines.append(report_line(r))
    lines.append("")
    lines.append("## Chi tiết")
    for r in results:
        lines.append(f"### {r['name']}")
        lines.append(f"- Mô tả: {r['description']}")
        lines.append(f"- Bước thực hiện: {', '.join(r['steps'])}")
        lines.append(f"- Dữ liệu đầu vào: `{json.dumps(r['input'], ensure_ascii=False)}`")
        lines.append(f"- Kết quả mong đợi: `{json.dumps(r['expected'], ensure_ascii=False)}`")
        lines.append(f"- Kết luận: {'PASS' if r['passed'] else 'FAIL'}")
        if r.get("error"):
            lines.append(f"- Lỗi: {r['error']}")
        if r.get("notes"):
            lines.append(f"- Ghi chú: {r['notes']}")
        lines.append("")
    with open(filename, "w", encoding="utf-8") as f:
        f.write("\n".join(lines))
