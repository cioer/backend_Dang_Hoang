<?php
require_once __DIR__ . '/../bootstrap.php';

use App\Core\{Middleware, Response, Request, Bootstrap};

Middleware::cors('POST');
// Note: Auth is commented out in original for testing - keeping that behavior
// Middleware::requireAdmin();

$db = Bootstrap::db();
$data = Request::all();

$tableName = $data['table_name'] ?? null;
$rows = $data['data'] ?? [];
$overwrite = $data['overwrite'] ?? false; // True: Delete old and insert new

if (!$tableName || empty($rows)) {
    Response::error("Missing table_name or data", 400);
}

// Allowed tables whitelist
$allowedTables = [
    'users', 'classes', 'subjects', 'students', 'student_profiles',
    'student_details', 'parent_student_links', 'schedule', 'attendance',
    'notifications', 'messages', 'news', 'behavior_reports',
    'discipline_points', 'conduct_rules', 'violations', 'scores',
    'conduct', 'teacher_subjects', 'class_registrations',
    'conduct_results', 'teacher_class_requests', 'class_teacher_assignments',
    'banners', 'banner_logs'
];

if (!in_array($tableName, $allowedTables)) {
    Response::error("Invalid table name: $tableName", 400);
}

try {
    // Get table schema for validation
    $stmt = $db->prepare("DESCRIBE " . $tableName);
    $stmt->execute();
    $schema = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $columns = [];
    $pk = null;
    $requiredFields = [];

    foreach ($schema as $col) {
        $columns[$col['Field']] = $col;
        if ($col['Key'] == 'PRI') $pk = $col['Field'];
        if ($col['Null'] == 'NO' && $col['Extra'] != 'auto_increment' && $col['Default'] === null) {
            $requiredFields[] = $col['Field'];
        }
    }

    // Validate input data
    $validatedRows = [];
    foreach ($rows as $index => $row) {
        $cleanRow = [];
        // Check required fields
        foreach ($requiredFields as $field) {
            if (!isset($row[$field]) || $row[$field] === '') {
                throw new Exception("Row " . ($index + 1) . ": Missing required field '$field'");
            }
        }

        // Map data to columns
        foreach ($row as $key => $val) {
            if (array_key_exists($key, $columns)) {
                $cleanRow[$key] = $val;
                // Basic type check
                if (strpos($columns[$key]['Type'], 'int') !== false && !is_numeric($val) && $val !== null) {
                    throw new Exception("Row " . ($index + 1) . ": Field '$key' must be numeric (Value: $val)");
                }
            }
        }
        $validatedRows[] = $cleanRow;
    }

    // Execute transaction
    $db->beginTransaction();

    // Delete old data if overwrite = true
    if ($overwrite) {
        $db->exec("SET FOREIGN_KEY_CHECKS = 0");
        $db->exec("DELETE FROM " . $tableName);
        $db->exec("SET FOREIGN_KEY_CHECKS = 1");
    }

    // Prepare INSERT statement
    if (count($validatedRows) > 0) {
        $firstRow = $validatedRows[0];
        $fields = array_keys($firstRow);
        $placeholders = array_map(function ($f) {
            return ":$f";
        }, $fields);

        $sql = "INSERT INTO $tableName (" . implode(',', $fields) . ") VALUES (" . implode(',', $placeholders) . ")";

        // Handle Duplicate Keys (Upsert) if not overwrite
        if (!$overwrite) {
            $updateParts = [];
            foreach ($fields as $f) {
                if ($f != $pk) $updateParts[] = "$f = VALUES($f)";
            }
            if (!empty($updateParts)) {
                $sql .= " ON DUPLICATE KEY UPDATE " . implode(',', $updateParts);
            }
        }

        $stmtInsert = $db->prepare($sql);

        foreach ($validatedRows as $i => $row) {
            // Bind params
            foreach ($fields as $f) {
                $stmtInsert->bindValue(":$f", $row[$f]);
            }
            $stmtInsert->execute();
        }
    }

    $db->commit();

    $logMsg = "Imported " . count($validatedRows) . " rows into $tableName. Overwrite: " . ($overwrite ? 'Yes' : 'No');

    Response::success([
        "status" => "success",
        "message" => $logMsg,
        "rows_affected" => count($validatedRows)
    ]);

} catch (PDOException $e) {
    if ($db->inTransaction()) $db->rollBack();

    $msg = $e->getMessage();
    // Parse common SQL errors
    if (strpos($msg, '1452') !== false) {
        $msg = "Foreign key error: Referenced data does not exist.";
    } elseif (strpos($msg, '1062') !== false) {
        $msg = "Duplicate entry error.";
    }

    Response::error($msg, 500);
} catch (Exception $e) {
    if ($db->inTransaction()) $db->rollBack();
    Response::error($e->getMessage(), 400);
}
