-- Migration: Add red_star support columns to users table
-- Date: 2026-01-19
-- Purpose: Fix error 500 when creating red star accounts

-- Check if columns exist before adding
SET @dbname = DATABASE();
SET @tablename = "users";

-- Add class_id column if not exists
SET @col_exists = (SELECT COUNT(*)
FROM information_schema.COLUMNS
WHERE TABLE_SCHEMA = @dbname
AND TABLE_NAME = @tablename
AND COLUMN_NAME = 'class_id');

SET @sql = IF(@col_exists = 0,
    'ALTER TABLE users ADD COLUMN class_id INT(11) DEFAULT NULL AFTER role',
    'SELECT "class_id column already exists" AS message');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add is_red_star column if not exists
SET @col_exists = (SELECT COUNT(*)
FROM information_schema.COLUMNS
WHERE TABLE_SCHEMA = @dbname
AND TABLE_NAME = @tablename
AND COLUMN_NAME = 'is_red_star');

SET @sql = IF(@col_exists = 0,
    'ALTER TABLE users ADD COLUMN is_red_star TINYINT(1) DEFAULT 0 AFTER role',
    'SELECT "is_red_star column already exists" AS message');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Update role enum to include 'red_star'
-- Note: This will recreate the column, so it might be slow on large tables
ALTER TABLE users MODIFY COLUMN role ENUM('admin','teacher','student','parent','red_star') NOT NULL DEFAULT 'student';

-- Verify the changes
SELECT 'Migration completed successfully!' AS status;
SELECT COLUMN_NAME, COLUMN_TYPE, IS_NULLABLE, COLUMN_DEFAULT
FROM information_schema.COLUMNS
WHERE TABLE_SCHEMA = DATABASE()
AND TABLE_NAME = 'users'
AND COLUMN_NAME IN ('role', 'class_id', 'is_red_star');
