<?php
/**
 * Check actual users table schema on database
 */

require_once __DIR__ . '/bootstrap.php';

use App\Core\Bootstrap;

$db = Bootstrap::db();

echo "====================================\n";
echo "USERS TABLE SCHEMA CHECK\n";
echo "====================================\n\n";

try {
    $stmt = $db->query("DESC users");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo "Current columns in 'users' table:\n\n";
    echo str_pad("Field", 25) . str_pad("Type", 20) . str_pad("Null", 10) . "Key\n";
    echo str_repeat("-", 70) . "\n";

    $hasClassId = false;
    $hasIsRedStar = false;
    $roleEnum = null;

    foreach ($columns as $col) {
        echo str_pad($col['Field'], 25)
           . str_pad($col['Type'], 20)
           . str_pad($col['Null'], 10)
           . $col['Key'] . "\n";

        if ($col['Field'] === 'class_id') {
            $hasClassId = true;
        }
        if ($col['Field'] === 'is_red_star') {
            $hasIsRedStar = true;
        }
        if ($col['Field'] === 'role') {
            $roleEnum = $col['Type'];
        }
    }

    echo "\n====================================\n";
    echo "VALIDATION:\n";
    echo "====================================\n\n";

    echo ($hasClassId ? "✅" : "❌") . " class_id column " . ($hasClassId ? "EXISTS" : "MISSING") . "\n";
    echo ($hasIsRedStar ? "✅" : "❌") . " is_red_star column " . ($hasIsRedStar ? "EXISTS" : "MISSING") . "\n";

    echo "\nRole enum: $roleEnum\n";
    $hasRedStarRole = (strpos($roleEnum, 'red_star') !== false);
    echo ($hasRedStarRole ? "✅" : "❌") . " 'red_star' role " . ($hasRedStarRole ? "EXISTS" : "MISSING") . " in enum\n";

    if (!$hasClassId || !$hasIsRedStar || !$hasRedStarRole) {
        echo "\n⚠️  SCHEMA NEEDS UPDATE!\n\n";
        echo "Run the following SQL commands to fix:\n\n";

        if (!$hasClassId) {
            echo "ALTER TABLE users ADD COLUMN class_id INT(11) DEFAULT NULL AFTER role;\n";
        }
        if (!$hasIsRedStar) {
            echo "ALTER TABLE users ADD COLUMN is_red_star TINYINT(1) DEFAULT 0 AFTER role;\n";
        }
        if (!$hasRedStarRole) {
            echo "ALTER TABLE users MODIFY COLUMN role ENUM('admin','teacher','student','parent','red_star') NOT NULL DEFAULT 'student';\n";
        }
    } else {
        echo "\n✅ Schema is up to date!\n";
    }

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

echo "\n====================================\n";
