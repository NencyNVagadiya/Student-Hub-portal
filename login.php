<?php

$conn = new mysqli("localhost", "root", "", "studenthub");

if ($conn->connect_error) {
    die("Database connection failed");
}

$username = trim($_POST["username"] ?? "");
$password = $_POST["password"] ?? "";

if ($username == "" || $password == "") {
    die("Please enter username and password.");
}

// Find user
$sql = "SELECT * FROM users WHERE username = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows == 1) {

    $user = $result->fetch_assoc();

    // Verify password
    if (password_verify($password, $user["password"])) {

        echo "<h2>✅ Login Successful!</h2>";
        echo "<p>Welcome, " . htmlspecialchars($user["username"]) . "!</p>";

    } else {

       echo "<script>
         alert('❌ Invalid Password');
          window.location.href = 'student.html';
         </script>";
    }

} else {

   echo "<script>
           alert('❌ User Not Found');
           window.location.href = 'student.html';
           </script>";
}

$stmt->close();
$conn->close();

?>