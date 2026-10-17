
<?php
session_start();

// Connect to database
$conn = new mysqli("localhost", "root", "", "studenthub");

if ($conn->connect_error) {
    http_response_code(500);
    exit("Database connection failed.");
}

// Get login details
$username = trim($_POST["username"] ?? "");
$password = $_POST["password"] ?? "";

// Check empty fields
if ($username === "" || $password === "") {
    header("Location: student.html?error=empty");
    exit;
}

// Find user in database
$sql = "SELECT user_id, username, password, role
        FROM users
        WHERE username = ?";

$stmt = $conn->prepare($sql);

if (!$stmt) {
    http_response_code(500);
    exit("Database query failed.");
}

$stmt->bind_param("s", $username);
$stmt->execute();

$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Verify password
if ($user && password_verify($password, $user["password"])) {

    // Create a new session ID after login
    session_regenerate_id(true);

    // Store user information in session
    $_SESSION["user_id"] = $user["user_id"];
    $_SESSION["username"] = $user["username"];
    $_SESSION["role"] = $user["role"];
    $_SESSION["last_activity"] = time();

    $role = $user["role"];

    $stmt->close();
    $conn->close();
   // Redirect after successful login
header("Location: Home.html");
exit;

} else {

    $stmt->close();
    $conn->close();

    // Incorrect username or password
    header("Location: student.html?error=invalid");
    exit;
}
?>
