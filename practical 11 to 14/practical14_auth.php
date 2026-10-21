<?php
/**
 * practical14_auth.php - Session, Authentication & Authorization Helper
 * Practical 14: Role-Based Access Control and Audit Logging
 */

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once "db.php";

/**
 * Check if a user is currently logged in
 */
function isLoggedIn(): bool {
    return isset($_SESSION["user_id"]) && !empty($_SESSION["user_id"]);
}

/**
 * Check if the current user has the 'admin' role
 */
function isAdmin(): bool {
    return isLoggedIn() && isset($_SESSION["role"]) && $_SESSION["role"] === "admin";
}

/**
 * Check if the current user has the 'student' role
 */
function isStudent(): bool {
    return isLoggedIn() && isset($_SESSION["role"]) && $_SESSION["role"] === "student";
}

/**
 * Enforce authentication: Redirect to login if user is not logged in
 */
function requireLogin(): void {
    if (!isLoggedIn()) {
        header("Location: practical14_login.php?error=" . urlencode("Please log in to access this page."));
        exit;
    }
}

/**
 * Enforce Administrator role:
 * - If not logged in -> redirect to login page.
 * - If logged in as student -> log unauthorized attempt and redirect to student home with error.
 */
function requireAdmin($conn = null): void {
    requireLogin();

    if (!isAdmin()) {
        // Record unauthorized attempt in audit log if connection provided
        if ($conn) {
            $userId   = $_SESSION["user_id"] ?? null;
            $username = $_SESSION["username"] ?? "Unknown";
            $role     = $_SESSION["role"] ?? "student";
            logAudit($conn, $userId, $username, $role, "Access Denied: Unauthorized access to Admin Dashboard");
        }

        header("Location: practical14_student.php?error=" . urlencode("Access Denied: Administrator role required."));
        exit;
    }
}

/**
 * Helper to get the client IP address
 */
function getClientIp(): string {
    if (!empty($_SERVER["HTTP_CLIENT_IP"])) {
        return $_SERVER["HTTP_CLIENT_IP"];
    } elseif (!empty($_SERVER["HTTP_X_FORWARDED_FOR"])) {
        return explode(",", $_SERVER["HTTP_X_FORWARDED_FOR"])[0];
    }
    return $_SERVER["REMOTE_ADDR"] ?? "127.0.0.1";
}

/**
 * Record an action in the audit_logs table
 */
function logAudit($conn, $userId, $username, $role, $action): bool {
    if (!$conn || !($conn instanceof mysqli)) {
        return false;
    }

    $ip = getClientIp();

    $stmt = $conn->prepare("INSERT INTO audit_logs (user_id, username, role, action, ip_address) VALUES (?, ?, ?, ?, ?)");
    if (!$stmt) {
        return false;
    }

    $stmt->bind_param("issss", $userId, $username, $role, $action, $ip);
    $success = $stmt->execute();
    $stmt->close();

    return $success;
}
?>
