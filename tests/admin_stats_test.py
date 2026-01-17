#!/usr/bin/env python3
# -*- coding: utf-8 -*-
import os
import sys

BASE_DIR = os.path.dirname(__file__)
PY_DIR = os.path.join(BASE_DIR, "python")
sys.path.append(PY_DIR)

from module_admin_stats import (
    test_violation_stats_week,
    test_violation_details_valid,
    test_violation_details_invalid_type,
    test_violation_details_missing_auth,
)
from python.common import write_markdown_report  # type: ignore

def main() -> int:
    results = []
    results.append(test_violation_stats_week())
    results.append(test_violation_details_valid())
    results.append(test_violation_details_invalid_type())
    results.append(test_violation_details_missing_auth())
    write_markdown_report("admin_stats_test_report.md", results)
    failed = [r for r in results if not r["passed"]]
    return 0 if not failed else 1

if __name__ == "__main__":
    raise SystemExit(main())
