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

?>

<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Success - StudentHub</title>

    <style>

        * {
            box-sizing: border-box;
        }

        body {

            margin: 0;

            min-height: 100vh;

            font-family: Arial, Helvetica, sans-serif;

            display: flex;

            justify-content: center;

            align-items: center;

            background:
                linear-gradient(
                    rgba(0,0,0,0.35),
                    rgba(0,0,0,0.35)
                ),
                url("./image/contact.png");

            background-size: cover;

            background-position: center;

            background-repeat: no-repeat;

            background-attachment: fixed;
        }


        .success-box {

            width: 90%;

            max-width: 520px;

            padding: 45px 35px;

            text-align: center;

            background: rgba(255,255,255,0.95);

            border-radius: 18px;

            box-shadow:
                0 10px 35px rgba(0,0,0,0.3);

        }


        .success-icon {

            width: 75px;

            height: 75px;

            margin: 0 auto 20px;

            display: flex;

            align-items: center;

            justify-content: center;

            background: #1678c9;

            border-radius: 50%;

            color: white;

            font-size: 42px;

        }


        h1 {

            margin: 0 0 15px;

            color: #0b4d74;

            font-size: 30px;

        }


        p {

            color: #444;

            font-size: 17px;

            line-height: 1.6;

            margin-bottom: 25px;

        }


        .back-button {

            display: inline-block;

            padding: 13px 25px;

            background: #1678c9;

            color: white;

            text-decoration: none;

            border-radius: 8px;

            font-size: 16px;

            font-weight: bold;

            transition: 0.3s;

        }


        .back-button:hover {

            background: #0b4d74;

            transform: translateY(-2px);

        }


        @media (max-width: 500px) {

            .success-box {

                padding: 35px 25px;

            }

            h1 {

                font-size: 25px;

            }

            p {

                font-size: 15px;

            }

        }

    </style>

</head>


<body>

    <div class="success-box">

        <div class="success-icon">
            ✓
        </div>

        <h1>
            Message Submitted Successfully!
        </h1>

        <p>
            Thank you for contacting StudentHub.<br>
            Your message has been saved successfully.
        </p>

        <a href="contact.html" class="back-button">
            ← Back to Contact Us
        </a>

    </div>

</body>

</html>

<?php

} else {

    echo "Invalid request.";

}

?>