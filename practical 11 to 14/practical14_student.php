<?php
/**
 * practical14_student.php - Student Home Portal
 * Practical 14: Role-Based Access Control (Student View)
 */

require_once "practical14_auth.php";

// Require login (accessible by any logged-in user, primarily students)
requireLogin();

$username     = $_SESSION["username"] ?? "Student";
$role         = $_SESSION["role"] ?? "student";
$errorMessage = trim($_GET["error"] ?? "");

// Fetch list of available upcoming events for student to view
$events = [];
$eventRes = $conn->query("SELECT id, event_name, event_date, venue FROM events ORDER BY event_date ASC");
if ($eventRes) {
    while ($row = $eventRes->fetch_assoc()) {
        $events[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Portal - Practical 14</title>
    <link rel="stylesheet" href="practical14.css">
</head>
<body>

<!-- Dynamic Navigation Menu (Shows only Student Home, Browse Events, Logout) -->
<?php include "practical14_menu.php"; ?>

<div class="container">

    <!-- Access Denied or Notification Alert -->
    <?php if ($errorMessage !== ""): ?>
        <div class="alert alert-error">
            <span>&#9888;</span>
            <span><?= htmlspecialchars($errorMessage) ?></span>
        </div>
    <?php endif; ?>

    <!-- Welcome Banner -->
    <header class="header-box">
        <h1>Welcome to Student Portal, <?= htmlspecialchars($username) ?>!</h1>
        <p>You are logged in with the <strong>Student</strong> role. You have access to student features and event schedules.</p>
    </header>

    <div class="card">
        <h2 class="card-title">Role-Based Access Control (RBAC) Notice</h2>
        <p style="margin-top: 8px; color: #475569; font-size: 14px;">
            As a student user, administrative tools (such as the <strong>Admin Dashboard</strong>, <strong>Student Management CRUD</strong>, and <strong>Audit Logs</strong>) are restricted and hidden from your navigation menu. Direct attempts to navigate to admin URLs will be intercepted by server-side authorization checks and logged into the audit system.
        </p>
    </div>

    <!-- Upcoming Events List -->
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">Available Campus Events</h2>
            <a href="index.php" class="btn btn-primary" style="font-size: 12px; padding: 5px 12px;">View Posters &rarr;</a>
        </div>

        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th style="width: 50px;">#</th>
                        <th>Event Name</th>
                        <th style="width: 140px;">Event Date</th>
                        <th>Venue</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (!empty($events)): ?>
                    <?php $count = 1; ?>
                    <?php foreach ($events as $event): ?>
                        <tr>
                            <td><?= $count++ ?></td>
                            <td><strong><?= htmlspecialchars($event["event_name"]) ?></strong></td>
                            <td><?= htmlspecialchars(date("d M Y", strtotime($event["event_date"]))) ?></td>
                            <td><?= htmlspecialchars($event["venue"]) ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4" style="text-align: center; color: #64748b; padding: 20px;">No events scheduled.</td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>

</body>
</html>
