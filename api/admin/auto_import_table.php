<?php
include_once '../../config/database.php';
include_once '../../config/jwt.php';

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");

// 1. Xác thực Admin
$headers = getallheaders();
$token = isset($headers['Authorization']) ? str_replace('Bearer ', '', $headers['Authorization']) : null;
/*
// Tạm tắt check token để dễ test, bật lại khi production
$decoded = validateJwt($token);
if (!$decoded || $decoded['data']->role != 'admin') {
    http_response_code(401);
    echo json_encode(["status" => "error", "message" => "Unauthorized"]);
    exit;
}
*/

$db = (new Database())->getConnection();
$data = json_decode(file_get_contents("php://input"), true);

$tableName = isset($data['table_name']) ? $data['table_name'] : null;
$rows = isset($data['data']) ? $data['data'] : [];
$overwrite = isset($data['overwrite']) ? $data['overwrite'] : false; // True: Xóa cũ chèn mới

if (!$tableName || empty($rows)) {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Missing table_name or data"]);
    exit;
}

// 2. Kiểm tra tên bảng hợp lệ (White-list)
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
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Invalid table name: $tableName"]);
    exit;
}

try {
    // 3. Lấy Schema bảng để validate
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

    // 4. Validate Dữ liệu đầu vào
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
                // Basic type check (Optional: expand this)
                if (strpos($columns[$key]['Type'], 'int') !== false && !is_numeric($val) && $val !== null) {
                     throw new Exception("Row " . ($index + 1) . ": Field '$key' must be numeric (Value: $val)");
                }
            }
        }
        $validatedRows[] = $cleanRow;
    }

    // 5. Thực thi Transaction
    $db->beginTransaction();

    // Xóa dữ liệu cũ nếu overwrite = true
    if ($overwrite) {
        // Tắt FK check tạm thời để xóa (Nguy hiểm, cân nhắc kỹ)
        $db->exec("SET FOREIGN_KEY_CHECKS = 0");
        $db->exec("DELETE FROM " . $tableName);
        $db->exec("SET FOREIGN_KEY_CHECKS = 1");
    }

    // Prepare INSERT statement
    if (count($validatedRows) > 0) {
        $firstRow = $validatedRows[0];
        $fields = array_keys($firstRow);
        $placeholders = array_map(function($f) { return ":$f"; }, $fields);
        
        $sql = "INSERT INTO $tableName (" . implode(',', $fields) . ") VALUES (" . implode(',', $placeholders) . ")";
        
        // Handle Duplicate Keys (Upsert) nếu không overwrite
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

    // 6. Ghi log (Nếu có bảng system_logs hoặc ghi file)
    $logMsg = "Imported " . count($validatedRows) . " rows into $tableName. Overwrite: " . ($overwrite ? 'Yes' : 'No');
    // file_put_contents('../../logs/import.log', date('Y-m-d H:i:s') . " - $logMsg\n", FILE_APPEND);

    echo json_encode([
        "status" => "success", 
        "message" => $logMsg,
        "rows_affected" => count($validatedRows)
    ]);

} catch (PDOException $e) {
    if ($db->inTransaction()) $db->rollBack();
    
    $msg = $e->getMessage();
    // Phân tích lỗi SQL phổ biến
    if (strpos($msg, '1452') !== false) {
        $msg = "Lỗi khóa ngoại: Dữ liệu tham chiếu không tồn tại.";
    } elseif (strpos($msg, '1062') !== false) {
        $msg = "Lỗi trùng lặp dữ liệu (Duplicate Entry).";
    }

    http_response_code(500);
    echo json_encode(["status" => "error", "message" => $msg, "debug" => $e->getMessage()]);
} catch (Exception $e) {
    if ($db->inTransaction()) $db->rollBack();
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}
?>