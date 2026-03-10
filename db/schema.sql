-- Corruption Control System Database Schema

CREATE DATABASE IF NOT EXISTS corruption_control_db;
USE corruption_control_db;

-- Users table (for all users: citizens, admins, officers)
CREATE TABLE IF NOT EXISTS users (
    user_id INT PRIMARY KEY AUTO_INCREMENT,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    phone VARCHAR(20),
    user_type ENUM('citizen', 'admin', 'officer') NOT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_email (email),
    INDEX idx_user_type (user_type)
);

-- Officers table (extends users for officers)
CREATE TABLE IF NOT EXISTS officers (
    officer_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL UNIQUE,
    department VARCHAR(100),
    badge_number VARCHAR(50) UNIQUE,
    designation VARCHAR(100),
    is_available BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    INDEX idx_user_id (user_id)
);

-- Complaints table
CREATE TABLE IF NOT EXISTS complaints (
    complaint_id INT PRIMARY KEY AUTO_INCREMENT,
    citizen_id INT NOT NULL,
    title VARCHAR(200) NOT NULL,
    description LONGTEXT NOT NULL,
    category VARCHAR(100),
    location VARCHAR(255),
    complaint_date DATE NOT NULL,
    evidence_count INT DEFAULT 0,
    status ENUM('pending', 'approved', 'rejected', 'under_investigation', 'completed') DEFAULT 'pending',
    assigned_officer_id INT,
    approval_date TIMESTAMP,
    rejection_reason TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (citizen_id) REFERENCES users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (assigned_officer_id) REFERENCES officers(officer_id) ON DELETE SET NULL,
    INDEX idx_citizen_id (citizen_id),
    INDEX idx_status (status),
    INDEX idx_assigned_officer_id (assigned_officer_id)
);

-- Evidence table (images, documents uploaded by citizens)
CREATE TABLE IF NOT EXISTS evidence (
    evidence_id INT PRIMARY KEY AUTO_INCREMENT,
    complaint_id INT NOT NULL,
    file_name VARCHAR(255) NOT NULL,
    file_type VARCHAR(50),
    file_size INT,
    file_path VARCHAR(255) NOT NULL,
    uploaded_by INT NOT NULL,
    uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (complaint_id) REFERENCES complaints(complaint_id) ON DELETE CASCADE,
    FOREIGN KEY (uploaded_by) REFERENCES users(user_id),
    INDEX idx_complaint_id (complaint_id)
);

-- Reports table (investigation reports by officers)
CREATE TABLE IF NOT EXISTS reports (
    report_id INT PRIMARY KEY AUTO_INCREMENT,
    complaint_id INT NOT NULL UNIQUE,
    officer_id INT NOT NULL,
    findings TEXT NOT NULL,
    recommendations TEXT,
    status ENUM('draft', 'submitted', 'approved') DEFAULT 'draft',
    submitted_date TIMESTAMP,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (complaint_id) REFERENCES complaints(complaint_id) ON DELETE CASCADE,
    FOREIGN KEY (officer_id) REFERENCES officers(officer_id),
    INDEX idx_complaint_id (complaint_id),
    INDEX idx_officer_id (officer_id)
);

-- Audit log table
CREATE TABLE IF NOT EXISTS audit_logs (
    log_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    action VARCHAR(100) NOT NULL,
    table_name VARCHAR(100),
    record_id INT,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE SET NULL,
    INDEX idx_user_id (user_id),
    INDEX idx_created_at (created_at)
);

-- Insert sample admin user (password: admin123)
INSERT INTO users (email, password, first_name, last_name, user_type, is_active) VALUES
('admin@corruption.com', '$2y$10$YourHashedPasswordHere', 'System', 'Admin', 'admin', TRUE);

-- Insert sample officer user (password: officer123)
INSERT INTO users (email, password, first_name, last_name, user_type, is_active) VALUES
('officer@corruption.com', '$2y$10$YourHashedPasswordHere', 'John', 'Officer', 'officer', TRUE);

-- Link officer to officers table
INSERT INTO officers (user_id, department, badge_number, designation, is_available) VALUES
(2, 'Investigation', 'OFF001', 'Senior Investigator', TRUE);
