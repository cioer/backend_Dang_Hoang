-- Migration: Create red_committee_logs table
-- Date: 2026-01-19
-- Purpose: Fix error 500 when creating red star accounts (missing logs table)

-- Create red_committee_logs table if not exists
CREATE TABLE IF NOT EXISTS `red_committee_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `actor_id` int(11) NOT NULL COMMENT 'ID of user who performed the action',
  `action` varchar(50) NOT NULL COMMENT 'Action performed: add, remove, replace, create_account',
  `target_user_id` int(11) NOT NULL COMMENT 'ID of user affected by the action',
  `class_id` int(11) DEFAULT NULL COMMENT 'Class ID associated with the action',
  `area` varchar(100) DEFAULT NULL COMMENT 'Area assigned',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `actor_id` (`actor_id`),
  KEY `target_user_id` (`target_user_id`),
  KEY `class_id` (`class_id`),
  KEY `action` (`action`),
  KEY `created_at` (`created_at`),
  CONSTRAINT `red_committee_logs_ibfk_1` FOREIGN KEY (`actor_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `red_committee_logs_ibfk_2` FOREIGN KEY (`target_user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `red_committee_logs_ibfk_3` FOREIGN KEY (`class_id`) REFERENCES `classes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Verify the table was created
SELECT 'red_committee_logs table created successfully!' AS status;
SHOW TABLES LIKE 'red_committee_logs';
DESC red_committee_logs;
