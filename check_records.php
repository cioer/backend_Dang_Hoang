<?php
try {
    $pdo = new PDO('mysql:host=localhost;dbname=nenep_management', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $tables = ['users','classes','violations','violation_types','discipline_records','student_violations','audit_logs','otp_codes','login_attempts'];
    
    echo "=== DATABASE RECORDS COUNT ===\n";
    foreach($tables as $table) {
        $stmt = $pdo->query("SELECT COUNT(*) as cnt FROM $table");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        echo str_pad($table, 25) . ": " . $result['cnt'] . " records\n";
    }
    
    echo "\n=== SAMPLE DATA ===\n";
    echo "Users (first 3):\n";
    $stmt = $pdo->query("SELECT username, full_name, role FROM users LIMIT 3");
    while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "  - " . $row['username'] . " (" . $row['full_name'] . ") - " . $row['role'] . "\n";
    }
    
    echo "\nClasses (first 3):\n";
    $stmt = $pdo->query("SELECT class_name, academic_year FROM classes LIMIT 3");
    while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "  - " . $row['class_name'] . " (" . $row['academic_year'] . ")\n";
    }
    
} catch(PDOException $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}