
<?php
require "auth.php";

if ($_SESSION["role"] !== "admin") {
    header("Location: dashboard.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StudentHub Admin</title>
</head>
<body>
    <h1>Admin Dashboard</h1>

    <p>
        Welcome,
        <?php echo htmlspecialchars($_SESSION["username"]); ?>!
    </p>

    <p>You have admin access.</p>

    <a href="logout.php">Logout</a>
</body>
</html>
