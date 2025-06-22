<?php
// Set timezone to Malaysia time
date_default_timezone_set('Asia/Kuala_Lumpur');

$host = 'localhost';
$port = '3306';
$db = 'parcelsystem';
$user = 'root';
$pass = '';

$pdo = new PDO("mysql:host=$host;port=$port;dbname=$db", $user, $pass);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Set MySQL timezone to match PHP
$pdo->exec("SET time_zone = '+08:00'");
?>