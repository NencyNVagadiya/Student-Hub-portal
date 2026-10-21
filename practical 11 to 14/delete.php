<?php
/**
 * delete.php - Delete an Event and its Poster File
 * Practical 12: Event Management System
 */

require_once "db.php";

$id = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT);

// Validate ID
if (!$id || $id < 1) {
    header("Location: index.php?error=" . urlencode("Invalid event ID."));
    exit;
}

// Handle POST request (actual deletion confirmation)
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $confirmId = filter_input(INPUT_POST, "id", FILTER_VALIDATE_INT);

    if ($confirmId !== $id) {
        header("Location: index.php?error=" . urlencode("Invalid delete confirmation request."));
        exit;
    }

    // 1. Fetch poster filename so we can delete the file from uploads/
    $stmt = $conn->prepare("SELECT poster FROM events WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $event = $result->fetch_assoc();
    $stmt->close();

    if ($event) {
        $posterFilename = $event["poster"];

        // 2. Delete poster file from uploads/ if it exists
        if (!empty($posterFilename)) {
            $posterPath = __DIR__ . "/uploads/" . $posterFilename;
            if (file_exists($posterPath) && is_file($posterPath)) {
                unlink($posterPath);
            }
        }

        // 3. Delete event record from database
        $deleteStmt = $conn->prepare("DELETE FROM events WHERE id = ?");
        $deleteStmt->bind_param("i", $id);

        if ($deleteStmt->execute()) {
            $deleteStmt->close();
            header("Location: index.php?success=" . urlencode("Event and its poster were deleted successfully!"));
            exit;
        } else {
            $deleteStmt->close();
            header("Location: index.php?error=" . urlencode("Failed to delete the event from database."));
            exit;
        }
    } else {
        header("Location: index.php?error=" . urlencode("Event not found in the database."));
        exit;
    }
}

// Handle GET request: Show confirmation card with event info
$stmt = $conn->prepare("SELECT * FROM events WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$event = $result->fetch_assoc();
$stmt->close();

if (!$event) {
    header("Location: index.php?error=" . urlencode("Event not found."));
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirm Delete Event - Practical 12</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="container">
    <div class="confirm-box">
        <h2>Confirm Event Deletion</h2>
        <p>Are you sure you want to delete this event? This action cannot be undone, and the poster file will also be deleted from the server.</p>

        <div class="confirm-details">
            <p><strong>Event Name:</strong> <?= htmlspecialchars($event["event_name"]) ?></p>
            <p><strong>Date:</strong> <?= htmlspecialchars(date("d M Y", strtotime($event["event_date"]))) ?></p>
            <p><strong>Venue:</strong> <?= htmlspecialchars($event["venue"]) ?></p>
            <p><strong>Description:</strong> <?= htmlspecialchars($event["description"]) ?></p>
            <?php
                $posterPath = "uploads/" . $event["poster"];
                $hasPoster  = (!empty($event["poster"]) && file_exists(__DIR__ . "/" . $posterPath));
            ?>
            <?php if ($hasPoster): ?>
                <div style="margin-top: 10px;">
                    <p style="margin-bottom: 4px;"><strong>Poster:</strong></p>
                    <img src="<?= htmlspecialchars($posterPath) ?>" alt="Poster Thumbnail" style="width: 80px; height: 80px; object-fit: cover; border-radius: 6px; border: 1px solid #cbd5e1;">
                </div>
            <?php endif; ?>
        </div>

        <form method="POST" action="delete.php?id=<?= (int)$id ?>">
            <input type="hidden" name="id" value="<?= (int)$id ?>">
            <div class="form-actions" style="margin-top: 0; padding-top: 0; border-top: none;">
                <button type="submit" class="btn btn-danger">Yes, Delete Event</button>
                <a href="index.php" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>

</body>
</html>
