
<?php
require_once "db.php";

$id = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT);

if (!$id || $id < 1) {
    header("Location: index.php?message=" . urlencode("Invalid student ID."));
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $confirmId = filter_input(INPUT_POST, "id", FILTER_VALIDATE_INT);

    if ($confirmId !== $id) {
        header("Location: index.php?message=" . urlencode("Invalid request."));
        exit;
    }

    $stmt = $conn->prepare("DELETE FROM students WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute() && $stmt->affected_rows > 0) {
        $stmt->close();
        header("Location: index.php?message=" . urlencode("Student deleted successfully!"));
        exit;
    }

    $stmt->close();
    header("Location: index.php?message=" . urlencode("Student not found or could not be deleted."));
    exit;
}

$stmt = $conn->prepare("SELECT name, email FROM students WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$student = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$student) {
    header("Location: index.php?message=" . urlencode("Student not found."));
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete Student</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f1f5f9;
            padding: 30px;
            color: #1e293b;
        }
        .box {
            max-width: 500px;
            margin: 50px auto;
            padding: 25px;
            background: white;
            border-radius: 10px;
        }
        button, a {
            display: inline-block;
            padding: 11px 15px;
            border-radius: 6px;
            text-decoration: none;
            border: none;
            cursor: pointer;
            margin-right: 8px;
        }
        button {
            background: #dc2626;
            color: white;
        }
        a {
            background: #e2e8f0;
            color: #1e293b;
        }
    </style>
</head>
<body>
<div class="box">
    <h2>Confirm Delete</h2>
    <p>Are you sure you want to delete this student?</p>

    <p><strong>Name:</strong>
        <?= htmlspecialchars($student["name"]) ?></p>
    <p><strong>Email:</strong>
        <?= htmlspecialchars($student["email"]) ?></p>

    <form method="POST">
        <input type="hidden" name="id" value="<?= (int)$id ?>">
        <button type="submit">Yes, Delete Student</button>
        <a href="index.php">Cancel</a>
    </form>
</div>
</body>
</html>
