<?php

$conn = new mysqli("localhost", "root", "", "studenthub");

if ($conn->connect_error) {
    die("Database connection failed");
}

$username = trim($_POST["username"] ?? "");
$email = trim($_POST["email"] ?? "");
$password = $_POST["password"] ?? "";
$confirm_password = $_POST["confirm_password"] ?? "";

// Backend validation
if ($username == "" || $email == "" || $password == "" || $confirm_password == "") {
    die("Please fill all fields.");
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    die("Invalid email address.");
}

if ($password != $confirm_password) {
    die("Passwords do not match.");
}

// Check duplicate username/email
$sql = "SELECT user_id FROM users WHERE username = ? OR email = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $username, $email);
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows > 0) {
    die("Username or Email already exists.");
}

// Hash password
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// Insert user
$sql = "INSERT INTO users (username, email, password)
        VALUES (?, ?, ?)";

$stmt = $conn->prepare($sql);
$stmt->bind_param("sss", $username, $email, $hashed_password);

if ($stmt->execute()) {

    echo '
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <title>Registration Successful</title>
        <link rel="stylesheet" href="register-success.css">
    </head>

    <body>
        <div class="success-box">
            <div class="success-icon">✅</div>
            <h1>Registration Successful!</h1>
            <p>Your account has been created successfully.</p>
            <a href="student.html">Go to Login</a>
        </div>

    </body>
    </html>
    ';

} else {

    echo '
    <h2>❌ Registration Failed</h2>
    <p>Please try again.</p>
    ';
}

$stmt->close();
$conn->close();

?>