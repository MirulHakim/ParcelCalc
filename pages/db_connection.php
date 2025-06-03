<?php
$host = "localhost"; // or your DB host
$username = "root"; // your DB username
$password = ""; // your DB password
$dbname = "parcelsystem"; // change to your actual DB name

$conn = new mysqli($host, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
