-- Migration: Cập nhật tính năng điểm danh
-- Version: 3
-- Description: Phân biệt vắng có phép / không phép, thêm rule vắng không phép

-- Cập nhật enum status trong bảng attendance
-- Thêm 'absent_excused' (vắng có phép) và 'absent_unexcused' (vắng không phép)
ALTER TABLE `attendance`
MODIFY COLUMN `status` enum('present','absent','absent_excused','absent_unexcused','late') DEFAULT 'present';

-- Thêm rule vắng không phép (-10 điểm) nếu chưa tồn tại
INSERT INTO `conduct_rules` (`rule_name`, `points`, `type`, `description`)
SELECT 'Vắng học không phép', 10, 'minus', 'Học sinh vắng học không có lý do chính đáng'
WHERE NOT EXISTS (
    SELECT 1 FROM `conduct_rules` WHERE `rule_name` = 'Vắng học không phép'
);
