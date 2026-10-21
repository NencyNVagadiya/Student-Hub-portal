-- ========================================================
-- WDF Practicals 11 to 14: Master Database Setup Script
-- Database: event_management
-- ========================================================

-- 1. Create the database if it doesn't already exist
CREATE DATABASE IF NOT EXISTS `event_management`
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

USE `event_management`;

-- ========================================================
-- Table for Practical 12: Events (File Upload & Poster CRUD)
-- ========================================================
CREATE TABLE IF NOT EXISTS `events` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `event_name` VARCHAR(150) NOT NULL,
    `event_date` DATE NOT NULL,
    `venue` VARCHAR(150) NOT NULL,
    `description` TEXT NOT NULL,
    `poster` VARCHAR(255) NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `events` (`event_name`, `event_date`, `venue`, `description`, `poster`) VALUES
('TechNova 2026 - Annual Tech Fest', '2026-11-15', 'Main Auditorium, Campus Block A', 'Join us for coding competitions, robotics showdowns, and guest tech talks from industry leaders.', 'sample_poster1.png'),
('AI & Machine Learning Workshop', '2026-11-28', 'Computer Lab 3, IT Building', 'A hands-on introductory workshop on Neural Networks, Python tools, and predictive modeling for engineering students.', 'sample_poster2.png')
ON DUPLICATE KEY UPDATE `id` = `id`;

-- ========================================================
-- Table for Practical 13: Students (JSON API & Fetch CRUD)
-- ========================================================
CREATE TABLE IF NOT EXISTS `students` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(100) NOT NULL,
    `email` VARCHAR(100) NOT NULL,
    `course` VARCHAR(100) NOT NULL,
    `semester` INT NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `students` (`name`, `email`, `course`, `semester`) VALUES
('Nency Vagadiya', 'nencyvagadiya09@gmail.com', 'Computer Engineering', 3),
('Rahul Sharma', 'rahul.sharma@example.com', 'Information Technology', 4),
('Priya Patel', 'priya.patel@example.com', 'Computer Engineering', 5)
ON DUPLICATE KEY UPDATE `id` = `id`;

-- ========================================================
-- Tables for Practical 14: Users, Registrations & Audit Logs
-- ========================================================
CREATE TABLE IF NOT EXISTS `users` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `username` VARCHAR(50) NOT NULL UNIQUE,
    `email` VARCHAR(100) NOT NULL,
    `password` VARCHAR(255) NOT NULL,
    `role` ENUM('admin', 'student') NOT NULL DEFAULT 'student',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `registrations` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `student_id` INT NULL,
    `event_id` INT NULL,
    `student_name` VARCHAR(100) NOT NULL,
    `event_name` VARCHAR(150) NOT NULL,
    `registered_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `audit_logs` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT NULL,
    `username` VARCHAR(50) NOT NULL,
    `role` VARCHAR(20) NOT NULL,
    `action` VARCHAR(255) NOT NULL,
    `ip_address` VARCHAR(45) DEFAULT '127.0.0.1',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Demo credentials: admin / admin123, student / student123
INSERT INTO `users` (`username`, `email`, `password`, `role`) VALUES
('admin', 'admin@studenthub.local', '$2y$10$O9BA45n39DsmtrucXoif0e2naIH1iLMdlbtNYFBqVfKiaBcW9TmP2', 'admin'),
('student', 'student@studenthub.local', '$2y$10$uvnGccWIzNQOIldvNWAZeOk6ZWz1W.C5IvnoNyMO7OYAgL1IfxPpO', 'student')
ON DUPLICATE KEY UPDATE `role` = VALUES(`role`);

INSERT INTO `registrations` (`student_id`, `event_id`, `student_name`, `event_name`, `registered_at`) VALUES
(1, 1, 'Nency Vagadiya', 'TechNova 2026 - Annual Tech Fest', '2026-10-21 14:30:00'),
(2, 2, 'Rahul Sharma', 'AI & Machine Learning Workshop', '2026-10-21 16:15:00'),
(3, 1, 'Priya Patel', 'TechNova 2026 - Annual Tech Fest', '2026-10-21 18:45:00')
ON DUPLICATE KEY UPDATE `id` = `id`;

INSERT INTO `audit_logs` (`user_id`, `username`, `role`, `action`, `ip_address`) VALUES
(1, 'admin', 'admin', 'System initialization: Database setup and tables configured', '127.0.0.1')
ON DUPLICATE KEY UPDATE `id` = `id`;
