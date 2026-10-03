<?php

require "db.php";

$name = "Nency Vagadiya";
$email = "nency@example.com";
$branch = "Computer Engineering";
$year = 2;

$sql = "INSERT INTO students
        (name, email, branch, year)
        VALUES
        (:name, :email, :branch, :year)";

$stmt = $pdo->prepare($sql);

$stmt->execute([
    ":name" => $name,
    ":email" => $email,
    ":branch" => $branch,
    ":year" => $year
]);

echo "Student Registered Successfully!";

?>