<?php
/**
 * edit.php - Edit and Update Event Details
 * Practical 12: Event Management System
 */

require_once "db.php";

$id = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT);

// Check if valid ID is provided
if (!$id || $id < 1) {
    header("Location: index.php?error=" . urlencode("Invalid event ID specified."));
    exit;
}

// Fetch existing event from database
$stmt = $conn->prepare("SELECT * FROM events WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$event = $result->fetch_assoc();
$stmt->close();

if (!$event) {
    header("Location: index.php?error=" . urlencode("Event not found in the database."));
    exit;
}

$errorMessage = "";
$eventName    = $event["event_name"];
$eventDate    = $event["event_date"];
$venue        = $event["venue"];
$description  = $event["description"];
$currentPoster = $event["poster"];

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $eventName   = trim($_POST["event_name"] ?? "");
    $eventDate   = trim($_POST["event_date"] ?? "");
    $venue       = trim($_POST["venue"] ?? "");
    $description = trim($_POST["description"] ?? "");

    // 1. Validate required text fields
    if ($eventName === "" || $eventDate === "" || $venue === "" || $description === "") {
        $errorMessage = "All text fields (Event Name, Date, Venue, Description) are required.";
    } else {
        $posterToSave = $currentPoster; // Default: keep the old poster
        $uploadNewPoster = false;

        // 2. Check if a new poster file was chosen
        if (isset($_FILES["poster"]) && $_FILES["poster"]["error"] !== UPLOAD_ERR_NO_FILE) {
            $file = $_FILES["poster"];

            // A. Check upload error
            if ($file["error"] !== UPLOAD_ERR_OK) {
                if ($file["error"] === UPLOAD_ERR_INI_SIZE || $file["error"] === UPLOAD_ERR_FORM_SIZE) {
                    $errorMessage = "New poster file exceeds the 2 MB limit.";
                } else {
                    $errorMessage = "File upload failed with error code: " . $file["error"];
                }
            }
            // B. Check file size (<= 2 MB)
            elseif ($file["size"] > (2 * 1024 * 1024)) {
                $errorMessage = "New poster file size exceeds 2 MB. Please select a smaller file.";
            }
            // C. Check file extension (JPG, JPEG, PNG)
            else {
                $extension   = strtolower(pathinfo($file["name"], PATHINFO_EXTENSION));
                $allowedExts = ["jpg", "jpeg", "png"];

                if (!in_array($extension, $allowedExts, true)) {
                    $errorMessage = "Invalid file type. Only JPG, JPEG, and PNG images are allowed.";
                }
                // D. Validate MIME type
                else {
                    $allowedMimes = ["image/jpeg", "image/png"];
                    $finfo        = finfo_open(FILEINFO_MIME_TYPE);
                    $mimeType     = finfo_file($finfo, $file["tmp_name"]);
                    finfo_close($finfo);

                    if (!in_array($mimeType, $allowedMimes, true)) {
                        $errorMessage = "Invalid image content. Allowed MIME types: image/jpeg, image/png.";
                    }
                    // E. Check image integrity
                    elseif (@getimagesize($file["tmp_name"]) === false) {
                        $errorMessage = "The uploaded file is not a valid image.";
                    }
                    else {
                        // Generate safe unique filename
                        $newPosterName = "poster_" . time() . "_" . bin2hex(random_bytes(4)) . "." . $extension;
                        $uploadDirectory = __DIR__ . "/uploads/";
                        if (!is_dir($uploadDirectory)) {
                            mkdir($uploadDirectory, 0777, true);
                        }

                        $destination = $uploadDirectory . $newPosterName;

                        if (move_uploaded_file($file["tmp_name"], $destination)) {
                            $posterToSave = $newPosterName;
                            $uploadNewPoster = true;
                        } else {
                            $errorMessage = "Failed to upload new poster to server.";
                        }
                    }
                }
            }
        }

        // 3. If there were no errors, update the database
        if ($errorMessage === "") {
            $updateStmt = $conn->prepare(
                "UPDATE events SET event_name = ?, event_date = ?, venue = ?, description = ?, poster = ? WHERE id = ?"
            );
            $updateStmt->bind_param("sssssi", $eventName, $eventDate, $venue, $description, $posterToSave, $id);

            if ($updateStmt->execute()) {
                $updateStmt->close();

                // If a new poster was successfully uploaded, remove the old poster file from disk
                if ($uploadNewPoster && !empty($currentPoster)) {
                    $oldPosterPath = __DIR__ . "/uploads/" . $currentPoster;
                    if (file_exists($oldPosterPath) && is_file($oldPosterPath)) {
                        unlink($oldPosterPath);
                    }
                }

                header("Location: index.php?success=" . urlencode("Event updated successfully!"));
                exit;
            } else {
                $errorMessage = "Database update failed: " . $updateStmt->error;
                $updateStmt->close();

                // If update failed, clean up newly uploaded poster
                if ($uploadNewPoster && file_exists(__DIR__ . "/uploads/" . $posterToSave)) {
                    unlink(__DIR__ . "/uploads/" . $posterToSave);
                }
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Event - Practical 12</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="container">
    <div class="form-card">
        <h2 class="form-title">Edit Event</h2>
        <p class="form-subtitle">Update event information. You can keep the existing poster or upload a new one.</p>

        <?php if ($errorMessage !== ""): ?>
            <div class="alert alert-error">
                <span>&#9888;</span>
                <span><?= htmlspecialchars($errorMessage) ?></span>
            </div>
        <?php endif; ?>

        <form method="POST" action="edit.php?id=<?= (int)$id ?>" enctype="multipart/form-data">
            <div class="form-group">
                <label for="event_name">Event Name <span class="required">*</span></label>
                <input
                    type="text"
                    id="event_name"
                    name="event_name"
                    class="form-control"
                    value="<?= htmlspecialchars($eventName) ?>"
                    required
                >
            </div>

            <div class="form-group">
                <label for="event_date">Event Date <span class="required">*</span></label>
                <input
                    type="date"
                    id="event_date"
                    name="event_date"
                    class="form-control"
                    value="<?= htmlspecialchars($eventDate) ?>"
                    required
                >
            </div>

            <div class="form-group">
                <label for="venue">Venue <span class="required">*</span></label>
                <input
                    type="text"
                    id="venue"
                    name="venue"
                    class="form-control"
                    value="<?= htmlspecialchars($venue) ?>"
                    required
                >
            </div>

            <div class="form-group">
                <label for="description">Event Description <span class="required">*</span></label>
                <textarea
                    id="description"
                    name="description"
                    class="form-control"
                    rows="4"
                    required
                ><?= htmlspecialchars($description) ?></textarea>
            </div>

            <div class="form-group">
                <label for="poster">Change Poster (Optional)</label>
                <input
                    type="file"
                    id="poster"
                    name="poster"
                    class="form-control"
                    accept=".jpg,.jpeg,.png,image/jpeg,image/png"
                >
                <span class="file-hint">Leave blank to keep the current poster. Allowed: JPG, JPEG, PNG (Max 2 MB).</span>

                <!-- Display current poster thumbnail -->
                <?php
                    $currentPosterPath = "uploads/" . $currentPoster;
                    $hasCurrentPoster  = (!empty($currentPoster) && file_exists(__DIR__ . "/" . $currentPosterPath));
                ?>
                <?php if ($hasCurrentPoster): ?>
                    <div class="current-poster-box">
                        <img src="<?= htmlspecialchars($currentPosterPath) ?>" alt="Current Poster" class="current-poster-preview">
                        <div>
                            <strong>Current Poster:</strong> <?= htmlspecialchars($currentPoster) ?><br>
                            <small style="color: #64748b;">This poster will remain if no new file is uploaded.</small>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Update Event</button>
                <a href="index.php" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>

</body>
</html>
