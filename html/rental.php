<?php

$mysqli = new mysqli("localhost", "user", "Password123!", "");

if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}
echo "Connected successfully to MySQL!";
?>
