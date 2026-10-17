<?php
require "auth.php";

if ($_SESSION["role"] !== "student") {
    header("Location: admin.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StudentHub Dashboard</title>
</head>
<body>
    <h1>Welcome to StudentHub!</h1>

    <p>
        Hello,
        <?php echo htmlspecialchars($_SESSION["username"]); ?>!
    </p>

    <p>You are logged in as a student.</p>

    <a href="logout.php">Logout</a>
</body>
</html>
