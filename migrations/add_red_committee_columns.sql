-- Migration: Add area and hash columns to red_committee_members table
-- Date: 2026-01-19
-- Purpose: Fix error 500 when creating red star accounts (missing area and hash columns)

-- Check if columns exist before adding
SET @dbname = DATABASE();
SET @tablename = "red_committee_members";

-- Add area column if not exists
SET @col_exists = (SELECT COUNT(*)
FROM information_schema.COLUMNS
WHERE TABLE_SCHEMA = @dbname
AND TABLE_NAME = @tablename
AND COLUMN_NAME = 'area');

SET @sql = IF(@col_exists = 0,
    'ALTER TABLE red_committee_members ADD COLUMN area VARCHAR(100) DEFAULT NULL AFTER class_id',
    'SELECT "area column already exists" AS message');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add hash column if not exists
SET @col_exists = (SELECT COUNT(*)
FROM information_schema.COLUMNS
WHERE TABLE_SCHEMA = @dbname
AND TABLE_NAME = @tablename
AND COLUMN_NAME = 'hash');

SET @sql = IF(@col_exists = 0,
    'ALTER TABLE red_committee_members ADD COLUMN hash VARCHAR(64) NOT NULL DEFAULT "" AFTER assigned_by',
    'SELECT "hash column already exists" AS message');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add unique index on hash if not exists
SET @index_exists = (SELECT COUNT(*)
FROM information_schema.STATISTICS
WHERE TABLE_SCHEMA = @dbname
AND TABLE_NAME = @tablename
AND INDEX_NAME = 'hash');

SET @sql = IF(@index_exists = 0,
    'ALTER TABLE red_committee_members ADD UNIQUE KEY hash (hash)',
    'SELECT "hash index already exists" AS message');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Verify the changes
SELECT 'Migration completed successfully!' AS status;
SELECT COLUMN_NAME, COLUMN_TYPE, IS_NULLABLE, COLUMN_DEFAULT
FROM information_schema.COLUMNS
WHERE TABLE_SCHEMA = DATABASE()
AND TABLE_NAME = 'red_committee_members'
AND COLUMN_NAME IN ('area', 'hash');
