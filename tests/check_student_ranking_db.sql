-- ================================================
-- Database Checks for Student Ranking Feature
-- ================================================

-- 1. Check indexes on violations table
-- ================================================
SELECT
    'Violations Table Indexes' as Check_Type,
    TABLE_NAME,
    INDEX_NAME,
    COLUMN_NAME,
    SEQ_IN_INDEX,
    NON_UNIQUE
FROM information_schema.STATISTICS
WHERE TABLE_SCHEMA = 'school_management'
  AND TABLE_NAME = 'violations'
ORDER BY INDEX_NAME, SEQ_IN_INDEX;

-- Expected indexes:
-- - student_id (for efficient JOIN on user)
-- - rule_id (for efficient JOIN on conduct_rules)
-- - created_at (for date range filtering)


-- 2. Check indexes on student_details table
-- ================================================
SELECT
    'Student Details Table Indexes' as Check_Type,
    TABLE_NAME,
    INDEX_NAME,
    COLUMN_NAME,
    SEQ_IN_INDEX,
    NON_UNIQUE
FROM information_schema.STATISTICS
WHERE TABLE_SCHEMA = 'school_management'
  AND TABLE_NAME = 'student_details'
ORDER BY INDEX_NAME, SEQ_IN_INDEX;

-- Expected indexes:
-- - user_id
-- - class_id (critical for WHERE clause)


-- 3. Check foreign key constraints
-- ================================================
SELECT
    'Foreign Key Constraints' as Check_Type,
    CONSTRAINT_NAME,
    TABLE_NAME,
    COLUMN_NAME,
    REFERENCED_TABLE_NAME,
    REFERENCED_COLUMN_NAME
FROM information_schema.KEY_COLUMN_USAGE
WHERE TABLE_SCHEMA = 'school_management'
  AND TABLE_NAME IN ('violations', 'student_details')
  AND REFERENCED_TABLE_NAME IS NOT NULL
ORDER BY TABLE_NAME, CONSTRAINT_NAME;


