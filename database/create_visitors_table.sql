CREATE TABLE IF NOT EXISTS visitor_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ip_address VARCHAR(45) NOT NULL,
    user_agent TEXT,
    location_data JSON,
    user_id INT NULL,
    has_scrolled TINYINT(1) DEFAULT 0,
    is_new_signup TINYINT(1) DEFAULT 0,
    is_first_visit_verified TINYINT(1) DEFAULT 0,
    referer TEXT,
    page_url TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_DATE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);
