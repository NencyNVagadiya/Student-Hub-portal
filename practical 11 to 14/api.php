<?php
/**
 * api.php - RESTful JSON API for Student CRUD
 * Practical 13: PHP JSON API and AJAX/Fetch-Based CRUD without Page Reload
 *
 * Supported Methods:
 *  - GET    /api.php        -> List all students (or ?id=X for single student)
 *  - POST   /api.php        -> Add a new student
 *  - PUT    /api.php        -> Update an existing student
 *  - DELETE /api.php?id=X   -> Delete a student by ID
 */

// 1. Set JSON response header and error reporting
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Handle preflight OPTIONS request
if ($_SERVER["REQUEST_METHOD"] === "OPTIONS") {
    http_response_code(200);
    exit;
}

// 2. Connect to database
require_once "db.php";

// Helper function to send JSON response and exit
function sendResponse($status, $message, $data = null, $httpCode = 200) {
    http_response_code($httpCode);
    $response = [
        "status"  => $status,
        "message" => $message
    ];
    if ($data !== null) {
        $response["data"] = $data;
    }
    echo json_encode($response, JSON_PRETTY_PRINT);
    exit;
}

// Helper function to read incoming request data (handles JSON and form data)
function getRequestData() {
    $rawInput = file_get_contents("php://input");
    $jsonData = json_decode($rawInput, true);

    if (is_array($jsonData)) {
        return $jsonData;
    }

    // Fallback to $_POST if regular form data was submitted
    if (!empty($_POST)) {
        return $_POST;
    }

    return [];
}

// Determine HTTP Request Method (supports X-HTTP-Method-Override if needed)
$method = $_SERVER["REQUEST_METHOD"];
if ($method === "POST" && isset($_SERVER["HTTP_X_HTTP_METHOD_OVERRIDE"])) {
    $method = strtoupper($_SERVER["HTTP_X_HTTP_METHOD_OVERRIDE"]);
}

