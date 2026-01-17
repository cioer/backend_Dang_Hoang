<?php
/**
 * Simple PHP Test Script for Graduation Thesis
 * Tests: Login Logic & Red Star Account Creation
 */

require_once '../config/database.php';
// Mocking JWT and headers for testing would be complex without a framework,
// so we will test the Database Logic and Core Functions directly.

class AutomatedTest {
    private $db;
    private $passed = 0;
    private $failed = 0;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        echo "=== STARTING AUTOMATED TESTING SUITE ===\n";
    }

    public function run() {
        $this->testDatabaseConnection();
        $this->testUserTableStructure();
        $this->testRedStarRoleExists();
        $this->testCreateMockUser();
        $this->printSummary();
    }

    private function assert($condition, $message) {
        if ($condition) {
            echo "[PASS] $message\n";
            $this->passed++;
        } else {
            echo "[FAIL] $message\n";
            $this->failed++;
        }
    }

    private function testDatabaseConnection() {
        $this->assert($this->db != null, "Database connection established");
    }

    private function testUserTableStructure() {
        $stmt = $this->db->query("DESCRIBE users");
        $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        $this->assert(in_array('username', $columns), "Table 'users' has 'username' column");
        $this->assert(in_array('role', $columns), "Table 'users' has 'role' column");
        $this->assert(in_array('is_red_star', $columns), "Table 'users' has 'is_red_star' column");
        $this->assert(in_array('class_id', $columns), "Table 'users' has 'class_id' column");
    }

    private function testRedStarRoleExists() {
        $stmt = $this->db->query("SHOW COLUMNS FROM users LIKE 'role'");
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $this->assert(strpos($row['Type'], "'red_star'") !== false, "Enum 'role' contains 'red_star'");
    }

    private function testCreateMockUser() {
        $testUser = 'test_auto_user_' . time();
        $pass = password_hash('123456', PASSWORD_BCRYPT);
        
        // Cleanup first
        $this->db->exec("DELETE FROM users WHERE username LIKE 'test_auto_user_%'");

        $stmt = $this->db->prepare("INSERT INTO users (username, password, full_name, role, is_red_star) VALUES (?, ?, ?, 'red_star', 1)");
        $result = $stmt->execute([$testUser, $pass, 'Test Auto Red Star']);
        
        $this->assert($result, "Create mock Red Star user successfully");

        // Verify insertion
        $check = $this->db->prepare("SELECT * FROM users WHERE username = ?");
        $check->execute([$testUser]);
        $user = $check->fetch(PDO::FETCH_ASSOC);

        $this->assert($user['username'] === $testUser, "Retrieved user matches created username");
        $this->assert($user['role'] === 'red_star', "Retrieved user has 'red_star' role");
        $this->assert($user['is_red_star'] == 1, "Retrieved user has is_red_star = 1");

        // Cleanup
        $this->db->exec("DELETE FROM users WHERE username = '$testUser'");
    }

    private function printSummary() {
        echo "\n=== TEST SUMMARY ===\n";
        echo "Total Tests: " . ($this->passed + $this->failed) . "\n";
        echo "Passed: " . $this->passed . "\n";
        echo "Failed: " . $this->failed . "\n";
        if ($this->failed == 0) {
            echo "RESULT: SUCCESS\n";
        } else {
            echo "RESULT: FAILURE\n";
        }
    }
}

$test = new AutomatedTest();
$test->run();
?>