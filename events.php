<?php

require "db.php";

$sql = "SELECT * FROM events";
$stmt = $pdo->query($sql);
$events = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>