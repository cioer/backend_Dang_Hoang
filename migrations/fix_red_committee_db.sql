-- Fix database schema for Red Committee feature
-- Run this on your Server MySQL/MariaDB

-- 1. Update users table
ALTER TABLE users ADD COLUMN class_id INT DEFAULT NULL;
ALTER TABLE users ADD COLUMN is_red_star TINYINT(1) DEFAULT 0;
ALTER TABLE users MODIFY COLUMN role ENUM('admin','teacher','student','parent','red_star') NOT NULL;

-- 2. Create red_committee_logs table
CREATE TABLE IF NOT EXISTS red_committee_logs (
  id int(11) NOT NULL AUTO_INCREMENT,
  actor_id int(11) NOT NULL,
  action varchar(50) NOT NULL,
  target_user_id int(11) NOT NULL,
  class_id int(11) DEFAULT NULL,
  area varchar(100) DEFAULT NULL,
  created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY actor_id (actor_id),
  KEY target_user_id (target_user_id),
  KEY class_id (class_id),
  KEY action (action),
  KEY created_at (created_at),
  CONSTRAINT red_committee_logs_ibfk_1 FOREIGN KEY (actor_id) REFERENCES users (id) ON DELETE CASCADE,
  CONSTRAINT red_committee_logs_ibfk_2 FOREIGN KEY (target_user_id) REFERENCES users (id) ON DELETE CASCADE,
  CONSTRAINT red_committee_logs_ibfk_3 FOREIGN KEY (class_id) REFERENCES classes (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 3. Update red_committee_members table (just in case)
ALTER TABLE red_committee_members ADD COLUMN area VARCHAR(100) DEFAULT NULL AFTER class_id;
ALTER TABLE red_committee_members ADD COLUMN hash VARCHAR(64) NOT NULL DEFAULT "" AFTER assigned_by;
ALTER TABLE red_committee_members ADD UNIQUE KEY hash (hash);
