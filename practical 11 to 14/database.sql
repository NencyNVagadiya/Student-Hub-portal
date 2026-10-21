-- ========================================================
-- WDF Practical 12 & Practical 13: Database Setup Script
-- Database: event_management
-- ========================================================

-- 1. Create the database if it doesn't already exist
CREATE DATABASE IF NOT EXISTS `event_management`
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

-- 2. Select the database
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

-- Sample events for Practical 12
INSERT INTO `events` (`event_name`, `event_date`, `venue`, `description`, `poster`) VALUES
('TechNova 2026 - Annual Tech Fest', '2026-11-15', 'Main Auditorium, Campus Block A', 'Join us for coding competitions, robotics showdowns, and guest tech talks from industry leaders.', 'sample_poster1.png'),
('AI & Machine Learning Workshop', '2026-11-28', 'Computer Lab 3, IT Building', 'A hands-on introductory workshop on Neural Networks, Python tools, and predictive modeling for engineering students.', 'sample_poster2.png');

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

-- Sample students for Practical 13
INSERT INTO `students` (`name`, `email`, `course`, `semester`) VALUES
('Nency Vagadiya', 'nencyvagadiya09@gmail.com', 'Computer Engineering', 3),
('Rahul Sharma', 'rahul.sharma@example.com', 'Information Technology', 4),
('Priya Patel', 'priya.patel@example.com', 'Computer Engineering', 5);
