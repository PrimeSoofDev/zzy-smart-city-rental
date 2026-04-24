-- 1. Password Reset Tokens Table
CREATE TABLE IF NOT EXISTS password_resets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    token VARCHAR(64) NOT NULL,
    expires_at DATETIME NOT NULL,
    used BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- 2. Update Staff Profiles to include location
ALTER TABLE staff_profiles ADD COLUMN IF NOT EXISTS assigned_location VARCHAR(255);

-- 3. Update Lawyer Profiles to include location
ALTER TABLE lawyer_profiles ADD COLUMN IF NOT EXISTS assigned_location VARCHAR(255);

-- 4. Add is_active to users
ALTER TABLE users ADD COLUMN IF NOT EXISTS is_active BOOLEAN DEFAULT FALSE;
