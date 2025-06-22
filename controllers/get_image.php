<?php
// Database connection
$host = 'localhost';
$db = 'parcelsystem';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

$parcel_id = $_GET['Parcel_id'] ?? '';

if ($parcel_id) {
    $stmt = $pdo->prepare("SELECT Parcel_image FROM Parcel_info WHERE Parcel_id = :parcel_id");
    $stmt->execute(['parcel_id' => $parcel_id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($row && !empty($row['Parcel_image'])) {
        header("Content-Type: image/jpeg"); // Adjust if your image type differs
        echo $row['Parcel_image'];
        exit;
    } else {
        http_response_code(404);
        echo "Image not found.";
    }
} else {
    http_response_code(400);
    echo "No parcel ID provided.";
}
