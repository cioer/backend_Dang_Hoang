#!/bin/bash

#############################################
# Check Database Indexes Script
# Kiểm tra indexes cho student ranking feature
#############################################

echo "=========================================="
echo "DATABASE INDEXES HEALTH CHECK"
echo "=========================================="

DB_CONTAINER="school_management_db"
DB_NAME="school_management"
DB_USER="root"
DB_PASS="root_password"

# Colors
GREEN='\033[0;32m'
RED='\033[0;31m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Function to run SQL query
run_query() {
    local query=$1
    docker exec $DB_CONTAINER mysql -u$DB_USER -p$DB_PASS $DB_NAME -e "$query" 2>/dev/null
}

echo ""
echo -e "${BLUE}=== 1. VIOLATIONS TABLE INDEXES ===${NC}"
run_query "
SELECT
    TABLE_NAME,
    INDEX_NAME,
    COLUMN_NAME,
    NON_UNIQUE,
    SEQ_IN_INDEX
FROM information_schema.STATISTICS
WHERE TABLE_SCHEMA = '$DB_NAME'
  AND TABLE_NAME = 'violations'
ORDER BY INDEX_NAME, SEQ_IN_INDEX;
"

echo ""
echo -e "${BLUE}=== 2. STUDENT_DETAILS TABLE INDEXES ===${NC}"
run_query "
SELECT
    TABLE_NAME,
    INDEX_NAME,
    COLUMN_NAME,
    NON_UNIQUE,
    SEQ_IN_INDEX
FROM information_schema.STATISTICS
WHERE TABLE_SCHEMA = '$DB_NAME'
  AND TABLE_NAME = 'student_details'
ORDER BY INDEX_NAME, SEQ_IN_INDEX;
"

echo ""
echo -e "${BLUE}=== 3. CONDUCT_RULES TABLE INDEXES ===${NC}"
run_query "
SELECT
    TABLE_NAME,
    INDEX_NAME,
    COLUMN_NAME,
    NON_UNIQUE,
    SEQ_IN_INDEX
FROM information_schema.STATISTICS
WHERE TABLE_SCHEMA = '$DB_NAME'
  AND TABLE_NAME = 'conduct_rules'
ORDER BY INDEX_NAME, SEQ_IN_INDEX;
"

echo ""
echo -e "${BLUE}=== 4. USERS TABLE INDEXES ===${NC}"
run_query "
SELECT
    TABLE_NAME,
    INDEX_NAME,
    COLUMN_NAME,
    NON_UNIQUE,
    SEQ_IN_INDEX
FROM information_schema.STATISTICS
WHERE TABLE_SCHEMA = '$DB_NAME'
  AND TABLE_NAME = 'users'
ORDER BY INDEX_NAME, SEQ_IN_INDEX;
"

echo ""
echo -e "${BLUE}=== 5. FOREIGN KEY CONSTRAINTS ===${NC}"
run_query "
SELECT
    CONSTRAINT_NAME,
    TABLE_NAME,
    COLUMN_NAME,
    REFERENCED_TABLE_NAME,
    REFERENCED_COLUMN_NAME
FROM information_schema.KEY_COLUMN_USAGE
WHERE TABLE_SCHEMA = '$DB_NAME'
  AND TABLE_NAME IN ('violations', 'student_details', 'conduct_rules')
  AND REFERENCED_TABLE_NAME IS NOT NULL
ORDER BY TABLE_NAME, CONSTRAINT_NAME;
"

echo ""
echo -e "${BLUE}=== 6. ORPHAN VIOLATIONS CHECK ===${NC}"
run_query "
SELECT
    COUNT(*) as orphan_count
FROM violations v
LEFT JOIN conduct_rules cr ON v.rule_id = cr.id
WHERE cr.id IS NULL;
"

echo ""
echo -e "${BLUE}=== 7. TABLE ROW COUNTS ===${NC}"
run_query "
SELECT
    'violations' as table_name,
    COUNT(*) as row_count
FROM violations
UNION ALL
SELECT
    'student_details' as table_name,
    COUNT(*) as row_count
FROM student_details
UNION ALL
SELECT
    'conduct_rules' as table_name,
    COUNT(*) as row_count
FROM conduct_rules
UNION ALL
SELECT
    'users (students)' as table_name,
    COUNT(*) as row_count
FROM users
WHERE role = 'student';
"

echo ""
echo -e "${BLUE}=== 8. STUDENTS PER CLASS DISTRIBUTION ===${NC}"
run_query "
SELECT
    c.id as class_id,
    c.name as class_name,
    COUNT(sd.user_id) as student_count,
    CASE
        WHEN COUNT(sd.user_id) > 50 THEN 'May need pagination'
        WHEN COUNT(sd.user_id) > 100 THEN 'NEEDS pagination'
        ELSE 'OK'
    END as performance_note
FROM classes c
LEFT JOIN student_details sd ON c.id = sd.class_id
GROUP BY c.id, c.name
ORDER BY student_count DESC
LIMIT 10;
"

echo ""
echo -e "${BLUE}=== 9. QUERY EXECUTION PLAN (EXPLAIN) ===${NC}"
echo "Testing query performance for get_student_ranking..."
run_query "
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
" 2>&1 | head -20

echo ""
echo "=========================================="
echo -e "${GREEN}RECOMMENDATIONS${NC}"
echo "=========================================="

# Check if critical indexes exist
violations_student_idx=$(run_query "
SELECT COUNT(*) FROM information_schema.STATISTICS
WHERE TABLE_SCHEMA = '$DB_NAME'
  AND TABLE_NAME = 'violations'
  AND COLUMN_NAME = 'student_id'
  AND INDEX_NAME != 'PRIMARY';
" | tail -1)

violations_created_idx=$(run_query "
SELECT COUNT(*) FROM information_schema.STATISTICS
WHERE TABLE_SCHEMA = '$DB_NAME'
  AND TABLE_NAME = 'violations'
  AND COLUMN_NAME = 'created_at';
" | tail -1)

student_details_class_idx=$(run_query "
SELECT COUNT(*) FROM information_schema.STATISTICS
WHERE TABLE_SCHEMA = '$DB_NAME'
  AND TABLE_NAME = 'student_details'
  AND COLUMN_NAME = 'class_id'
  AND INDEX_NAME != 'PRIMARY';
" | tail -1)

echo ""
if [ "$violations_student_idx" -eq 0 ]; then
    echo -e "${RED}✗ MISSING:${NC} violations.student_id index"
    echo "  Recommend: CREATE INDEX idx_violations_student_id ON violations(student_id);"
else
    echo -e "${GREEN}✓ OK:${NC} violations.student_id has index"
fi

if [ "$violations_created_idx" -eq 0 ]; then
    echo -e "${YELLOW}⚠ MISSING:${NC} violations.created_at index (for date filtering)"
    echo "  Recommend: CREATE INDEX idx_violations_created_at ON violations(created_at);"
else
    echo -e "${GREEN}✓ OK:${NC} violations.created_at has index"
fi

if [ "$student_details_class_idx" -eq 0 ]; then
    echo -e "${RED}✗ MISSING:${NC} student_details.class_id index"
    echo "  Recommend: CREATE INDEX idx_student_details_class_id ON student_details(class_id);"
else
    echo -e "${GREEN}✓ OK:${NC} student_details.class_id has index"
fi

echo ""
echo "=========================================="
echo "Check completed!"
echo "=========================================="
