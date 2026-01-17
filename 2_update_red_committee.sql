-- Cập nhật bảng red_committee_members để hỗ trợ tính năng mới
-- Thêm cột area (khu vực phân công) và hash (mã kiểm tra toàn vẹn)

ALTER TABLE `red_committee_members`
ADD COLUMN `area` varchar(100) DEFAULT NULL AFTER `class_id`,
ADD COLUMN `hash` varchar(64) NOT NULL AFTER `assigned_by`;

-- Cập nhật lại các bản ghi cũ (nếu có) để tránh lỗi hash null
UPDATE `red_committee_members` 
SET `hash` = SHA2(CONCAT(user_id, '|', IFNULL(class_id, 'null'), '|', IFNULL(area, 'null')), 256)
WHERE `hash` = '';
