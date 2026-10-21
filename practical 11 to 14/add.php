
<?php
require_once "db.php";

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = trim($_POST["name"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $course = trim($_POST["course"] ?? "");
    $semester = filter_input(INPUT_POST, "semester", FILTER_VALIDATE_INT);

    if ($name === "" || !filter_var($email, FILTER_VALIDATE_EMAIL)
        || $course === "" || $semester === false || $semester === null
        || $semester < 1 || $semester > 12) {
        $message = "Please enter valid details. Semester must be 1–12.";
    } else {
        $stmt = $conn->prepare(
            "INSERT INTO students (name, email, course, semester)
             VALUES (?, ?, ?, ?)"
        );

        $stmt->bind_param("sssi", $name, $email, $course, $semester);

        if ($stmt->execute()) {
            header("Location: index.php?message=" .
                urlencode("Student added successfully!"));
            exit;
        } else {
            $message = "Failed to add student: " . $stmt->error;
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Student</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f1f5f9;
            padding: 30px;
            color: #1e293b;
        }
        .form-box {
            max-width: 500px;
            margin: 30px auto;
            padding: 25px;
            background: white;
            border-radius: 10px;
        }
        input, select, button {
            width: 100%;
            padding: 12px;
            margin: 9px 0;
            box-sizing: border-box;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
        }
        button {
            background: #0369a1;
            color: white;
            border: none;
            cursor: pointer;
        }
        a { color: #0369a1; }
        .error { color: #b91c1c; }
    </style>
</head>
<body>
<div class="form-box">
    <h2>Add New Student</h2>

    <?php if ($message !== ""): ?>
        <p class="error"><?= htmlspecialchars($message) ?></p>
    <?php endif; ?>

    <form method="POST">
        <label>Student Name</label>
        <input type="text" name="name" required maxlength="100">

        <label>Email</label>
        <input type="email" name="email" required maxlength="100">

        <label>Course</label>
        <select name="course" required>
            <option value="">Select Course</option>
            <option value="Computer Engineering">Computer Engineering</option>
            <option value="IT">Information Technology</option>
            <option value="CSE">Computer Science</option>
            <option value="Other">Other</option>
        </select>

        <label>Semester (1–12)</label>
        <input type="number" name="semester" min="1" max="12" required>

        <button type="submit">Save Student</button>
    </form>

    <a href="index.php">← Back to Student List</a>
</div>
</body>
</html>
