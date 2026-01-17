#!/usr/bin/env python3
"""
Comprehensive Test Runner for Nenep Management System
Chạy toàn bộ test suite cho hệ thống quản lý nề nếp
"""

import subprocess
import sys
import os
import json
from datetime import datetime

def print_header(title):
    """Print formatted header"""
    print("\n" + "=" * 80)
    print(f"🧪 {title}")
    print("=" * 80)

def print_section(title):
    """Print formatted section"""
    print(f"\n📋 {title}")
    print("-" * 60)

def run_python_test(test_file, description):
    """Run a Python test file"""
    print(f"\n🔍 Running: {description}")
    print(f"📁 File: {test_file}")
    
    try:
        result = subprocess.run([sys.executable, test_file], 
                              capture_output=True, 
                              text=True, 
                              cwd=os.path.dirname(test_file))
        
        print("📊 Output:")
        if result.stdout:
            print(result.stdout)
        
        if result.stderr:
            print("⚠️  Errors:")
            print(result.stderr)
        
        if result.returncode == 0:
            print("✅ Test completed successfully")
            return True
        else:
            print("❌ Test failed")
            return False
            
    except Exception as e:
        print(f"❌ Error running test: {e}")
        return False

def run_php_test(test_file, description):
    """Run a PHP test file"""
    print(f"\n🔍 Running: {description}")
    print(f"📁 File: {test_file}")
    
    try:
        result = subprocess.run(["php", test_file], 
                              capture_output=True, 
                              text=True, 
                              cwd=os.path.dirname(test_file))
        
        print("📊 Output:")
        if result.stdout:
            print(result.stdout)
        
        if result.stderr:
            print("⚠️  Errors:")
            print(result.stderr)
        
        if result.returncode == 0:
            print("✅ Test completed successfully")
            return True
        else:
            print("❌ Test failed")
            return False
            
    except Exception as e:
        print(f"❌ Error running test: {e}")
        return False

def check_server_availability():
    """Check if the server is running"""
    print("🔌 Checking server availability...")
    
    try:
        import requests
        response = requests.get("http://localhost/Backend/api/login.php", timeout=5)
        if response.status_code in [200, 400, 401]:  # Any valid response
            print("✅ Server is accessible")
            return True
        else:
            print(f"⚠️  Server returned status: {response.status_code}")
            return False
    except Exception as e:
        print(f"❌ Server not accessible: {e}")
        print("💡 Please ensure your web server (Apache/Nginx) is running with PHP support")
        return False

def check_database_connection():
    """Check database connection"""
    print("🗄️  Checking database connection...")
    
    try:
        import mysql.connector
        conn = mysql.connector.connect(
            host='localhost',
            user='root',
            password='',
            database='nenep_management'
        )
        conn.close()
        print("✅ Database connection successful")
        return True
    except Exception as e:
        print(f"❌ Database connection failed: {e}")
        print("💡 Please ensure MySQL is running and database exists")
        return False

def generate_test_report(results):
    """Generate comprehensive test report"""
    print_header("TEST REPORT")
    
    report = {
        "timestamp": datetime.now().isoformat(),
        "summary": {
            "total_tests": len(results),
            "passed": sum(1 for r in results.values() if r),
            "failed": sum(1 for r in results.values() if not r)
        },
        "details": results
    }
    
    # Save report to file
    report_file = f"test_report_{datetime.now().strftime('%Y%m%d_%H%M%S')}.json"
    with open(report_file, 'w', encoding='utf-8') as f:
        json.dump(report, f, indent=2, ensure_ascii=False)
    
    print(f"📊 Test Summary:")
    print(f"   Total Tests: {report['summary']['total_tests']}")
    print(f"   ✅ Passed: {report['summary']['passed']}")
    print(f"   ❌ Failed: {report['summary']['failed']}")
    print(f"📁 Report saved to: {report_file}")
    
    return report

def main():
    """Main test runner"""
    print_header("NENEP MANAGEMENT SYSTEM - COMPREHENSIVE TEST SUITE")
    
    # Check prerequisites
    print_section("PREREQUISITE CHECKS")
    
    server_ok = check_server_availability()
    db_ok = check_database_connection()
    
    if not server_ok or not db_ok:
        print("\n❌ Prerequisites not met. Please fix the issues above before running tests.")
        return 1
    
    # Define test suites
    test_suites = [
        {
            "name": "VIOLATIONS COMPREHENSIVE TESTS",
            "tests": [
                ("test_violations_comprehensive.py", "Comprehensive violations management testing")
            ]
        },
        {
            "name": "ADMIN STATS TESTS", 
            "tests": [
                ("admin_stats_test.py", "Admin stats and violation details endpoints")
            ]
        },
        {
            "name": "SYSTEM VALIDATION TESTS", 
            "tests": [
                ("system_validation_test.py", "Complete system validation with new data")
            ]
        },
        {
            "name": "PHP BACKEND TESTS",
            "tests": [
                ("banners_get_active_test.php", "Banner system functionality"),
                ("stream_test.php", "SSE stream functionality")
            ]
        }
    ]
    
    # Run tests
    all_results = {}
    
    for suite in test_suites:
        print_header(suite["name"])
        
        for test_file, description in suite["tests"]:
            test_path = os.path.join(os.path.dirname(__file__), test_file)
            
            if os.path.exists(test_path):
                if test_file.endswith('.py'):
                    success = run_python_test(test_path, description)
                elif test_file.endswith('.php'):
                    success = run_php_test(test_path, description)
                else:
                    print(f"⚠️  Unknown test type: {test_file}")
                    success = False
                
                all_results[test_file] = success
            else:
                print(f"⚠️  Test file not found: {test_path}")
                all_results[test_file] = False
    
    # Generate final report
    print_header("FINAL RESULTS")
    report = generate_test_report(all_results)
    
    # Return appropriate exit code
    if report['summary']['failed'] > 0:
        print("\n❌ Some tests failed. Please review the results above.")
        return 1
    else:
        print("\n🎉 All tests passed! System is working correctly.")
        return 0

if __name__ == "__main__":
    try:
        exit_code = main()
        sys.exit(exit_code)
    except KeyboardInterrupt:
        print("\n\n⚠️  Test suite interrupted by user.")
        sys.exit(1)
    except Exception as e:
        print(f"\n\n❌ Unexpected error: {e}")
        sys.exit(1)
