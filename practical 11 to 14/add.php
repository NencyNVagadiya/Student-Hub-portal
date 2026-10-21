<?php
/**
 * add.php - Add New Event with Poster Upload & File Validation
 * Practical 12: Event Management System
 */

require_once "db.php";

$errorMessage = "";
$eventName    = "";
$eventDate    = "";
$venue        = "";
$description  = "";

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // 1. Sanitize text inputs
    $eventName   = trim($_POST["event_name"] ?? "");
    $eventDate   = trim($_POST["event_date"] ?? "");
    $venue       = trim($_POST["venue"] ?? "");
    $description = trim($_POST["description"] ?? "");

    // 2. Validate required text fields
    if ($eventName === "" || $eventDate === "" || $venue === "" || $description === "") {
        $errorMessage = "All fields (Event Name, Date, Venue, Description) are required.";
    } elseif (!isset($_FILES["poster"]) || $_FILES["poster"]["error"] === UPLOAD_ERR_NO_FILE) {
        $errorMessage = "Please select an event poster image to upload.";
    } else {
        $file = $_FILES["poster"];

        // 3. Poster Validation
        // A. Check for PHP upload errors
        if ($file["error"] !== UPLOAD_ERR_OK) {
            if ($file["error"] === UPLOAD_ERR_INI_SIZE || $file["error"] === UPLOAD_ERR_FORM_SIZE) {
                $errorMessage = "Poster file size exceeds the 2 MB limit.";
            } else {
                $errorMessage = "File upload failed with error code: " . $file["error"];
            }
        }
        // B. Check file size (Maximum 2 MB = 2 * 1024 * 1024 bytes = 2,097,152 bytes)
        elseif ($file["size"] > (2 * 1024 * 1024)) {
            $errorMessage = "Poster file size exceeds 2 MB. Please choose a smaller image.";
        }
        // C. Check file extension (JPG, JPEG, PNG only)
        else {
            $originalName = $file["name"];
            $extension    = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
            $allowedExts  = ["jpg", "jpeg", "png"];

            if (!in_array($extension, $allowedExts, true)) {
                $errorMessage = "Invalid file type. Only JPG, JPEG, and PNG images are allowed.";
            }
            // D. Validate MIME type using finfo
            else {
                $allowedMimes = ["image/jpeg", "image/png"];
                $finfo        = finfo_open(FILEINFO_MIME_TYPE);
                $mimeType     = finfo_file($finfo, $file["tmp_name"]);
                finfo_close($finfo);

                if (!in_array($mimeType, $allowedMimes, true)) {
                    $errorMessage = "Invalid image content. Only JPEG and PNG images are accepted.";
                }
                // E. Validate image integrity using getimagesize()
                elseif (@getimagesize($file["tmp_name"]) === false) {
                    $errorMessage = "The uploaded file is not a valid or readable image.";
                }
                else {
                    // 4. Generate a safe unique filename to avoid overwrites and path traversal
                    $uniqueFileName = "poster_" . time() . "_" . bin2hex(random_bytes(4)) . "." . $extension;

                    // Ensure target upload directory exists
                    $uploadDirectory = __DIR__ . "/uploads/";
                    if (!is_dir($uploadDirectory)) {
                        mkdir($uploadDirectory, 0777, true);
                    }

                    $destination = $uploadDirectory . $uniqueFileName;

                    // 5. Move uploaded file from temp directory to uploads/
                    if (move_uploaded_file($file["tmp_name"], $destination)) {
                        // 6. Insert record into database using prepared statement
                        $stmt = $conn->prepare(
                            "INSERT INTO events (event_name, event_date, venue, description, poster) VALUES (?, ?, ?, ?, ?)"
                        );
                        $stmt->bind_param("sssss", $eventName, $eventDate, $venue, $description, $uniqueFileName);

                        if ($stmt->execute()) {
                            $stmt->close();
                            header("Location: index.php?success=" . urlencode("Event added successfully with poster!"));
                            exit;
                        } else {
                            $errorMessage = "Database insertion failed: " . $stmt->error;
                            // Clean up uploaded file if database insert fails
                            if (file_exists($destination)) {
                                unlink($destination);
                            }
                            $stmt->close();
                        }
                    } else {
                        $errorMessage = "Failed to move uploaded poster to the uploads folder.";
                    }
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
    <title>Add New Event - Practical 12</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="container">
    <div class="form-card">
        <h2 class="form-title">Add New Event</h2>
        <p class="form-subtitle">Fill in the event details and upload a poster image (JPG, JPEG, PNG up to 2 MB).</p>

        <?php if ($errorMessage !== ""): ?>
            <div class="alert alert-error">
                <span>&#9888;</span>
                <span><?= htmlspecialchars($errorMessage) ?></span>
            </div>
        <?php endif; ?>

        <!-- Form must have enctype="multipart/form-data" for file uploads -->
        <form method="POST" action="add.php" enctype="multipart/form-data">
            <div class="form-group">
                <label for="event_name">Event Name <span class="required">*</span></label>
                <input
                    type="text"
                    id="event_name"
                    name="event_name"
                    class="form-control"
                    placeholder="e.g., CodeSprint 2026 Hackathon"
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
                    placeholder="e.g., Seminar Hall A, Engineering Block"
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
                    placeholder="Brief description of the event agenda, topics, or rules..."
                    required
                ><?= htmlspecialchars($description) ?></textarea>
            </div>

            <div class="form-group">
                <label for="poster">Event Poster <span class="required">*</span></label>
                <input
                    type="file"
                    id="poster"
                    name="poster"
                    class="form-control"
                    accept=".jpg,.jpeg,.png,image/jpeg,image/png"
                    required
                >
                <span class="file-hint">Allowed formats: JPG, JPEG, PNG &bull; Maximum size: 2 MB</span>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Save Event</button>
                <a href="index.php" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>

</body>
</html>
