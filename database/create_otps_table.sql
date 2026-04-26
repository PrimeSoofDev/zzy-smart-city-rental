CREATE TABLE IF NOT EXISTS otps (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NULL,
    identifier VARCHAR(255) NOT NULL, -- Email address or Phone number
    otp_code VARCHAR(255) NOT NULL, -- Hashed OTP code
    channel ENUM('email', 'phone') NOT NULL,
    status ENUM('pending', 'verified', 'expired') DEFAULT 'pending',
    attempts INT DEFAULT 0,
    expires_at DATETIME NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX (identifier),
    INDEX (user_id)
);
