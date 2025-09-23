-- Pwesto Database Setup

-- Create database
CREATE DATABASE IF NOT EXISTS pwesto_db;
USE pwesto_db;

-- Drop existing tables if they exist (for clean setup)
DROP TABLE IF EXISTS disputes;
DROP TABLE IF EXISTS reviews;
DROP TABLE IF EXISTS floor_plans;
DROP TABLE IF EXISTS bookings;
DROP TABLE IF EXISTS sessions;
DROP TABLE IF EXISTS password_reset_tokens;
DROP TABLE IF EXISTS users;
DROP TABLE IF EXISTS cache;
DROP TABLE IF EXISTS jobs; 

-- Create users table
CREATE TABLE users (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    email_verified_at TIMESTAMP NULL,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(255) NULL,
    profile_image VARCHAR(255) NULL,
    role VARCHAR(255) DEFAULT 'user',
    status VARCHAR(255) DEFAULT 'approved',
    company VARCHAR(255) NULL,
    company_id VARCHAR(255) NULL,
    company_name VARCHAR(255) NULL,
    company_address TEXT NULL,
    company_phone VARCHAR(255) NULL,
    remember_token VARCHAR(100) NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
);

-- Create password reset tokens table
CREATE TABLE password_reset_tokens (
    email VARCHAR(255) PRIMARY KEY,
    token VARCHAR(255) NOT NULL,
    created_at TIMESTAMP NULL
);

-- Create sessions table
CREATE TABLE sessions (
    id VARCHAR(255) PRIMARY KEY,
    user_id INT UNSIGNED NULL,
    ip_address VARCHAR(45) NULL,
    user_agent TEXT NULL,
    payload LONGTEXT NOT NULL,
    last_activity INT NOT NULL,
    INDEX sessions_user_id_index (user_id),
    INDEX sessions_last_activity_index (last_activity)
);

-- Create cache table
CREATE TABLE cache (
    `key` VARCHAR(255) PRIMARY KEY,
    value MEDIUMTEXT NOT NULL,
    expiration INT NOT NULL
);
-- Create jobs table
CREATE TABLE jobs (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    queue VARCHAR(255) NOT NULL,
    payload LONGTEXT NOT NULL,
    attempts TINYINT UNSIGNED NOT NULL,
    reserved_at INT UNSIGNED NULL,
    available_at INT UNSIGNED NOT NULL,
    created_at INT UNSIGNED NOT NULL,
    INDEX jobs_queue_index (queue)
);

