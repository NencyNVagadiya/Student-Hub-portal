<?php
/**
 * index.php - View All Events
 * Practical 12: Event Management System
 */

require_once "db.php";

// Fetch success or error messages from query string
$successMessage = trim($_GET['success'] ?? '');
$errorMessage   = trim($_GET['error'] ?? '');

// Fetch all events from database ordered by date
$sql = "SELECT * FROM events ORDER BY event_date ASC, id DESC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Management System - Practical 12</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="container">
    <!-- Top Navigation Bar between Practicals -->
    <div style="background: #0f172a; color: #fff; padding: 10px 16px; border-radius: 6px; margin-bottom: 20px; font-size: 13px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 8px;">
        <span><strong>WDF Practical Series:</strong> Practical 12 (Event Management CRUD)</span>
        <a href="practical13.php" style="color: #93c5fd; text-decoration: none; font-weight: 600;">Go to Practical 13 (Fetch API CRUD) &rarr;</a>
    </div>

    <!-- Header with Action Button -->
    <header class="header-box">
        <div>
            <h1>Event Management System</h1>
            <p>Admin Portal &bull; WDF Practical 12 Demo</p>
        </div>
        <a href="add.php" class="btn btn-primary">+ Add New Event</a>
    </header>

    <!-- Feedback Alerts -->
    <?php if ($successMessage !== ''): ?>
        <div class="alert alert-success">
            <span>&#10003;</span>
            <span><?= htmlspecialchars($successMessage) ?></span>
        </div>
    <?php endif; ?>

    <?php if ($errorMessage !== ''): ?>
        <div class="alert alert-error">
            <span>&#9888;</span>
            <span><?= htmlspecialchars($errorMessage) ?></span>
        </div>
    <?php endif; ?>

    <!-- Events Table Card -->
    <div class="table-card">
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th style="width: 50px;">#</th>
                        <th style="width: 80px;">Poster</th>
                        <th>Event Name</th>
                        <th style="width: 120px;">Date</th>
                        <th style="width: 160px;">Venue</th>
                        <th>Description</th>
                        <th style="width: 140px; text-align: center;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php if ($result && $result->num_rows > 0): ?>
                    <?php $count = 1; ?>
                    <?php while ($event = $result->fetch_assoc()): ?>
                        <?php
                            $posterFile = $event['poster'];
                            $posterPath = "uploads/" . $posterFile;
                            $hasPoster = (!empty($posterFile) && file_exists(__DIR__ . "/" . $posterPath));
                        ?>
                        <tr>
                            <td><?= $count++ ?></td>
                            <td>
                                <?php if ($hasPoster): ?>
                                    <a href="<?= htmlspecialchars($posterPath) ?>" target="_blank" title="Click to view full poster">
                                        <img src="<?= htmlspecialchars($posterPath) ?>" alt="Poster" class="poster-thumb">
                                    </a>
                                <?php else: ?>
                                    <span style="font-size: 11px; color: #94a3b8; font-style: italic;">No Poster</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <strong><?= htmlspecialchars($event['event_name']) ?></strong>
                            </td>
                            <td>
                                <?= htmlspecialchars(date("d M Y", strtotime($event['event_date']))) ?>
                            </td>
                            <td><?= htmlspecialchars($event['venue']) ?></td>
                            <td><?= nl2br(htmlspecialchars($event['description'])) ?></td>
                            <td style="text-align: center;">
                                <div class="action-links" style="justify-content: center;">
                                    <a href="edit.php?id=<?= (int)$event['id'] ?>" class="btn btn-edit">Edit</a>
                                    <a href="delete.php?id=<?= (int)$event['id'] ?>" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this event?');">Delete</a>
                                </div>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7">
                            <div class="empty-state">
                                <h3>No Events Found</h3>
                                <p>There are no events in the database right now.</p>
                                <br>
                                <a href="add.php" class="btn btn-primary">+ Add Your First Event</a>
                            </div>
                        </td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

</body>
</html>
