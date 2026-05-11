-- =============================================================================
-- Pwesto — full manual database setup (MySQL 8+)
-- Aligns with Laravel migrations in database/migrations (including
-- transaction_number on bookings). Use utf8mb4.
-- =============================================================================

CREATE DATABASE IF NOT EXISTS pwesto_db
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;
USE pwesto_db;

SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS notifications;
DROP TABLE IF EXISTS disputes;
DROP TABLE IF EXISTS reviews;
DROP TABLE IF EXISTS floor_plans;
DROP TABLE IF EXISTS bookings;
DROP TABLE IF EXISTS failed_jobs;
DROP TABLE IF EXISTS job_batches;
DROP TABLE IF EXISTS jobs;
DROP TABLE IF EXISTS cache_locks;
DROP TABLE IF EXISTS cache;
DROP TABLE IF EXISTS sessions;
DROP TABLE IF EXISTS password_reset_tokens;
DROP TABLE IF EXISTS users;
DROP TABLE IF EXISTS migrations;

SET FOREIGN_KEY_CHECKS = 1;

-- -----------------------------------------------------------------------------
-- users (0001_01_01_000000 + phone, role, status, company fields)
-- -----------------------------------------------------------------------------
CREATE TABLE users (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    email_verified_at TIMESTAMP NULL DEFAULT NULL,
    password VARCHAR(255) NOT NULL,
    profile_image VARCHAR(255) NULL DEFAULT NULL,
    remember_token VARCHAR(100) NULL DEFAULT NULL,
    created_at TIMESTAMP NULL DEFAULT NULL,
    updated_at TIMESTAMP NULL DEFAULT NULL,
    phone VARCHAR(255) NULL DEFAULT NULL,
    company VARCHAR(255) NULL DEFAULT NULL,
    company_id VARCHAR(255) NULL DEFAULT NULL,
    role VARCHAR(255) NOT NULL DEFAULT 'user',
    status VARCHAR(255) NOT NULL DEFAULT 'approved',
    PRIMARY KEY (id),
    UNIQUE KEY users_email_unique (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- password_reset_tokens, sessions
-- -----------------------------------------------------------------------------
CREATE TABLE password_reset_tokens (
    email VARCHAR(255) NOT NULL,
    token VARCHAR(255) NOT NULL,
    created_at TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE sessions (
    id VARCHAR(255) NOT NULL,
    user_id BIGINT UNSIGNED NULL DEFAULT NULL,
    ip_address VARCHAR(45) NULL DEFAULT NULL,
    user_agent TEXT NULL,
    payload LONGTEXT NOT NULL,
    last_activity INT NOT NULL,
    PRIMARY KEY (id),
    KEY sessions_user_id_index (user_id),
    KEY sessions_last_activity_index (last_activity)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- cache, cache_locks (0001_01_01_000001)
-- -----------------------------------------------------------------------------
CREATE TABLE cache (
    `key` VARCHAR(255) NOT NULL,
    value MEDIUMTEXT NOT NULL,
    expiration INT NOT NULL,
    PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE cache_locks (
    `key` VARCHAR(255) NOT NULL,
    owner VARCHAR(255) NOT NULL,
    expiration INT NOT NULL,
    PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- jobs, job_batches, failed_jobs (0001_01_01_000002)
-- -----------------------------------------------------------------------------
CREATE TABLE jobs (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    queue VARCHAR(255) NOT NULL,
    payload LONGTEXT NOT NULL,
    attempts TINYINT UNSIGNED NOT NULL,
    reserved_at INT UNSIGNED NULL DEFAULT NULL,
    available_at INT UNSIGNED NOT NULL,
    created_at INT UNSIGNED NOT NULL,
    PRIMARY KEY (id),
    KEY jobs_queue_index (queue)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE job_batches (
    id VARCHAR(255) NOT NULL,
    name VARCHAR(255) NOT NULL,
    total_jobs INT NOT NULL,
    pending_jobs INT NOT NULL,
    failed_jobs INT NOT NULL,
    failed_job_ids LONGTEXT NOT NULL,
    options MEDIUMTEXT NULL,
    cancelled_at INT NULL DEFAULT NULL,
    created_at INT NOT NULL,
    finished_at INT NULL DEFAULT NULL,
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE failed_jobs (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    uuid VARCHAR(255) NOT NULL,
    connection TEXT NOT NULL,
    queue TEXT NOT NULL,
    payload LONGTEXT NOT NULL,
    exception LONGTEXT NOT NULL,
    failed_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY failed_jobs_uuid_unique (uuid)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- bookings (core + seat fields + rejected + transaction_number)
-- -----------------------------------------------------------------------------
CREATE TABLE bookings (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    user_id BIGINT UNSIGNED NOT NULL,
    hub_owner_id BIGINT UNSIGNED NOT NULL,
    hub_name VARCHAR(255) NOT NULL,
    service_type VARCHAR(255) NULL DEFAULT NULL,
    seat_id VARCHAR(255) NULL DEFAULT NULL,
    seat_label VARCHAR(255) NULL DEFAULT NULL,
    booking_date DATE NOT NULL,
    booking_time TIME NULL DEFAULT NULL,
    start_time TIME NOT NULL,
    end_time TIME NOT NULL,
    status ENUM('pending','confirmed','cancelled','completed','rejected') NOT NULL DEFAULT 'pending',
    amount DECIMAL(10,2) NOT NULL,
    transaction_number VARCHAR(64) NULL DEFAULT NULL,
    notes TEXT NULL,
    created_at TIMESTAMP NULL DEFAULT NULL,
    updated_at TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY bookings_transaction_number_unique (transaction_number),
    KEY bookings_user_id_foreign (user_id),
    KEY bookings_hub_owner_id_foreign (hub_owner_id),
    CONSTRAINT bookings_user_id_foreign FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE,
    CONSTRAINT bookings_hub_owner_id_foreign FOREIGN KEY (hub_owner_id) REFERENCES users (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- floor_plans
-- -----------------------------------------------------------------------------
CREATE TABLE floor_plans (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    hub_owner_id BIGINT UNSIGNED NOT NULL,
    name VARCHAR(255) NOT NULL DEFAULT 'My Floor Plan',
    layout_data JSON NOT NULL,
    description TEXT NULL,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP NULL DEFAULT NULL,
    updated_at TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (id),
    KEY floor_plans_hub_owner_id_foreign (hub_owner_id),
    CONSTRAINT floor_plans_hub_owner_id_foreign FOREIGN KEY (hub_owner_id) REFERENCES users (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- reviews
-- -----------------------------------------------------------------------------
CREATE TABLE reviews (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    user_id BIGINT UNSIGNED NOT NULL,
    hub_owner_id BIGINT UNSIGNED NOT NULL,
    booking_id BIGINT UNSIGNED NULL DEFAULT NULL,
    rating INT NOT NULL DEFAULT 0,
    comment TEXT NOT NULL,
    feedback_type ENUM('workspace','platform') NOT NULL DEFAULT 'workspace',
    status ENUM('pending','approved','rejected') NOT NULL DEFAULT 'pending',
    priority INT NOT NULL DEFAULT 0,
    is_flagged TINYINT(1) NOT NULL DEFAULT 0,
    approved_at TIMESTAMP NULL DEFAULT NULL,
    rejected_at TIMESTAMP NULL DEFAULT NULL,
    approved_by BIGINT UNSIGNED NULL DEFAULT NULL,
    rejected_by BIGINT UNSIGNED NULL DEFAULT NULL,
    moderation_notes TEXT NULL,
    hub_owner_response TEXT NULL,
    hub_owner_responded_at TIMESTAMP NULL DEFAULT NULL,
    created_at TIMESTAMP NULL DEFAULT NULL,
    updated_at TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (id),
    KEY reviews_user_id_foreign (user_id),
    KEY reviews_hub_owner_id_foreign (hub_owner_id),
    KEY reviews_booking_id_foreign (booking_id),
    KEY reviews_approved_by_foreign (approved_by),
    KEY reviews_rejected_by_foreign (rejected_by),
    CONSTRAINT reviews_user_id_foreign FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE,
    CONSTRAINT reviews_hub_owner_id_foreign FOREIGN KEY (hub_owner_id) REFERENCES users (id) ON DELETE CASCADE,
    CONSTRAINT reviews_booking_id_foreign FOREIGN KEY (booking_id) REFERENCES bookings (id) ON DELETE SET NULL,
    CONSTRAINT reviews_approved_by_foreign FOREIGN KEY (approved_by) REFERENCES users (id) ON DELETE SET NULL,
    CONSTRAINT reviews_rejected_by_foreign FOREIGN KEY (rejected_by) REFERENCES users (id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- disputes (2025_08_07_163834)
-- -----------------------------------------------------------------------------
CREATE TABLE disputes (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    user_id BIGINT UNSIGNED NOT NULL,
    hub_owner_id BIGINT UNSIGNED NOT NULL,
    booking_id BIGINT UNSIGNED NULL DEFAULT NULL,
    type ENUM('payment','service','behavior','other') NOT NULL,
    description TEXT NOT NULL,
    evidence TEXT NULL,
    status ENUM('open','resolved','escalated') NOT NULL DEFAULT 'open',
    resolution TEXT NULL,
    resolved_by BIGINT UNSIGNED NULL DEFAULT NULL,
    resolved_at TIMESTAMP NULL DEFAULT NULL,
    escalated_at TIMESTAMP NULL DEFAULT NULL,
    created_by BIGINT UNSIGNED NOT NULL,
    created_at TIMESTAMP NULL DEFAULT NULL,
    updated_at TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (id),
    KEY disputes_user_id_index (user_id),
    KEY disputes_hub_owner_id_index (hub_owner_id),
    KEY disputes_booking_id_index (booking_id),
    KEY disputes_resolved_by_index (resolved_by),
    KEY disputes_created_by_index (created_by),
    CONSTRAINT disputes_user_id_foreign FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE,
    CONSTRAINT disputes_hub_owner_id_foreign FOREIGN KEY (hub_owner_id) REFERENCES users (id) ON DELETE CASCADE,
    CONSTRAINT disputes_booking_id_foreign FOREIGN KEY (booking_id) REFERENCES bookings (id) ON DELETE SET NULL,
    CONSTRAINT disputes_resolved_by_foreign FOREIGN KEY (resolved_by) REFERENCES users (id) ON DELETE SET NULL,
    CONSTRAINT disputes_created_by_foreign FOREIGN KEY (created_by) REFERENCES users (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- notifications (2026_05_03_120000)
-- -----------------------------------------------------------------------------
CREATE TABLE notifications (
    id CHAR(36) NOT NULL,
    type VARCHAR(255) NOT NULL,
    notifiable_type VARCHAR(255) NOT NULL,
    notifiable_id BIGINT UNSIGNED NOT NULL,
    data TEXT NOT NULL,
    read_at TIMESTAMP NULL DEFAULT NULL,
    created_at TIMESTAMP NULL DEFAULT NULL,
    updated_at TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (id),
    KEY notifications_notifiable_type_notifiable_id_index (notifiable_type, notifiable_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- Laravel migrations bookkeeping (so `php artisan migrate` skips already-built)
-- -----------------------------------------------------------------------------
CREATE TABLE migrations (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    migration VARCHAR(255) NOT NULL,
    batch INT NOT NULL,
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
('2025_09_02_094225_add_rejected_status_to_bookings_table', 1),
('2025_09_18_104803_add_company_fields_to_users_table_fix', 1),
('2025_12_06_064533_add_feedback_fields_to_reviews_table', 1),
('2025_12_06_070804_add_missing_feedback_fields_to_reviews_table', 1),
('2026_05_03_120000_create_notifications_table', 1),
('2026_05_07_032500_add_hub_owner_response_to_reviews_table', 1),
('2026_05_11_000001_add_transaction_number_to_bookings_table', 1);

-- -----------------------------------------------------------------------------
-- Sample data (optional — remove if you want an empty DB)
-- Password for all sample users below: "password" (Laravel default hash)
-- -----------------------------------------------------------------------------
INSERT INTO users (id, name, email, password, role, status, created_at, updated_at) VALUES
(1, 'Admin User', 'admin@pwesto.com', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 'approved', NOW(), NOW()),
(2, 'Carl Admin', 'carl@gmail.com', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 'approved', NOW(), NOW()),
(3, 'Hub Owner', 'hubowner@pwesto.com', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'hub_owner', 'approved', NOW(), NOW()),
(4, 'Test User', 'user@pwesto.com', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'user', 'approved', NOW(), NOW());

INSERT INTO floor_plans (id, hub_owner_id, name, layout_data, description, is_active, created_at, updated_at) VALUES
(1, 3, 'Main Office Floor Plan',
 '{"items":[{"id":1,"shape":"sofa","x":50,"y":50,"width":120,"height":60,"label":"Sofa","color":"#FBBF24"},{"id":2,"shape":"desk","x":200,"y":50,"width":80,"height":60,"label":"Desk","color":"#8B4513"},{"id":3,"shape":"chair","x":100,"y":150,"width":40,"height":40,"label":"Chair","color":"#9CA3AF"}]}',
 'Main office layout with various workspaces', 1, NOW(), NOW());

INSERT INTO bookings (
    id, user_id, hub_owner_id, hub_name, service_type, seat_id, seat_label,
    booking_date, booking_time, start_time, end_time, status, amount, transaction_number, notes, created_at, updated_at
) VALUES
(1, 4, 3, 'PWESTO Workspace', 'hot-desk', 'chair_1', 'Chair 1',
 '2026-09-15', '09:00:00', '09:00:00', '17:00:00', 'confirmed', 25.00, 'PWE-20260511-SAMPLE01', NULL, NOW(), NOW()),
(2, 4, 3, 'PWESTO Workspace', 'private-office', 'office_a', 'Office A',
 '2026-09-16', '10:00:00', '10:00:00', '18:00:00', 'pending', 50.00, NULL, NULL, NOW(), NOW());

-- Optional: match older seed passwords for admin accounts
UPDATE users SET password = '$2y$10$mbMvKKc2IJpsxlx86mK.4ehZ0zjJghTstcj5RjHm1SmfaXuztWVM.'
WHERE email IN ('admin@pwesto.com', 'carl@gmail.com');

ALTER TABLE users AUTO_INCREMENT = 5;
ALTER TABLE floor_plans AUTO_INCREMENT = 2;
ALTER TABLE bookings AUTO_INCREMENT = 3;

SELECT 'Pwesto database setup completed successfully!' AS message;