-- Create bookings table
CREATE TABLE bookings (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    hub_owner_id INT UNSIGNED NOT NULL,
    hub_name VARCHAR(255) NOT NULL,
    booking_date DATE NOT NULL,
    start_time TIME NOT NULL,
    end_time TIME NOT NULL,
    booking_time TIME NOT NULL,
    service_type VARCHAR(255) NOT NULL,
    seat_label VARCHAR(255) NOT NULL,
    seat_id VARCHAR(255) NOT NULL,
    status ENUM('pending', 'confirmed', 'cancelled', 'completed', 'rejected') DEFAULT 'pending',
    amount DECIMAL(10,2) NOT NULL,
    notes TEXT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (hub_owner_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Create floor_plans table
CREATE TABLE floor_plans (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    hub_owner_id INT UNSIGNED NOT NULL,
    name VARCHAR(255) DEFAULT 'My Floor Plan',
    layout_data JSON NOT NULL,
    description TEXT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (hub_owner_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Create reviews table
CREATE TABLE reviews (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    hub_owner_id INT UNSIGNED NOT NULL,
    rating INT DEFAULT 0,
    comment TEXT NOT NULL,
    status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    approved_at TIMESTAMP NULL,
    rejected_at TIMESTAMP NULL,
    approved_by INT UNSIGNED NULL,
    rejected_by INT UNSIGNED NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (hub_owner_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (approved_by) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (rejected_by) REFERENCES users(id) ON DELETE SET NULL
);

-- Create disputes table
CREATE TABLE disputes (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    hub_owner_id INT UNSIGNED NOT NULL,
    booking_id INT UNSIGNED NOT NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    status ENUM('open', 'in_progress', 'resolved', 'closed') DEFAULT 'open',
    resolution TEXT NULL,
    resolved_at TIMESTAMP NULL,
    resolved_by INT UNSIGNED NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (hub_owner_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (booking_id) REFERENCES bookings(id) ON DELETE CASCADE,
    FOREIGN KEY (resolved_by) REFERENCES users(id) ON DELETE SET NULL
);

-- Insert sample admin user
INSERT INTO users (name, email, password, role, status, created_at, updated_at) VALUES
('Admin User', 'admin@pwesto.com', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 'approved', NOW(), NOW()),
('Carl Admin', 'carl@gmail.com', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 'approved', NOW(), NOW()),
('Hub Owner', 'hubowner@pwesto.com', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'hub_owner', 'approved', NOW(), NOW()),
('Test User', 'user@pwesto.com', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'user', 'approved', NOW(), NOW());

-- Insert sample floor plan data
INSERT INTO floor_plans (hub_owner_id, name, layout_data, description, created_at, updated_at) VALUES
(3, 'Main Office Floor Plan', '{"items":[{"id":1,"shape":"sofa","x":50,"y":50,"width":120,"height":60,"label":"Sofa","color":"#FBBF24"},{"id":2,"shape":"desk","x":200,"y":50,"width":80,"height":60,"label":"Desk","color":"#8B4513"},{"id":3,"shape":"chair","x":100,"y":150,"width":40,"height":40,"label":"Chair","color":"#9CA3AF"}]}', 'Main office layout with various workspaces', NOW(), NOW());

-- Insert sample bookings
INSERT INTO bookings (user_id, hub_owner_id, hub_name, booking_date, start_time, end_time, booking_time, service_type, seat_label, seat_id, status, amount, created_at, updated_at) VALUES
(4, 3, 'Pwesto Main Office', '2025-09-15', '09:00:00', '17:00:00', '09:00:00', 'hot-desk', 'Chair 1', 'chair_1', 'confirmed', 25.00, NOW(), NOW()),
(4, 3, 'Pwesto Main Office', '2025-09-16', '10:00:00', '18:00:00', '10:00:00', 'private-office', 'Office A', 'office_a', 'pending', 50.00, NOW(), NOW());

-- Show success message
SELECT 'Pwesto database setup completed successfully!' as message;

CREATE TABLE IF NOT EXISTS migrations (
    id int(10) unsigned NOT NULL AUTO_INCREMENT,
    migration varchar(255) NOT NULL,
    batch int(11) NOT NULL,
    PRIMARY KEY (id)
);

INSERT INTO migrations (migration, batch) VALUES 
('0001_01_01_000000_create_users_table', 1),
('0001_01_01_000001_create_cache_table', 1),
('0001_01_01_000002_create_jobs_table', 1),
('2025_07_23_081642_add_phone_to_users_table', 1),
('2025_07_28_000001_add_profile_image_to_users_table', 1),
('2025_07_29_071808_add_role_to_users_table', 1),
('2025_07_29_072928_add_status_to_users_table', 1),
('2025_07_29_073500_add_company_fields_to_users_table', 1),
('2025_07_31_075944_create_bookings_table', 1),
('2025_08_03_065229_create_floor_plans_table', 1),
('2025_08_07_163815_create_reviews_table', 1),
('2025_08_07_163834_create_disputes_table', 1),
('2025_08_28_073431_add_seat_info_to_bookings_table', 1),
('2025_08_29_071326_add_booking_fields_to_bookings_table', 1),
('2025_09_02_091117_add_seat_booking_fields_to_bookings_table', 1),
('2025_09_02_094003_add_missing_fields_to_bookings_table', 1),
('2025_09_02_094027_add_missing_fields_to_bookings_table', 1),
('2025_09_02_094225_add_rejected_status_to_bookings_table', 1);

UPDATE users SET password = '$2y$10$mbMvKKc2IJpsxlx86mK.4ehZ0zjJghTstcj5RjHm1SmfaXuztWVM.' 
WHERE email = 'admin@pwesto.com';

UPDATE users SET password = '$2y$10$mbMvKKc2IJpsxlx86mK.4ehZ0zjJghTstcj5RjHm1SmfaXuztWVM.' 
WHERE email = 'carl@gmail.com';