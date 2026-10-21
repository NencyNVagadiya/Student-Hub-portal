-- ========================================================
-- WDF Practical 12: Event Management System
-- Database Setup Script
-- ========================================================

-- 1. Create the database if it doesn't already exist
CREATE DATABASE IF NOT EXISTS `event_management`
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

-- 2. Select the database
USE `event_management`;

-- 3. Create the 'events' table
CREATE TABLE IF NOT EXISTS `events` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `event_name` VARCHAR(150) NOT NULL,
    `event_date` DATE NOT NULL,
    `venue` VARCHAR(150) NOT NULL,
    `description` TEXT NOT NULL,
    `poster` VARCHAR(255) NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 4. Insert initial sample events (optional demo data)
INSERT INTO `events` (`event_name`, `event_date`, `venue`, `description`, `poster`) VALUES
('TechNova 2026 - Annual Tech Fest', '2026-11-15', 'Main Auditorium, Campus Block A', 'Join us for coding competitions, robotics showdowns, and guest tech talks from industry leaders.', 'sample_poster1.png'),
('AI & Machine Learning Workshop', '2026-11-28', 'Computer Lab 3, IT Building', 'A hands-on introductory workshop on Neural Networks, Python tools, and predictive modeling for engineering students.', 'sample_poster2.png');
