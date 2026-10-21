
<?php
require_once "db.php";

$id = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT);

if (!$id || $id < 1) {
    exit("Invalid student ID. Please return to the student list.");
}

$message = "";

// Update student when form is submitted
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
            "UPDATE students
             SET name = ?, email = ?, course = ?, semester = ?
             WHERE id = ?"
        );
        $stmt->bind_param("sssii", $name, $email, $course, $semester, $id);

        if ($stmt->execute()) {
            $stmt->close();
            header("Location: index.php?message=" . urlencode("Student updated successfully!"));
            exit;
        } else {
            $message = "Failed to update student: " . $stmt->error;
        }
        $stmt->close();
    }
}

// Get current student details
$stmt = $conn->prepare("SELECT * FROM students WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$student = $result->fetch_assoc();
$stmt->close();

if (!$student) {
    exit("Student not found. Please return to the student list.");
}

$name = $student["name"];
$email = $student["email"];
$course = $student["course"];
$semester = $student["semester"];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Student</title>
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
    <h2>Edit Student</h2>

    <?php if ($message !== ""): ?>
        <p class="error"><?= htmlspecialchars($message) ?></p>
    <?php endif; ?>

    <form method="POST">
        <label>Student Name</label>
        <input type="text" name="name" maxlength="100"
               value="<?= htmlspecialchars($name) ?>" required>

        <label>Email</label>
        <input type="email" name="email" maxlength="100"
               value="<?= htmlspecialchars($email) ?>" required>

        <label>Course</label>
        <select name="course" required>
            <?php
            $courses = [
                "Computer Engineering",
                "IT",
                "CSE",
                "Other"
            ];

            // Keep a previously saved course available too
            if (!in_array($course, $courses, true)) {
                $courses[] = $course;
            }

            foreach ($courses as $option):
            ?>
                <option value="<?= htmlspecialchars($option) ?>"
                    <?= $course === $option ? "selected" : "" ?>>
                    <?= htmlspecialchars($option) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label>Semester (1–12)</label>
        <input type="number" name="semester" min="1" max="12"
               value="<?= (int)$semester ?>" required>

        <button type="submit">Update Student</button>
    </form>

    <a href="index.php">← Back to Student List</a>
</div>
</body>
</html>
