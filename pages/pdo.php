<?php
$host = 'localhost';      
$port = '3306';           
$db   = 'parcelsystem';  
$user = 'root';           
$pass = '';             

$pdo = new PDO("mysql:host=$host;port=$port;dbname=$db", $user, $pass);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
?>