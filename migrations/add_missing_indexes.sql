-- ================================================
-- Add Missing Indexes for Student Ranking Feature
-- ================================================
-- This migration adds indexes to improve performance
-- of the get_student_ranking.php API endpoint
-- ================================================

USE school_management;

-- 1. Add index on violations.student_id (if not exists)
-- This improves JOIN performance between violations and users
SET @index_exists = (
    SELECT COUNT(*)
    FROM information_schema.STATISTICS
    WHERE TABLE_SCHEMA = 'school_management'
      AND TABLE_NAME = 'violations'
      AND INDEX_NAME = 'idx_violations_student_id'
);

SET @sql = IF(@index_exists = 0,
    'CREATE INDEX idx_violations_student_id ON violations(student_id)',
    'SELECT "Index idx_violations_student_id already exists" as message'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- 2. Add index on violations.created_at (if not exists)
-- This improves date range filtering performance
SET @index_exists = (
    SELECT COUNT(*)
    FROM information_schema.STATISTICS
    WHERE TABLE_SCHEMA = 'school_management'
      AND TABLE_NAME = 'violations'
      AND INDEX_NAME = 'idx_violations_created_at'
);

SET @sql = IF(@index_exists = 0,
    'CREATE INDEX idx_violations_created_at ON violations(created_at)',
    'SELECT "Index idx_violations_created_at already exists" as message'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- 3. Add composite index on violations (student_id, created_at)
-- This is even better for queries that filter by both
SET @index_exists = (
    SELECT COUNT(*)
    FROM information_schema.STATISTICS
    WHERE TABLE_SCHEMA = 'school_management'
      AND TABLE_NAME = 'violations'
      AND INDEX_NAME = 'idx_violations_student_date'
);

SET @sql = IF(@index_exists = 0,
    'CREATE INDEX idx_violations_student_date ON violations(student_id, created_at)',
    'SELECT "Index idx_violations_student_date already exists" as message'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- 4. Add index on violations.rule_id (if not exists)
-- This improves JOIN performance with conduct_rules
SET @index_exists = (
    SELECT COUNT(*)
    FROM information_schema.STATISTICS
    WHERE TABLE_SCHEMA = 'school_management'
      AND TABLE_NAME = 'violations'
      AND INDEX_NAME = 'idx_violations_rule_id'
);

SET @sql = IF(@index_exists = 0,
    'CREATE INDEX idx_violations_rule_id ON violations(rule_id)',
    'SELECT "Index idx_violations_rule_id already exists" as message'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- 5. Add index on student_details.class_id (if not exists)
-- This improves WHERE clause performance
SET @index_exists = (
    SELECT COUNT(*)
    FROM information_schema.STATISTICS
    WHERE TABLE_SCHEMA = 'school_management'
      AND TABLE_NAME = 'student_details'
      AND INDEX_NAME = 'idx_student_details_class_id'
);

SET @sql = IF(@index_exists = 0,
    'CREATE INDEX idx_student_details_class_id ON student_details(class_id)',
    'SELECT "Index idx_student_details_class_id already exists" as message'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- 6. Add index on student_details.user_id (if not exists)
-- This improves JOIN with users table
SET @index_exists = (
    SELECT COUNT(*)
    FROM information_schema.STATISTICS
    WHERE TABLE_SCHEMA = 'school_management'
      AND TABLE_NAME = 'student_details'
      AND INDEX_NAME = 'idx_student_details_user_id'
);

SET @sql = IF(@index_exists = 0,
    'CREATE INDEX idx_student_details_user_id ON student_details(user_id)',
    'SELECT "Index idx_student_details_user_id already exists" as message'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Verify all indexes were created
SELECT
    'Index Verification' as Step,
    TABLE_NAME,
    INDEX_NAME,
    COLUMN_NAME,
    NON_UNIQUE
FROM information_schema.STATISTICS
WHERE TABLE_SCHEMA = 'school_management'
  AND TABLE_NAME IN ('violations', 'student_details')
  AND INDEX_NAME LIKE 'idx_%'
ORDER BY TABLE_NAME, INDEX_NAME, SEQ_IN_INDEX;

SELECT '✓ Migration completed: Indexes added successfully' as Status;
