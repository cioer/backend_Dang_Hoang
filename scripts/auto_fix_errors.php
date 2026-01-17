<?php
/**
 * Auto-fix script for common PHP/MySQL errors
 * Tự động phát hiện và sửa các lỗi thường gặp trong hệ thống quản lý nề nếp
 */

class AutoErrorFixer {
    private $db;
    private $log = [];
    
    public function __construct($db) {
        $this->db = $db;
    }
    
    public function log($message, $type = 'INFO') {
        $timestamp = date('Y-m-d H:i:s');
        $log_entry = "[$timestamp] [$type] $message";
        $this->log[] = $log_entry;
        echo $log_entry . "\n";
    }
    
    /**
     * Fix foreign key constraint errors
     */
    public function fixForeignKeyErrors() {
        $this->log("=== FIXING FOREIGN KEY CONSTRAINTS ===", "INFO");
        
        try {
            // Disable foreign key checks temporarily
            $this->db->exec("SET FOREIGN_KEY_CHECKS = 0");
            
            // Get all tables
            $tables = $this->db->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
            
            foreach ($tables as $table) {
                // Check for orphaned records
                $this->fixOrphanedRecords($table);
            }
            
            // Re-enable foreign key checks
            $this->db->exec("SET FOREIGN_KEY_CHECKS = 1");
            
            $this->log("Foreign key constraints fixed successfully", "SUCCESS");
        } catch (Exception $e) {
            $this->log("Error fixing foreign keys: " . $e->getMessage(), "ERROR");
        }
    }
    
    /**
     * Fix orphaned records in specific table
     */
    private function fixOrphanedRecords($table) {
        try {
            // Get table structure
            $columns = $this->db->query("SHOW COLUMNS FROM $table")->fetchAll(PDO::FETCH_ASSOC);
            
            foreach ($columns as $column) {
                $column_name = $column['Field'];
                
                // Check if column ends with '_id' (likely foreign key)
                if (preg_match('/_id$/', $column_name)) {
                    $referenced_table = str_replace('_id', '', $column_name);
                    
                    // Check if referenced table exists
                    $table_exists = $this->db->query("SHOW TABLES LIKE '$referenced_table'")->rowCount() > 0;
                    
                    if ($table_exists) {
                        // Find orphaned records
                        $orphaned_sql = "SELECT COUNT(*) as count FROM $table t 
                                        LEFT JOIN $referenced_table r ON t.$column_name = r.id 
                                        WHERE t.$column_name IS NOT NULL AND r.id IS NULL";
                        
                        $result = $this->db->query($orphaned_sql)->fetch(PDO::FETCH_ASSOC);
                        $orphaned_count = $result['count'];
                        
                        if ($orphaned_count > 0) {
                            $this->log("Found $orphaned_count orphaned records in $table.$column_name", "WARNING");
                            
                            // Option 1: Set orphaned records to NULL
                            $fix_sql = "UPDATE $table t 
                                       LEFT JOIN $referenced_table r ON t.$column_name = r.id 
                                       SET t.$column_name = NULL 
                                       WHERE t.$column_name IS NOT NULL AND r.id IS NULL";
                            
                            $this->db->exec($fix_sql);
                            $this->log("Fixed orphaned records in $table.$column_name", "SUCCESS");
                        }
                    }
                }
            }
        } catch (Exception $e) {
            $this->log("Error checking table $table: " . $e->getMessage(), "ERROR");
        }
    }
    
    /**
     * Fix duplicate key errors
     */
    public function fixDuplicateKeyErrors() {
        $this->log("=== FIXING DUPLICATE KEY ERRORS ===", "INFO");
        
        try {
            // Get all tables
            $tables = $this->db->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
            
            foreach ($tables as $table) {
                $this->fixDuplicateKeys($table);
            }
            
            $this->log("Duplicate key errors fixed successfully", "SUCCESS");
        } catch (Exception $e) {
            $this->log("Error fixing duplicate keys: " . $e->getMessage(), "ERROR");
        }
    }
    
