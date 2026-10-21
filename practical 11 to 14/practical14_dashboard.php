<?php
/**
 * practical14_dashboard.php - Admin Dashboard
 * Practical 14: Role-Based Access Control, Statistics & Audit Log
 */

require_once "practical14_auth.php";

// Strict Access Control: Only admins allowed
requireAdmin($conn);

$adminUsername = $_SESSION["username"] ?? "Admin";

// =============================================================
// 1. Fetch Summary Statistics
// =============================================================

// Total Users Count
$totalUsers = 0;
$userQuery = $conn->query("SELECT COUNT(*) AS total FROM users");
if ($userQuery) {
    $totalUsers = (int)$userQuery->fetch_assoc()["total"];
}

// Total Students Count
$totalStudents = 0;
$studentQuery = $conn->query("SELECT COUNT(*) AS total FROM students");
if ($studentQuery) {
    $totalStudents = (int)$studentQuery->fetch_assoc()["total"];
}

// Total Events Count
$totalEvents = 0;
$eventQuery = $conn->query("SELECT COUNT(*) AS total FROM events");
if ($eventQuery) {
    $totalEvents = (int)$eventQuery->fetch_assoc()["total"];
}

// Total Registrations Count
$totalRegistrations = 0;
$regQuery = $conn->query("SELECT COUNT(*) AS total FROM registrations");
if ($regQuery) {
    $totalRegistrations = (int)$regQuery->fetch_assoc()["total"];
}

// =============================================================
// 2. Fetch Latest Registrations
// =============================================================
$latestRegs = [];
$latestRegQuery = $conn->query(
    "SELECT id, student_name, event_name, registered_at FROM registrations ORDER BY registered_at DESC LIMIT 5"
);
if ($latestRegQuery) {
    while ($row = $latestRegQuery->fetch_assoc()) {
        $latestRegs[] = $row;
    }
}

// =============================================================
// 3. Fetch Recent Audit Logs Preview
// =============================================================
$recentLogs = [];
$auditQuery = $conn->query(
    "SELECT id, username, role, action, ip_address, created_at FROM audit_logs ORDER BY id DESC LIMIT 5"
);
if ($auditQuery) {
    while ($row = $auditQuery->fetch_assoc()) {
        $recentLogs[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Practical 14</title>
    <link rel="stylesheet" href="practical14.css">
</head>
<body>

<!-- Dynamic Navigation Menu -->
<?php include "practical14_menu.php"; ?>

<div class="container">

    <!-- Header Box -->
    <header class="header-box">
        <div>
            <h1>Admin Dashboard</h1>
            <p>Welcome back, <strong><?= htmlspecialchars($adminUsername) ?></strong> &bull; System Overview & Live Statistics</p>
        </div>
    </header>

    <!-- 4 Statistic Cards -->
    <div class="stat-grid">
        <!-- Card 1: Total Users -->
        <div class="stat-card card-blue">
            <div class="stat-info">
                <div class="stat-label">Total Users</div>
                <div class="stat-count"><?= $totalUsers ?></div>
            </div>
            <div class="stat-icon">&#128101;</div>
        </div>

        <!-- Card 2: Total Students -->
        <div class="stat-card card-green">
            <div class="stat-info">
                <div class="stat-label">Total Students</div>
                <div class="stat-count"><?= $totalStudents ?></div>
            </div>
            <div class="stat-icon">&#127891;</div>
        </div>

        <!-- Card 3: Total Events -->
        <div class="stat-card card-amber">
            <div class="stat-info">
                <div class="stat-label">Total Events</div>
                <div class="stat-count"><?= $totalEvents ?></div>
            </div>
            <div class="stat-icon">&#128197;</div>
        </div>

        <!-- Card 4: Total Registrations -->
        <div class="stat-card card-purple">
            <div class="stat-info">
                <div class="stat-label">Registrations</div>
                <div class="stat-count"><?= $totalRegistrations ?></div>
            </div>
            <div class="stat-icon">&#128221;</div>
        </div>
    </div>

    <!-- Latest Registrations Table Card -->
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">Latest Event Registrations</h2>
            <span style="font-size: 12px; color: #64748b;">Showing latest 5 entries</span>
        </div>

        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th style="width: 50px;">#</th>
                        <th>Student Name</th>
                        <th>Event Name</th>
                        <th style="width: 180px;">Registered Date & Time</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (!empty($latestRegs)): ?>
                    <?php $count = 1; ?>
                    <?php foreach ($latestRegs as $reg): ?>
                        <tr>
                            <td><?= $count++ ?></td>
                            <td><strong><?= htmlspecialchars($reg["student_name"]) ?></strong></td>
                            <td><?= htmlspecialchars($reg["event_name"]) ?></td>
                            <td><?= htmlspecialchars(date("d M Y, h:i A", strtotime($reg["registered_at"]))) ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4" style="text-align: center; color: #64748b; padding: 24px;">No registrations recorded yet.</td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Recent Audit Logs Table Card -->
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">Recent Audit Log Activities</h2>
            <a href="practical14_audit.php" class="btn btn-secondary" style="font-size: 12px; padding: 5px 12px;">View Full Audit Log &rarr;</a>
        </div>

        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th style="width: 50px;">#</th>
                        <th style="width: 160px;">Date & Time</th>
                        <th>User</th>
                        <th style="width: 100px;">Role</th>
                        <th>Action Performed</th>
                        <th style="width: 120px;">IP Address</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (!empty($recentLogs)): ?>
                    <?php foreach ($recentLogs as $log): ?>
                        <tr>
                            <td><?= (int)$log["id"] ?></td>
                            <td><?= htmlspecialchars(date("d M Y, h:i A", strtotime($log["created_at"]))) ?></td>
                            <td><strong><?= htmlspecialchars($log["username"]) ?></strong></td>
                            <td>
                                <span class="badge-role badge-<?= htmlspecialchars($log["role"]) ?>">
                                    <?= strtoupper(htmlspecialchars($log["role"])) ?>
                                </span>
                            </td>
                            <td><?= htmlspecialchars($log["action"]) ?></td>
                            <td><code><?= htmlspecialchars($log["ip_address"]) ?></code></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" style="text-align: center; color: #64748b; padding: 24px;">No audit log activities recorded yet.</td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>

</body>
</html>
