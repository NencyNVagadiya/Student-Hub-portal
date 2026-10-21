<?php
/**
 * db.php - Database Connection Configuration
 * Practical 12: Event Management System
 */

// Database credentials
$host     = "localhost";
$username = "root";
$password = "";
$database = "event_management";

// Create MySQLi connection
$conn = new mysqli($host, $username, $password, $database);

// Check if connection succeeded
if ($conn->connect_error) {
    die("Database Connection Failed: " . $conn->connect_error);
}

// Set charset to utf8mb4 for unicode character support
$conn->set_charset("utf8mb4");
?>
