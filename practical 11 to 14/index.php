
<?php
require_once "db.php";

// Fetch students
$search = trim($_GET['search'] ?? '');
$course = trim($_GET['course'] ?? '');

$sql = "SELECT * FROM students WHERE 1=1";
$params = [];
$types = "";

if ($search !== '') {
    $sql .= " AND (name LIKE ? OR email LIKE ?)";
    $value = "%$search%";
    $params[] = $value;
    $params[] = $value;
    $types .= "ss";
}

if ($course !== '') {
    $sql .= " AND course = ?";
    $params[] = $course;
    $types .= "s";
}

$sql .= " ORDER BY id DESC";

$stmt = $conn->prepare($sql);

if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();

// Fetch available courses for the filter
$courses = $conn->query(
    "SELECT DISTINCT course FROM students ORDER BY course"
);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Management</title>
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            padding: 30px;
            font-family: Arial, sans-serif;
            background: #f1f5f9;
            color: #1e293b;
        }

        .container {
            max-width: 1100px;
            margin: auto;
        }

        h1 {
            color: #075985;
        }

        .toolbar, .filters {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            margin-bottom: 20px;
        }

        input, select, button, .btn {
            padding: 11px 14px;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            font-size: 14px;
        }

        input {
            flex: 1;
            min-width: 180px;
        }

        button, .btn {
            background: #0369a1;
            color: white;
            border: none;
            cursor: pointer;
            text-decoration: none;
        }

        .table-box {
            background: white;
            padding: 15px;
            border-radius: 10px;
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            padding: 14px;
            border-bottom: 1px solid #e2e8f0;
            text-align: left;
        }

        th {
            background: #e0f2fe;
        }

        .message {
            padding: 12px;
            background: #dcfce7;
            margin-bottom: 15px;
            border-radius: 6px;
        }

        @media (max-width: 600px) {
            body {
                padding: 15px;
            }
        }
    </style>
</head>
<body>
<div class="container">
    <h1>Student Management System</h1>
    <p>Manage, search and filter student records.</p>

    <?php if (isset($_GET['message'])): ?>
        <div class="message">
            <?= htmlspecialchars($_GET['message']) ?>
        </div>
    <?php endif; ?>

    <div class="toolbar">
        <a class="btn" href="add.php">+ Add Student</a>
    </div>

    <form method="GET" class="filters">
        <input
            type="text"
            name="search"
            placeholder="Search name or email..."
            value="<?= htmlspecialchars($search) ?>"
        >

        <select name="course">
            <option value="">All Courses</option>

            <?php while ($row = $courses->fetch_assoc()): ?>
                <option
                    value="<?= htmlspecialchars($row['course']) ?>"
                    <?= $course === $row['course'] ? 'selected' : '' ?>
                >
                    <?= htmlspecialchars($row['course']) ?>
                </option>
            <?php endwhile; ?>
        </select>

        <button type="submit">Search / Filter</button>
        <a class="btn" href="index.php">Reset</a>
    </form>

    <div class="table-box">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Course</th>
                    <th>Semester</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php if ($result->num_rows > 0): ?>
                <?php while ($student = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= (int)$student['id'] ?></td>
                        <td><?= htmlspecialchars($student['name']) ?></td>
                        <td><?= htmlspecialchars($student['email']) ?></td>
                        <td><?= htmlspecialchars($student['course']) ?></td>
                        <td><?= (int)$student['semester'] ?></td>
                        <td>
                            <a href="edit.php?id=<?= (int)$student['id'] ?>">Edit</a>
                            |
                            <a href="delete.php?id=<?= (int)$student['id'] ?>">Delete</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6">No student records found.</td>
                </tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
</body>
</html>
