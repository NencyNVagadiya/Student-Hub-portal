<?php

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $name = $_POST["name"] ?? "";
    $email = $_POST["email"] ?? "";
    $subject = $_POST["subject"] ?? "";
    $message = $_POST["message"] ?? "";

    $file = "contacts.csv";

    $handle = fopen($file, "a");

    if ($handle === false) {
        die("Cannot open contacts.csv");
    }

    // Add heading only when file is empty
    if (filesize($file) == 0) {
        fputcsv($handle, [
            "Name",
            "Email",
            "Subject",
            "Message"
        ]);
    }

    // Save form data
    fputcsv($handle, [
        $name,
        $email,
        $subject,
        $message
    ]);

    fclose($handle);

    echo "<h2>✅ Message Submitted Successfully!</h2>";
    echo "<p>Your data has been saved in contacts.csv</p>";
    echo '<a href="contact.html">Back to Contact Us</a>';

} else {

    echo "Invalid request.";

}

?>