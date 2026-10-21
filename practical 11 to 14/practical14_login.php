<?php
/**
 * practical14_login.php - User Login & Authentication
 * Practical 14: Role-Based Access Control
 */

require_once "practical14_auth.php";

// If already logged in, redirect to appropriate home
if (isLoggedIn()) {
    if (isAdmin()) {
        header("Location: practical14_dashboard.php");
        exit;
    } else {
        header("Location: practical14_student.php");
        exit;
    }
}

$errorMessage   = trim($_GET["error"] ?? "");
$successMessage = trim($_GET["message"] ?? "");
$username       = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST["username"] ?? "");
    $password = trim($_POST["password"] ?? "");

    if ($username === "" || $password === "") {
        $errorMessage = "Please enter both username and password.";
    } else {
        // Query user using prepared statement
        $stmt = $conn->prepare("SELECT id, username, password, role FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        $user   = $result->fetch_assoc();
        $stmt->close();

        // Verify password using PHP password_verify()
        if ($user && password_verify($password, $user["password"])) {
            // Prevent session fixation
            session_regenerate_id(true);

            // Store user identity and role in session
            $_SESSION["user_id"]  = (int)$user["id"];
            $_SESSION["username"] = $user["username"];
            $_SESSION["role"]     = $user["role"];

            // Record successful login in audit log
            logAudit($conn, $user["id"], $user["username"], $user["role"], "User logged in successfully");

            // Redirect based on role
            if ($user["role"] === "admin") {
                header("Location: practical14_dashboard.php");
                exit;
            } else {
                header("Location: practical14_student.php");
                exit;
            }
        } else {
            // Log failed login attempt
            logAudit($conn, null, $username, "guest", "Failed login attempt: Invalid credentials");
            $errorMessage = "Invalid username or password.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Practical 14: RBAC & Dashboard</title>
    <link rel="stylesheet" href="practical14.css">
</head>
<body>

<div class="container">

    <!-- Top Navigation Bar between Practicals -->
    <div style="background: #0f172a; color: #fff; padding: 10px 16px; border-radius: 6px; margin-bottom: 24px; font-size: 13px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 8px;">
        <span><strong>WDF Practical 14:</strong> RBAC, Admin Dashboard & Audit Log</span>
        <div>
            <a href="index.php" style="color: #93c5fd; text-decoration: none; margin-right: 14px;">Practical 12 (Events) &rarr;</a>
            <a href="practical13.php" style="color: #93c5fd; text-decoration: none;">Practical 13 (Fetch CRUD) &rarr;</a>
        </div>
    </div>

    <!-- Login Box Card -->
    <div class="login-box">
        <h2 class="login-title">Portal Login</h2>
        <p class="login-subtitle">Sign in to access your role-based dashboard</p>

        <?php if ($errorMessage !== ""): ?>
            <div class="alert alert-error">
                <span>&#9888;</span>
                <span><?= htmlspecialchars($errorMessage) ?></span>
            </div>
        <?php endif; ?>

        <?php if ($successMessage !== ""): ?>
            <div class="alert alert-success">
                <span>&#10003;</span>
                <span><?= htmlspecialchars($successMessage) ?></span>
            </div>
        <?php endif; ?>

        <form method="POST" action="practical14_login.php">
            <div class="form-group">
                <label for="username">Username</label>
                <input
                    type="text"
                    id="username"
                    name="username"
                    class="form-control"
                    placeholder="Enter username"
                    value="<?= htmlspecialchars($username) ?>"
                    required
                    autofocus
                >
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input
                    type="password"
                    id="password"
                    name="password"
                    class="form-control"
                    placeholder="Enter password"
                    required
                >
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 6px;">Sign In</button>
        </form>

        <!-- Demo Credentials Helper for Evaluation -->
        <div class="demo-credentials">
            <strong>Demo Credentials for Evaluation:</strong>
            <p style="margin-top: 6px;">
                &bull; <strong>Admin:</strong> <code>admin</code> / <code>admin123</code><br>
                &bull; <strong>Student:</strong> <code>student</code> / <code>student123</code>
            </p>
        </div>
    </div>

</div>

</body>
</html>
