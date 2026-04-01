-- ISP Billing Engine Database Schema

CREATE DATABASE IF NOT EXISTS isp_billing;
USE isp_billing;

CREATE TABLE tbl_users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    fullname VARCHAR(100) NOT NULL,
    phone VARCHAR(20),
    email VARCHAR(100),
    user_type ENUM('SuperAdmin', 'Admin', 'Agent', 'Sales') NOT NULL,
    root INT DEFAULT NULL,
    status ENUM('active', 'inactive') DEFAULT 'active',
    FOREIGN KEY (root) REFERENCES tbl_users(id) ON DELETE SET NULL
);

CREATE TABLE tbl_plans (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name_plan VARCHAR(100) NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    type ENUM('prepaid', 'postpaid') NOT NULL,
    is_radius BOOLEAN DEFAULT FALSE,
    enabled BOOLEAN DEFAULT TRUE
);

CREATE TABLE tbl_user_recharges (
    id INT AUTO_INCREMENT PRIMARY KEY,
    customer_id INT NOT NULL,
    plan_id INT NOT NULL,
    status ENUM('active', 'inactive', 'expired') DEFAULT 'active',
    recharged_on DATE NOT NULL,
    recharged_time TIME NOT NULL,
    expiration DATE,
    time TIME,
    FOREIGN KEY (customer_id) REFERENCES tbl_users(id) ON DELETE CASCADE,
    FOREIGN KEY (plan_id) REFERENCES tbl_plans(id) ON DELETE CASCADE
);

CREATE TABLE tbl_voucher (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_plan INT NOT NULL,
    code VARCHAR(50) UNIQUE NOT NULL,
    status ENUM('active', 'used', 'expired') DEFAULT 'active',
    generated_by INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_plan) REFERENCES tbl_plans(id) ON DELETE CASCADE,
    FOREIGN KEY (generated_by) REFERENCES tbl_users(id) ON DELETE CASCADE
);

CREATE INDEX idx_users_username ON tbl_users(username);
CREATE INDEX idx_users_type ON tbl_users(user_type);
CREATE INDEX idx_users_root ON tbl_users(root);
CREATE INDEX idx_plans_enabled ON tbl_plans(enabled);
CREATE INDEX idx_recharges_customer ON tbl_user_recharges(customer_id);
CREATE INDEX idx_recharges_plan ON tbl_user_recharges(plan_id);
CREATE INDEX idx_voucher_code ON tbl_voucher(code);