    /**
     * Fix duplicate keys in specific table
     */
    private function fixDuplicateKeys($table) {
        try {
            // Get primary key columns
            $primary_keys = $this->db->query("SHOW KEYS FROM $table WHERE Key_name = 'PRIMARY'")
                                     ->fetchAll(PDO::FETCH_ASSOC);
            
            if (empty($primary_keys)) {
                return; // No primary key
            }
            
            $pk_columns = array_column($primary_keys, 'Column_name');
            $pk_column = $pk_columns[0]; // Assume single column PK
            
            // Find duplicate primary keys (shouldn't happen, but check anyway)
            $dup_sql = "SELECT $pk_column, COUNT(*) as count FROM $table GROUP BY $pk_column HAVING COUNT(*) > 1";
            $duplicates = $this->db->query($dup_sql)->fetchAll(PDO::FETCH_ASSOC);
            
            foreach ($duplicates as $duplicate) {
                $pk_value = $duplicate[$pk_column];
                $count = $duplicate['count'];
                
                $this->log("Found $count duplicate records with $pk_column = $pk_value in $table", "WARNING");
                
                // Keep only the first record, delete the rest
                $delete_sql = "DELETE FROM $table WHERE $pk_column = ? AND id NOT IN (
                              SELECT id FROM (SELECT MIN(id) as id FROM $table WHERE $pk_column = ?) as keep_id)";
                
                $stmt = $this->db->prepare($delete_sql);
                $stmt->execute([$pk_value, $pk_value]);
                
                $deleted = $stmt->rowCount();
                $this->log("Deleted $deleted duplicate records from $table", "SUCCESS");
            }
            
        } catch (Exception $e) {
            $this->log("Error fixing duplicates in $table: " . $e->getMessage(), "ERROR");
        }
    }
    
    /**
     * Fix character encoding issues
     */
    public function fixCharacterEncoding() {
        $this->log("=== FIXING CHARACTER ENCODING ===", "INFO");
        
        try {
            // Set proper character encoding
            $this->db->exec("SET NAMES utf8mb4");
            $this->db->exec("SET CHARACTER SET utf8mb4");
            
            // Get all tables
            $tables = $this->db->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
            
            foreach ($tables as $table) {
                // Convert table to utf8mb4
                $this->db->exec("ALTER TABLE $table CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
                $this->log("Fixed character encoding for table: $table", "SUCCESS");
            }
            
            $this->log("Character encoding fixed successfully", "SUCCESS");
        } catch (Exception $e) {
            $this->log("Error fixing character encoding: " . $e->getMessage(), "ERROR");
        }
    }
    
    /**
     * Fix missing table columns
     */
    public function fixMissingColumns() {
        $this->log("=== FIXING MISSING COLUMNS ===", "INFO");
        
        try {
            // Define expected columns for each table
            $expected_columns = [
                'users' => [
                    'username' => 'VARCHAR(50) NOT NULL UNIQUE',
                    'password' => 'VARCHAR(255) NOT NULL',
                    'full_name' => 'VARCHAR(100) NOT NULL',
                    'role' => 'VARCHAR(20) NOT NULL',
                    'email' => 'VARCHAR(100)',
                    'phone' => 'VARCHAR(20)',
                    'created_at' => 'TIMESTAMP DEFAULT CURRENT_TIMESTAMP',
                    'updated_at' => 'TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'
                ],
                'classes' => [
                    'class_name' => 'VARCHAR(20) NOT NULL',
                    'academic_year' => 'VARCHAR(9) NOT NULL',
                    'homeroom_teacher_id' => 'INT',
                    'grade_level' => 'INT',
                    'created_at' => 'TIMESTAMP DEFAULT CURRENT_TIMESTAMP'
                ],
                'violations' => [
                    'student_id' => 'INT NOT NULL',
                    'rule_id' => 'INT NOT NULL',
                    'reporter_id' => 'INT NOT NULL',
                    'note' => 'TEXT',
                    'created_at' => 'TIMESTAMP DEFAULT CURRENT_TIMESTAMP'
                ]
            ];
            
            foreach ($expected_columns as $table => $columns) {
                if ($this->tableExists($table)) {
                    $this->addMissingColumns($table, $columns);
                }
            }
            
            $this->log("Missing columns fixed successfully", "SUCCESS");
        } catch (Exception $e) {
            $this->log("Error fixing missing columns: " . $e->getMessage(), "ERROR");
        }
    }
    
    /**
     * Check if table exists
     */
    private function tableExists($table) {
        $result = $this->db->query("SHOW TABLES LIKE '$table'")->rowCount();
        return $result > 0;
    }
    
    /**
     * Add missing columns to table
     */
    private function addMissingColumns($table, $expected_columns) {
        try {
            // Get existing columns
            $existing_columns = $this->db->query("SHOW COLUMNS FROM $table")->fetchAll(PDO::FETCH_COLUMN);
            
            foreach ($expected_columns as $column_name => $column_definition) {
                if (!in_array($column_name, $existing_columns)) {
                    $sql = "ALTER TABLE $table ADD COLUMN $column_name $column_definition";
                    $this->db->exec($sql);
                    $this->log("Added missing column $column_name to $table", "SUCCESS");
                }
            }
        } catch (Exception $e) {
            $this->log("Error adding columns to $table: " . $e->getMessage(), "ERROR");
        }
    }
    
    /**
     * Fix auto-increment issues
     */
    public function fixAutoIncrement() {
        $this->log("=== FIXING AUTO-INCREMENT ISSUES ===", "INFO");
        
        try {
            // Get all tables
            $tables = $this->db->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
            
            foreach ($tables as $table) {
                // Check if table has auto-increment primary key
                $result = $this->db->query("SHOW COLUMNS FROM $table WHERE Extra LIKE '%auto_increment%'")
                                   ->fetchAll(PDO::FETCH_ASSOC);
                
                if (!empty($result)) {
                    // Reset auto-increment to max id + 1
                    $max_id = $this->db->query("SELECT MAX(id) as max_id FROM $table")->fetch(PDO::FETCH_ASSOC)['max_id'];
                    
                    if ($max_id !== null) {
                        $next_id = $max_id + 1;
                        $this->db->exec("ALTER TABLE $table AUTO_INCREMENT = $next_id");
                        $this->log("Fixed auto-increment for $table to $next_id", "SUCCESS");
                    }
                }
            }
            
            $this->log("Auto-increment issues fixed successfully", "SUCCESS");
        } catch (Exception $e) {
            $this->log("Error fixing auto-increment: " . $e->getMessage(), "ERROR");
        }
    }
    
    /**
     * Generate comprehensive error report
     */
    public function generateErrorReport() {
        $this->log("=== GENERATING ERROR REPORT ===", "INFO");
        
        $report = [
            'timestamp' => date('Y-m-d H:i:s'),
            'database_status' => $this->checkDatabaseStatus(),
            'table_status' => $this->checkTableStatus(),
            'common_issues' => $this->checkCommonIssues()
        ];
        
        // Save report to file
        $report_file = 'error_report_' . date('Y-m-d_H-i-s') . '.json';
        file_put_contents($report_file, json_encode($report, JSON_PRETTY_PRINT));
        
        $this->log("Error report generated: $report_file", "SUCCESS");
        return $report;
    }
    
    /**
     * Check database status
     */
    private function checkDatabaseStatus() {
        try {
            $result = $this->db->query("SELECT DATABASE() as db_name, VERSION() as version")->fetch(PDO::FETCH_ASSOC);
            return [
                'name' => $result['db_name'],
                'version' => $result['version'],
                'connection' => 'OK'
            ];
        } catch (Exception $e) {
            return ['connection' => 'FAILED', 'error' => $e->getMessage()];
        }
    }
    
    /**
     * Check table status
     */
    private function checkTableStatus() {
        $status = [];
        
        try {
            $tables = $this->db->query("SHOW TABLE STATUS")->fetchAll(PDO::FETCH_ASSOC);
            
            foreach ($tables as $table) {
                $status[$table['Name']] = [
                    'rows' => $table['Rows'],
                    'data_length' => $this->formatBytes($table['Data_length']),
                    'index_length' => $this->formatBytes($table['Index_length']),
                    'auto_increment' => $table['Auto_increment'],
                    'collation' => $table['Collation']
                ];
            }
        } catch (Exception $e) {
            $status['error'] = $e->getMessage();
        }
        
        return $status;
    }
    
    /**
     * Check common issues
     */
    private function checkCommonIssues() {
        $issues = [];
        
        try {
            // Check for tables without primary keys
            $no_pk_tables = $this->db->query("
                SELECT TABLE_NAME 
                FROM information_schema.TABLES 
                WHERE TABLE_SCHEMA = DATABASE() 
                AND TABLE_NAME NOT IN (
                    SELECT TABLE_NAME 
                    FROM information_schema.TABLE_CONSTRAINTS 
                    WHERE CONSTRAINT_TYPE = 'PRIMARY KEY'
                )
            ")->fetchAll(PDO::FETCH_COLUMN);
            
            if (!empty($no_pk_tables)) {
                $issues['tables_without_primary_key'] = $no_pk_tables;
            }
            
            // Check for tables using MyISAM engine
            $myisam_tables = $this->db->query("
                SELECT TABLE_NAME 
                FROM information_schema.TABLES 
                WHERE TABLE_SCHEMA = DATABASE() 
                AND ENGINE = 'MyISAM'
            ")->fetchAll(PDO::FETCH_COLUMN);
            
            if (!empty($myisam_tables)) {
                $issues['myisam_tables'] = $myisam_tables;
            }
            
        } catch (Exception $e) {
            $issues['check_error'] = $e->getMessage();
        }
        
        return $issues;
    }
    
    /**
     * Format bytes to human readable
     */
    private function formatBytes($bytes) {
        if ($bytes == 0) return '0 B';
        $units = ['B', 'KB', 'MB', 'GB'];
        $i = floor(log($bytes) / log(1024));
        return round($bytes / pow(1024, $i), 2) . ' ' . $units[$i];
    }
    
    /**
     * Run all fixes
     */
    public function runAllFixes() {
        $this->log("=== STARTING AUTO-FIX PROCESS ===", "INFO");
        
        $this->fixCharacterEncoding();
        $this->fixMissingColumns();
        $this->fixForeignKeyErrors();
        $this->fixDuplicateKeyErrors();
        $this->fixAutoIncrement();
        
        $this->generateErrorReport();
        
        $this->log("=== AUTO-FIX PROCESS COMPLETED ===", "INFO");
    }
    
    /**
     * Get log
     */
    public function getLog() {
        return $this->log;
    }
}

// Main execution
if (php_sapi_name() === 'cli') {
    try {
        // Database connection
        $db = new PDO('mysql:host=localhost;dbname=nenep_management', 'root', '');
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        echo "🛠️  AUTO ERROR FIXER FOR NENEP MANAGEMENT SYSTEM\n";
        echo "=" . str_repeat("=", 50) . "\n\n";
        
        $fixer = new AutoErrorFixer($db);
        
        if (isset($argv[1])) {
            // Run specific fix
            switch ($argv[1]) {
                case 'foreign_keys':
                    $fixer->fixForeignKeyErrors();
                    break;
                case 'duplicates':
                    $fixer->fixDuplicateKeyErrors();
                    break;
                case 'encoding':
                    $fixer->fixCharacterEncoding();
                    break;
                case 'columns':
                    $fixer->fixMissingColumns();
                    break;
                case 'auto_increment':
                    $fixer->fixAutoIncrement();
                    break;
                case 'report':
                    $fixer->generateErrorReport();
                    break;
                default:
                    echo "Unknown fix type: {$argv[1]}\n";
                    echo "Available: foreign_keys, duplicates, encoding, columns, auto_increment, report\n";
            }
        } else {
            // Run all fixes
            $fixer->runAllFixes();
        }
        
        echo "\n" . str_repeat("=", 50) . "\n";
        echo "✅ Auto-fix process completed!\n";
        
    } catch (Exception $e) {
        echo "❌ Error: " . $e->getMessage() . "\n";
        exit(1);
    }
}