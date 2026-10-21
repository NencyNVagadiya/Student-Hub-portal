
<?php
$host = "localhost";
$username = "root";
$password = "";
$database = "studenthub_demo";

// Connect PHP to MySQL using MySQLi
$conn = new mysqli(
    $host,
    $username,
    $password,
    $database
);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");

?>
