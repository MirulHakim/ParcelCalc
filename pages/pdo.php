<?php
$host = 'localhost';      // Database server
$port = '3306';           // Usually 3306 for MySQL (8889 for MAMP)
$db   = 'parcelsystem';  // Change this to your actual DB name
$user = 'root';           // Your MySQL username (often 'root' on XAMPP)
$pass = '';               // Your MySQL password (often empty '' on XAMPP)

$pdo = new PDO("mysql:host=$host;port=$port;dbname=$db", $user, $pass);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
?>