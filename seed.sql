CREATE DATABASE IF NOT EXISTS zzy_rental;
USE zzy_rental;

-- Roles Table
CREATE TABLE roles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    role_name VARCHAR(50) NOT NULL UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Users Table
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(20),
    status ENUM('pending', 'verified', 'suspended') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_email (email),
    INDEX idx_username (username)
) ENGINE=InnoDB;

-- User Roles (RBAC)
CREATE TABLE user_roles (
    user_id INT NOT NULL,
    role_id INT NOT NULL,
    PRIMARY KEY (user_id, role_id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Tenant Profiles
CREATE TABLE tenant_profiles (
    user_id INT PRIMARY KEY,
    bvn_nin VARCHAR(20) NOT NULL,
    address TEXT NOT NULL,
    employment_status VARCHAR(100),
    guarantor_name VARCHAR(100),
    guarantor_phone VARCHAR(20),
    verification_status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Landlord Profiles
CREATE TABLE landlord_profiles (
    user_id INT PRIMARY KEY,
    business_reg_number VARCHAR(50),
    verification_status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Staff Profiles
CREATE TABLE staff_profiles (
    user_id INT PRIMARY KEY,
    staff_id VARCHAR(20) UNIQUE NOT NULL,
    department VARCHAR(50),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Lawyer Profiles
CREATE TABLE lawyer_profiles (
    user_id INT PRIMARY KEY,
    license_number VARCHAR(50) UNIQUE NOT NULL,
    firm_name VARCHAR(100),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Properties
CREATE TABLE properties (
    id INT AUTO_INCREMENT PRIMARY KEY,
    landlord_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    address TEXT NOT NULL,
    price DECIMAL(15, 2) NOT NULL,
    rooms INT,
    bathrooms INT,
    property_type ENUM('apartment', 'house', 'commercial', 'land') NOT NULL,
    status ENUM('draft', 'pending_verification', 'approved', 'rented', 'rejected') DEFAULT 'pending_verification',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (landlord_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_status (status)
) ENGINE=InnoDB;

-- Property Media
CREATE TABLE property_media (
    id INT AUTO_INCREMENT PRIMARY KEY,
    property_id INT NOT NULL,
    file_path VARCHAR(255) NOT NULL,
    is_primary BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (property_id) REFERENCES properties(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Property Verifications
CREATE TABLE property_verifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    property_id INT NOT NULL,
    staff_id INT NOT NULL,
    verified_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    notes TEXT,
    result ENUM('approved', 'rejected') NOT NULL,
    FOREIGN KEY (property_id) REFERENCES properties(id) ON DELETE CASCADE,
    FOREIGN KEY (staff_id) REFERENCES users(id)
) ENGINE=InnoDB;

-- Rental Requests
CREATE TABLE rental_requests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tenant_id INT NOT NULL,
    property_id INT NOT NULL,
    request_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('pending', 'processing', 'paid', 'cancelled', 'completed') DEFAULT 'pending',
    FOREIGN KEY (tenant_id) REFERENCES users(id),
    FOREIGN KEY (property_id) REFERENCES properties(id),
    INDEX idx_status (status)
) ENGINE=InnoDB;

-- Transactions
CREATE TABLE transactions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    request_id INT NOT NULL,
    user_id INT NOT NULL,
    amount DECIMAL(15, 2) NOT NULL,
    payment_method VARCHAR(50),
    transaction_type ENUM('escrow_deposit', 'landlord_payout', 'refund') NOT NULL,
    status ENUM('pending', 'completed', 'failed') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (request_id) REFERENCES rental_requests(id),
    FOREIGN KEY (user_id) REFERENCES users(id)
) ENGINE=InnoDB;

-- Agreements
CREATE TABLE agreements (
    id INT AUTO_INCREMENT PRIMARY KEY,
    request_id INT NOT NULL,
    lawyer_id INT NOT NULL,
    document_path VARCHAR(255) NOT NULL,
    signed_at TIMESTAMP NULL,
    status ENUM('draft', 'signed', 'expired') DEFAULT 'draft',
    FOREIGN KEY (request_id) REFERENCES rental_requests(id),
    FOREIGN KEY (lawyer_id) REFERENCES users(id)
) ENGINE=InnoDB;

-- Notifications
CREATE TABLE notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    message TEXT NOT NULL,
    is_read BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Audit Logs
CREATE TABLE audit_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    action VARCHAR(255) NOT NULL,
    entity_type VARCHAR(50),
    entity_id INT,
    ip_address VARCHAR(45),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- Disputes
CREATE TABLE disputes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    request_id INT NOT NULL,
    raised_by INT NOT NULL,
    reason TEXT NOT NULL,
    status ENUM('open', 'resolving', 'closed') DEFAULT 'open',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (request_id) REFERENCES rental_requests(id),
    FOREIGN KEY (raised_by) REFERENCES users(id)
) ENGINE=InnoDB;

-- Initial Role Seed
INSERT INTO roles (role_name) VALUES ('Tenant'), ('Landlord'), ('Staff'), ('Admin'), ('Lawyer');
