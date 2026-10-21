<?php
/**
 * practical14_logout.php - Session Termination & Audit Log
 * Practical 14: Authentication & Session Management
 */

require_once "practical14_auth.php";

// If user was logged in, log the logout action
if (isLoggedIn()) {
    $userId   = $_SESSION["user_id"] ?? null;
    $username = $_SESSION["username"] ?? "Unknown";
    $role     = $_SESSION["role"] ?? "user";

    logAudit($conn, $userId, $username, $role, "User logged out");
}

// Clear all session variables
$_SESSION = [];

// Destroy session cookie if exists
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}

// Destroy session completely
session_destroy();

// Redirect to login page with feedback message
header("Location: practical14_login.php?message=" . urlencode("You have been logged out successfully."));
exit;
?>
