<?php
/**
 * practical14_menu.php - Dynamic Navigation Menu Based on User Role
 * Practical 14: Role-Based Access Control
 */

require_once "practical14_auth.php";

$currentPage = basename($_SERVER["PHP_SELF"]);
$username    = $_SESSION["username"] ?? "";
$role        = $_SESSION["role"] ?? "";
?>

<nav class="dynamic-navbar">
    <div class="nav-container">
        <!-- Logo / Brand -->
        <a href="<?= isAdmin() ? 'practical14_dashboard.php' : 'practical14_student.php' ?>" class="nav-brand">
            StudentHub Portal <span class="badge-role badge-<?= htmlspecialchars($role) ?>"><?= strtoupper(htmlspecialchars($role)) ?></span>
        </a>

        <!-- Dynamic Menu Links based on Role -->
        <ul class="nav-links">
            <?php if (isAdmin()): ?>
                <!-- ADMIN MENU -->
                <li>
                    <a href="practical14_dashboard.php" class="<?= $currentPage === 'practical14_dashboard.php' ? 'active' : '' ?>">
                        Dashboard
                    </a>
                </li>
                <li>
                    <a href="practical13.php">
                        Manage Students
                    </a>
                </li>
                <li>
                    <a href="index.php">
                        Manage Events
                    </a>
                </li>
                <li>
                    <a href="practical14_audit.php" class="<?= $currentPage === 'practical14_audit.php' ? 'active' : '' ?>">
                        Audit Log
                    </a>
                </li>

            <?php elseif (isStudent()): ?>
                <!-- STUDENT MENU -->
                <li>
                    <a href="practical14_student.php" class="<?= $currentPage === 'practical14_student.php' ? 'active' : '' ?>">
                        Student Home
                    </a>
                </li>
                <li>
                    <a href="index.php">
                        Browse Events
                    </a>
                </li>
            <?php endif; ?>

            <!-- USER INFO & LOGOUT -->
            <?php if (isLoggedIn()): ?>
                <li class="user-info">
                    <span>User: <strong><?= htmlspecialchars($username) ?></strong></span>
                </li>
                <li>
                    <a href="practical14_logout.php" class="btn-nav-logout">Logout</a>
                </li>
            <?php else: ?>
                <li>
                    <a href="practical14_login.php" class="btn-nav-login">Login</a>
                </li>
            <?php endif; ?>
        </ul>
    </div>
</nav>
