CREATE TABLE IF NOT EXISTS system_settings (
    setting_key VARCHAR(50) PRIMARY KEY,
    setting_value TEXT,
    description VARCHAR(255),
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

INSERT IGNORE INTO system_settings (setting_key, setting_value, description) VALUES
('platform_name', 'ZZY Rental', 'The official name of the marketplace'),
('platform_email', 'support@zzyrental.com', 'System email for notifications'),
('platform_phone', '+234 800 123 4567', 'Official support contact number'),
('commission_rate', '5.0', 'Percentage commission on each successful rental'),
('maintenance_mode', '0', '0 = Off, 1 = On'),
('registration_enabled', '1', 'Allow new users to sign up');
