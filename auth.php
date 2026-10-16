
<?php
session_start();

// User must be logged in
if (!isset($_SESSION["user_id"])) {
    header("Location: student.html");
    exit;
}

// Session timeout: 15 minutes
$timeout = 900;

if (
    !isset($_SESSION["last_activity"]) ||
    time() - $_SESSION["last_activity"] > $timeout
) {
    $_SESSION = [];
    session_destroy();

    header("Location: student.html?timeout=1");
    exit;
}

// Update last activity time
$_SESSION["last_activity"] = time();
?>
