<?php

$host = "localhost";
$dbname = "studenthub";
$username = "root";
$password = "";

try {

    $pdo = new PDO(
        "mysql:host=$host;dbname=$dbname",
        $username,
        $password
    );

    $pdo->setAttribute(
        PDO::ATTR_ERRMODE,
        PDO::ERRMODE_EXCEPTION
    );

    echo "Database Connected Successfully!";

} catch (PDOException $e) {

    echo "Connection Failed: " . $e->getMessage();

}

?>