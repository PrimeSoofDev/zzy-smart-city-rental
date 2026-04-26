-- Migration: Add extended user statuses and action tracking

-- Alter users table to include 'rejected' and 'banned' statuses
ALTER TABLE users MODIFY COLUMN status ENUM('pending', 'verified', 'rejected', 'banned', 'suspended') DEFAULT 'pending';

-- Create table to track user rejections and bans with reasons
CREATE TABLE IF NOT EXISTS user_actions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    action_type ENUM('rejected', 'banned') NOT NULL,
    reason TEXT,
    performed_by INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (performed_by) REFERENCES users(id) ON DELETE RESTRICT,
    INDEX idx_user_id (user_id),
    INDEX idx_action_type (action_type),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB;
