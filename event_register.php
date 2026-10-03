<?php

require "db.php";

$student_id = 1;
$event_id = 1;

$sql = "INSERT INTO registrations
        (student_id, event_id, registration_date)
        VALUES
        (:student_id, :event_id, CURDATE())";

$stmt = $pdo->prepare($sql);

$stmt->execute([
    ":student_id" => $student_id,
    ":event_id" => $event_id
]);

echo "Event Registration Successful!";

?>