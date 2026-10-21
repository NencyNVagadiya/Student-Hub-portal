-- ========================================================
-- WDF Practical 14: Admin Dashboard, RBAC & Audit Log
-- Database Setup Script (Database: event_management)
-- ========================================================

USE `event_management`;

-- 1. Create 'users' table
CREATE TABLE IF NOT EXISTS `users` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `username` VARCHAR(50) NOT NULL UNIQUE,
    `email` VARCHAR(100) NOT NULL,
    `password` VARCHAR(255) NOT NULL,
    `role` ENUM('admin', 'student') NOT NULL DEFAULT 'student',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 2. Create 'registrations' table
CREATE TABLE IF NOT EXISTS `registrations` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `student_id` INT NULL,
    `event_id` INT NULL,
    `student_name` VARCHAR(100) NOT NULL,
    `event_name` VARCHAR(150) NOT NULL,
    `registered_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 3. Create 'audit_logs' table
CREATE TABLE IF NOT EXISTS `audit_logs` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT NULL,
    `username` VARCHAR(50) NOT NULL,
    `role` VARCHAR(20) NOT NULL,
    `action` VARCHAR(255) NOT NULL,
    `ip_address` VARCHAR(45) DEFAULT '127.0.0.1',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 4. Insert Demo Users (admin123 and student123)
-- Passwords are generated using PHP password_hash(..., PASSWORD_DEFAULT)
INSERT INTO `users` (`username`, `email`, `password`, `role`) VALUES
('admin', 'admin@studenthub.local', '$2y$10$O9BA45n39DsmtrucXoif0e2naIH1iLMdlbtNYFBqVfKiaBcW9TmP2', 'admin'),
('student', 'student@studenthub.local', '$2y$10$uvnGccWIzNQOIldvNWAZeOk6ZWz1W.C5IvnoNyMO7OYAgL1IfxPpO', 'student')
ON DUPLICATE KEY UPDATE `role` = VALUES(`role`);

-- 5. Insert Sample Registrations for Dashboard Statistics
INSERT INTO `registrations` (`student_id`, `event_id`, `student_name`, `event_name`, `registered_at`) VALUES
(1, 1, 'Nency Vagadiya', 'TechNova 2026 - Annual Tech Fest', '2026-10-21 14:30:00'),
(2, 2, 'Rahul Sharma', 'AI & Machine Learning Workshop', '2026-10-21 16:15:00'),
(3, 1, 'Priya Patel', 'TechNova 2026 - Annual Tech Fest', '2026-10-21 18:45:00')
ON DUPLICATE KEY UPDATE `id` = `id`;

-- 6. Insert Initial Audit Log Entry
INSERT INTO `audit_logs` (`user_id`, `username`, `role`, `action`, `ip_address`) VALUES
(1, 'admin', 'admin', 'System initialization: Database setup and tables configured', '127.0.0.1');
