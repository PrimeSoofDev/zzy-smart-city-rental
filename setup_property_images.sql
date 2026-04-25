-- Fix: Create property_images table alias for compatibility
-- The LandlordController uses 'property_images' but seed.sql creates 'property_media'
-- Run this SQL against zzy_rental database if property_images table is missing

USE zzy_rental;

CREATE TABLE IF NOT EXISTS property_images (
    id INT AUTO_INCREMENT PRIMARY KEY,
    property_id INT NOT NULL,
    image_url VARCHAR(255) NOT NULL,
    is_primary BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (property_id) REFERENCES properties(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Also ensure property_verifications has the right columns
-- (already in seed.sql, this is just a safety check)
ALTER TABLE property_verifications
    MODIFY COLUMN result ENUM('approved','rejected') NOT NULL;
