
<?php
session_start();

$conn = new mysqli("localhost", "root", "", "studenthub");

if ($conn->connect_error) {
    http_response_code(500);
    exit("Database connection failed.");
}

$username = trim($_POST["username"] ?? "");
$password = $_POST["password"] ?? "";

if ($username === "" || $password === "") {
    header("Location: student.html?error=empty");
    exit;
}

$sql = "SELECT user_id, username, password, role
        FROM users
        WHERE username = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();

$result = $stmt->get_result();
$user = $result->fetch_assoc();

if ($user && password_verify($password, $user["password"])) {

    // Generate a new session ID after login
    session_regenerate_id(true);

    $_SESSION["user_id"] = $user["user_id"];
    $_SESSION["username"] = $user["username"];
    $_SESSION["role"] = $user["role"];
    $_SESSION["last_activity"] = time();

    $stmt->close();
    $conn->close();

    // Role-based redirection
    if ($user["role"] === "admin") {
        header("Location: admin.php");
    } else {
        header("Location: dashboard.php");
    }
    exit;

} else {
    $stmt->close();
    $conn->close();

    header("Location: student.html?error=invalid");
    exit;
}
?>
