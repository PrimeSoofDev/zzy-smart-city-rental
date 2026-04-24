-- Add default admin user
INSERT INTO users (username, email, password, status)
VALUES ('admin', 'admin@zzyrental.com', '$2y$10$8CjYVfS8V.S8V.S8V.S8V.S8V.S8V.S8V.S8V.S8V.S8V.', 'verified');
-- Note: The password above is a dummy. I will create a setup script to ensure the real hash is used.
