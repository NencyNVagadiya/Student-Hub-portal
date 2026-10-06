<?php

require "db.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);
    exit("Method not allowed.");
}

$studentId = strtoupper(trim($_POST["student_id"] ?? ""));
$eventId = filter_var($_POST["event_id"] ?? null, FILTER_VALIDATE_INT);

if (!preg_match('/^[A-Z0-9][A-Z0-9-]{0,31}$/', $studentId)) {
    http_response_code(400);
    exit("Enter a valid Student ID.");
}

if ($eventId === false || $eventId === null || $eventId < 1) {
    http_response_code(400);
    exit("Select a valid event.");
}

// Get the event (also gives us its name to show)
$eventCheck = $pdo->prepare(
    "SELECT event_name FROM events WHERE event_id = :event_id"
);
$eventCheck->execute([":event_id" => $eventId]);
$event = $eventCheck->fetch(PDO::FETCH_ASSOC);

if (!$event) {
    http_response_code(400);
    exit("The selected event does not exist.");
}

// Look up the student (optional, just to show the name)
$studentCheck = $pdo->prepare(
    "SELECT name, email FROM students WHERE student_id = :student_id"
);
$studentCheck->execute([":student_id" => $studentId]);
$student = $studentCheck->fetch(PDO::FETCH_ASSOC);

// Save the registration
try {
    $stmt = $pdo->prepare(
        "INSERT INTO registrations (student_id, event_id, registration_date)
         VALUES (:student_id, :event_id, CURDATE())"
    );
    $stmt->execute([
        ":student_id" => $studentId,
        ":event_id"   => $eventId
    ]);
} catch (PDOException $e) {
    http_response_code(400);
    exit("Could not register. Student ID may not exist or is already registered.");
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Registration Successful</title>
    <link rel="stylesheet" href="events.css">
</head>
<body>
<div class="container">
    <h1>✅ Registration Successful!</h1>
    <p><strong>Student ID:</strong> <?php echo htmlspecialchars($studentId); ?></p>
    <?php if ($student) { ?>
        <p><strong>Name:</strong> <?php echo htmlspecialchars($student["name"]); ?></p>
        <p><strong>Email:</strong> <?php echo htmlspecialchars($student["email"]); ?></p>
    <?php } ?>
    <p><strong>Event:</strong> <?php echo htmlspecialchars($event["event_name"]); ?></p>
    <p><a href="events.php">← Back to Events</a></p>
</div>
</body>
</html>