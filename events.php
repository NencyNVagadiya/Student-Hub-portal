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
   <link rel="stylesheet" href="events.css">
</head>

<body>

<header>
    <nav>
        <div class="left" onclick="toggleMenu()">☰</div>
        <div id="sidebar" class="sidebar">
            <div class="close-btn" onclick="toggleMenu()">←</div>
            <a href="Home.html">🏡 Home page</a>
            <a href="AcademicCalender.html">📅 Academic Calendar</a>
            <a href="faculty.html">👩🏼‍🏫🧑🏻‍🏫 Faculty (DEPSTAR)</a>
            <a href="fees.html">💳 Fees</a>
            <a href="contact.html">📞 Contact Us</a>
            <a href="attendance.html">📊 Attendance</a>
            <a href="studentO2.html">📜 Student O2</a>
            <a href="Scholarship.html">🎓 Scholarship</a>
            <a href="study-materials.html">📚 Study Materials</a>
            <a href="profile.html">👤 Profile</a>
            <a href="notices.html">📢 Notices</a>
            <a href="events.php">🎉 Events</a>
        </div>
        <div class="right" id="theme-toggle">🌙</div>
    </nav>
</header>

<div class="container">

    <h1>🎓 StudentHub Events</h1>

    <form action="event_register.php" method="POST">

        <label>Student ID</label>

        <input
            type="letter"
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

<script>
const themeToggle = document.getElementById("theme-toggle");

if (localStorage.getItem("darkMode") === "enabled") {
    document.body.classList.add("dark-mode");
    themeToggle.textContent = "☀️";
}

themeToggle.addEventListener("click", function () {
    document.body.classList.toggle("dark-mode");

    const darkModeEnabled = document.body.classList.contains("dark-mode");
    localStorage.setItem("darkMode", darkModeEnabled ? "enabled" : "disabled");
    themeToggle.textContent = darkModeEnabled ? "☀️" : "🌙";
});

function toggleMenu() {
    const sidebar = document.getElementById("sidebar");
    sidebar.style.left = sidebar.style.left === "0px" ? "-300px" : "0px";
}
</script>

</body>
</html>