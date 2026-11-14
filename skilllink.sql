-- SkillLink database schema
-- Import this file in phpMyAdmin, or run it in the SQL tab.

CREATE DATABASE IF NOT EXISTS skilllink
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE skilllink;

CREATE TABLE IF NOT EXISTS users (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    phone VARCHAR(15) NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('customer', 'provider', 'admin') NOT NULL DEFAULT 'customer',
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS provider_details (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL UNIQUE,
    category VARCHAR(100) NOT NULL,
    experience VARCHAR(30) NOT NULL,
    service_area VARCHAR(150) NOT NULL,
    bio TEXT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_provider_user
        FOREIGN KEY (user_id) REFERENCES users(id)
        ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE INDEX idx_provider_category ON provider_details(category);
CREATE INDEX idx_provider_area ON provider_details(service_area);