// -------------------------------------------------------------
// Route Request based on HTTP Method
// -------------------------------------------------------------
switch ($method) {

    // =========================================================
    // GET: List all students, or retrieve a single student by ?id=
    // =========================================================
    case "GET":
        $id = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT);

        if ($id && $id > 0) {
            // Fetch single student
            $stmt = $conn->prepare("SELECT id, name, email, course, semester, created_at FROM students WHERE id = ?");
            if (!$stmt) {
                sendResponse("error", "Database error: " . $conn->error, null, 500);
            }
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result();
            $student = $result->fetch_assoc();
            $stmt->close();

            if ($student) {
                sendResponse("success", "Student retrieved successfully.", $student, 200);
            } else {
                sendResponse("error", "Student not found.", null, 404);
            }
        } else {
            // Fetch all students (latest first)
            $sql = "SELECT id, name, email, course, semester, created_at FROM students ORDER BY id DESC";
            $result = $conn->query($sql);

            if (!$result) {
                sendResponse("error", "Failed to fetch students: " . $conn->error, null, 500);
            }

            $students = [];
            while ($row = $result->fetch_assoc()) {
                $students[] = $row;
            }

            sendResponse("success", "Students fetched successfully.", $students, 200);
        }
        break;

    // =========================================================
    // POST: Add a new student record
    // =========================================================
    case "POST":
        $input = getRequestData();

        $name     = trim($input["name"] ?? "");
        $email    = trim($input["email"] ?? "");
        $course   = trim($input["course"] ?? "");
        $semester = filter_var($input["semester"] ?? null, FILTER_VALIDATE_INT);

        // Server-side validation
        if ($name === "" || strlen($name) < 2) {
            sendResponse("error", "Student name is required and must be at least 2 characters.", null, 400);
        }
        if ($email === "" || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            sendResponse("error", "Please provide a valid email address.", null, 400);
        }
        if ($course === "") {
            sendResponse("error", "Please select or enter a course.", null, 400);
        }
        if ($semester === false || $semester < 1 || $semester > 12) {
            sendResponse("error", "Semester must be a valid number between 1 and 12.", null, 400);
        }

        // Insert using prepared statement
        $stmt = $conn->prepare("INSERT INTO students (name, email, course, semester) VALUES (?, ?, ?, ?)");
        if (!$stmt) {
            sendResponse("error", "Database error: " . $conn->error, null, 500);
        }

        $stmt->bind_param("sssi", $name, $email, $course, $semester);

        if ($stmt->execute()) {
            $newId = $conn->insert_id;
            $stmt->close();
            sendResponse("success", "Student added successfully!", ["id" => $newId], 201);
        } else {
            $error = $stmt->error;
            $stmt->close();
            sendResponse("error", "Failed to add student: " . $error, null, 500);
        }
        break;

    // =========================================================
    // PUT: Update an existing student record
    // =========================================================
    case "PUT":
        $input = getRequestData();

        // Check if ID is in input body or in query string
        $id = filter_var($input["id"] ?? ($_GET["id"] ?? null), FILTER_VALIDATE_INT);

        if (!$id || $id < 1) {
            sendResponse("error", "A valid student ID is required for update.", null, 400);
        }

        $name     = trim($input["name"] ?? "");
        $email    = trim($input["email"] ?? "");
        $course   = trim($input["course"] ?? "");
        $semester = filter_var($input["semester"] ?? null, FILTER_VALIDATE_INT);

        // Server-side validation
        if ($name === "" || strlen($name) < 2) {
            sendResponse("error", "Student name is required and must be at least 2 characters.", null, 400);
        }
        if ($email === "" || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            sendResponse("error", "Please provide a valid email address.", null, 400);
        }
        if ($course === "") {
            sendResponse("error", "Please select or enter a course.", null, 400);
        }
        if ($semester === false || $semester < 1 || $semester > 12) {
            sendResponse("error", "Semester must be a valid number between 1 and 12.", null, 400);
        }

        // Verify student exists
        $checkStmt = $conn->prepare("SELECT id FROM students WHERE id = ?");
        $checkStmt->bind_param("i", $id);
        $checkStmt->execute();
        $checkResult = $checkStmt->get_result();
        if ($checkResult->num_rows === 0) {
            $checkStmt->close();
            sendResponse("error", "Student with ID $id not found.", null, 404);
        }
        $checkStmt->close();

        // Update using prepared statement
        $stmt = $conn->prepare("UPDATE students SET name = ?, email = ?, course = ?, semester = ? WHERE id = ?");
        if (!$stmt) {
            sendResponse("error", "Database error: " . $conn->error, null, 500);
        }

        $stmt->bind_param("sssii", $name, $email, $course, $semester, $id);

        if ($stmt->execute()) {
            $stmt->close();
            sendResponse("success", "Student updated successfully!", ["id" => $id], 200);
        } else {
            $error = $stmt->error;
            $stmt->close();
            sendResponse("error", "Failed to update student: " . $error, null, 500);
        }
        break;

    // =========================================================
    // DELETE: Delete a student record by ID
    // =========================================================
    case "DELETE":
        $input = getRequestData();
        $id = filter_var($input["id"] ?? ($_GET["id"] ?? null), FILTER_VALIDATE_INT);

        if (!$id || $id < 1) {
            sendResponse("error", "A valid student ID is required for deletion.", null, 400);
        }

        $stmt = $conn->prepare("DELETE FROM students WHERE id = ?");
        if (!$stmt) {
            sendResponse("error", "Database error: " . $conn->error, null, 500);
        }

        $stmt->bind_param("i", $id);
        $stmt->execute();

        if ($stmt->affected_rows > 0) {
            $stmt->close();
            sendResponse("success", "Student deleted successfully!", ["id" => $id], 200);
        } else {
            $stmt->close();
            sendResponse("error", "Student not found or already deleted.", null, 404);
        }
        break;

    // =========================================================
    // Any other unsupported HTTP method
    // =========================================================
    default:
        sendResponse("error", "Method not allowed. Supported methods: GET, POST, PUT, DELETE.", null, 405);
        break;
}
?>
