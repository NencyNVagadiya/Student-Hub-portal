<?php

require "db.php";

$student_id = $_POST["student_id"] ?? "";
$event_id = $_POST["event_id"] ?? "";

if ($student_id == "" || $event_id == "") {
    die("Please select a student and event.");
}

$sql = "INSERT INTO registrations
        (student_id, event_id, registration_date)
        VALUES
        (:student_id, :event_id, CURDATE())";

$stmt = $pdo->prepare($sql);

$stmt->execute([
    ":student_id" => $student_id,
    ":event_id" => $event_id
]);

echo "<h2>✅ Event Registration Successful!</h2>";
echo "<p>Student ID: " . htmlspecialchars($student_id) . "</p>";
echo "<p>Event ID: " . htmlspecialchars($event_id) . "</p>";

echo '<a href="events.php">← Back to Events</a>';

?>