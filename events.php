<?php

require "db.php";

$sql = "SELECT * FROM events";
$stmt = $pdo->query($sql);
$events = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html>
<head>
    <title>StudentHub - Events</title>

</head>

<body>

<div class="container">

    <h1>🎓 StudentHub Events</h1>

    <form action="event_register.php" method="POST">

        <label>Student ID</label>

        <input
            type="number"
            name="student_id"
            placeholder="Enter Student ID"
            required
        >

        <label>Select Event</label>

        <select name="event_id" required>

            <option value="">-- Select Event --</option>

            <?php foreach ($events as $event) { ?>

                <option value="<?php echo $event['event_id']; ?>">

                    <?php echo htmlspecialchars($event['event_name']); ?>

                </option>

            <?php } ?>

        </select>

        <button type="submit">
            Register for Event
        </button>

    </form>

</div>

</body>
</html>