-- 4. Check for orphan violations (rule_id doesn't exist)
-- ================================================
SELECT
    'Orphan Violations Check' as Check_Type,
    COUNT(*) as orphan_count,
    GROUP_CONCAT(DISTINCT v.rule_id) as missing_rule_ids
FROM violations v
LEFT JOIN conduct_rules cr ON v.rule_id = cr.id
WHERE cr.id IS NULL;

-- Expected: orphan_count = 0


-- 5. Check for students without class assignment
-- ================================================
SELECT
    'Students Without Class' as Check_Type,
    COUNT(*) as students_without_class
FROM users u
WHERE u.role = 'student'
  AND NOT EXISTS (
      SELECT 1 FROM student_details sd WHERE sd.user_id = u.id
  );

-- Expected: Should be 0 or very few


-- 6. Sample data verification - Test ranking calculation
-- ================================================
SELECT
    'Sample Ranking Data' as Check_Type,
    u.id as student_id,
    u.full_name,
    u.username as student_code,
    sd.class_id,
    COUNT(v.id) as violation_count,
    COALESCE(SUM(cr.points), 0) as total_deducted
FROM users u
JOIN student_details sd ON u.id = sd.user_id
LEFT JOIN violations v ON u.id = v.student_id
LEFT JOIN conduct_rules cr ON v.rule_id = cr.id
WHERE u.role = 'student'
GROUP BY u.id, u.full_name, u.username, sd.class_id
ORDER BY total_deducted ASC, violation_count ASC, u.full_name ASC
LIMIT 10;


-- 7. Check classes and their homeroom teachers
-- ================================================
SELECT
    'Classes with Homeroom Teachers' as Check_Type,
    c.id as class_id,
    c.name as class_name,
    c.homeroom_teacher_id,
    t.full_name as homeroom_teacher,
    COUNT(DISTINCT sd.user_id) as student_count
FROM classes c
LEFT JOIN users t ON c.homeroom_teacher_id = t.id
LEFT JOIN student_details sd ON c.id = sd.class_id
GROUP BY c.id, c.name, c.homeroom_teacher_id, t.full_name
ORDER BY c.id;


-- 8. Check teachers without homeroom class
-- ================================================
SELECT
    'Teachers Without Homeroom Class' as Check_Type,
    u.id as teacher_id,
    u.full_name,
    u.username,
    COUNT(DISTINCT cta.class_id) as assigned_classes,
    COUNT(DISTINCT s.class_id) as schedule_classes
FROM users u
LEFT JOIN classes c ON u.id = c.homeroom_teacher_id
LEFT JOIN class_teacher_assignments cta ON u.id = cta.teacher_id
LEFT JOIN schedule s ON u.id = s.teacher_id
WHERE u.role = 'teacher'
  AND c.id IS NULL  -- No homeroom class
GROUP BY u.id, u.full_name, u.username
HAVING assigned_classes > 0 OR schedule_classes > 0;

-- These teachers will get 404 error if they don't pass class_id


-- 9. Check for violations with NULL or 0 points
-- ================================================
SELECT
    'Violations with Problematic Points' as Check_Type,
    COUNT(*) as count,
    'NULL points' as issue_type
FROM violations v
LEFT JOIN conduct_rules cr ON v.rule_id = cr.id
WHERE cr.points IS NULL

UNION ALL

SELECT
    'Violations with Problematic Points' as Check_Type,
    COUNT(*) as count,
    'Zero points' as issue_type
FROM violations v
JOIN conduct_rules cr ON v.rule_id = cr.id
WHERE cr.points = 0;


-- 10. Performance test query - Explain plan
-- ================================================
-- Run this separately to see query execution plan
EXPLAIN SELECT
    u.id as student_id,
    u.full_name,
    u.username as student_code,
    COALESCE(SUM(cr.points), 0) as total_deducted,
    COUNT(v.id) as violation_count
FROM student_details sd
JOIN users u ON sd.user_id = u.id
LEFT JOIN violations v ON u.id = v.student_id
LEFT JOIN conduct_rules cr ON v.rule_id = cr.id
WHERE sd.class_id = 1
GROUP BY u.id, u.full_name, u.username
ORDER BY total_deducted ASC, violation_count ASC, u.full_name ASC;

-- Check for:
-- - Using indexes (type should be 'ref' or 'eq_ref', not 'ALL')
-- - Rows scanned should be reasonable
-- - Extra should not show 'Using filesort' or 'Using temporary' excessively


-- 11. Check date filter performance (with created_at)
-- ================================================
EXPLAIN SELECT
    u.id as student_id,
    u.full_name,
    u.username as student_code,
    COALESCE(SUM(cr.points), 0) as total_deducted,
    COUNT(v.id) as violation_count
FROM student_details sd
JOIN users u ON sd.user_id = u.id
LEFT JOIN violations v ON u.id = v.student_id
    AND v.created_at BETWEEN '2026-01-01 00:00:00' AND '2026-01-31 23:59:59'
LEFT JOIN conduct_rules cr ON v.rule_id = cr.id
WHERE sd.class_id = 1
GROUP BY u.id, u.full_name, u.username
ORDER BY total_deducted ASC, violation_count ASC, u.full_name ASC;

-- Should use index on created_at if available


-- 12. Count total students per class
-- ================================================
SELECT
    'Students Per Class Distribution' as Check_Type,
    c.id as class_id,
    c.name as class_name,
    COUNT(sd.user_id) as student_count,
    CASE
        WHEN COUNT(sd.user_id) > 50 THEN 'May need pagination'
        WHEN COUNT(sd.user_id) > 100 THEN 'DEFINITELY needs pagination'
        ELSE 'OK'
    END as performance_note
FROM classes c
LEFT JOIN student_details sd ON c.id = sd.class_id
GROUP BY c.id, c.name
ORDER BY student_count DESC;


-- ================================================
-- RECOMMENDATIONS BASED ON RESULTS
-- ================================================
/*
1. If violations.student_id has no index:
   CREATE INDEX idx_violations_student_id ON violations(student_id);

2. If violations.created_at has no index:
   CREATE INDEX idx_violations_created_at ON violations(created_at);

3. If student_details.class_id has no index:
   CREATE INDEX idx_student_details_class_id ON student_details(class_id);

4. If there are orphan violations:
   -- Fix data or add FK constraint
   ALTER TABLE violations
   ADD CONSTRAINT fk_violations_rule_id
   FOREIGN KEY (rule_id) REFERENCES conduct_rules(id)
   ON DELETE CASCADE;

5. If teachers without homeroom class exist:
   -- Update get_student_ranking.php to fallback to assigned class
   -- OR require class_id parameter for these teachers

6. For classes with >50 students:
   -- Add pagination to API
   -- Add caching for frequently accessed rankings
*/
