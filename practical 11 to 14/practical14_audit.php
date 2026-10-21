<?php
/**
 * practical14_audit.php - System Audit Log Viewer
 * Practical 14: Audit Logging & Activity Tracking
 */

require_once "practical14_auth.php";

// Only administrators can view the audit log
requireAdmin($conn);

// Fetch all audit logs, latest first
$logs = [];
$result = $conn->query("SELECT id, user_id, username, role, action, ip_address, created_at FROM audit_logs ORDER BY id DESC LIMIT 100");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $logs[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>System Audit Log - Practical 14</title>
    <link rel="stylesheet" href="practical14.css">
</head>
<body>

<!-- Dynamic Navigation Menu -->
<?php include "practical14_menu.php"; ?>

<div class="container">

    <header class="header-box">
        <h1>System Audit Log</h1>
        <p>Immutable record of authentication, access control events, and administrative activities.</p>
    </header>

    <div class="card">
        <div class="card-header">
            <h2 class="card-title">Activity Trail (Latest 100 Entries)</h2>
            <a href="practical14_dashboard.php" class="btn btn-secondary" style="font-size: 12px; padding: 5px 12px;">&larr; Back to Dashboard</a>
        </div>

        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th style="width: 50px;">Log ID</th>
                        <th style="width: 170px;">Timestamp</th>
                        <th>User</th>
                        <th style="width: 110px;">Role</th>
                        <th>Action Performed</th>
                        <th style="width: 130px;">IP Address</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (!empty($logs)): ?>
                    <?php foreach ($logs as $log): ?>
                        <tr>
                            <td>#<?= (int)$log["id"] ?></td>
                            <td><?= htmlspecialchars(date("d M Y, h:i:s A", strtotime($log["created_at"]))) ?></td>
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
                        <td colspan="6" style="text-align: center; color: #64748b; padding: 24px;">No audit activities recorded.</td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>

</body>
</html>
