<?php
require_once "pdo.php";

if (isset($_GET['Parcel_id'])) {
    $id = $_GET['Parcel_id'];
    $stmt = $pdo->prepare("SELECT Parcel_image FROM Parcel_info WHERE Parcel_id = :id");
    $stmt->execute([':id' => $id]);
    if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        header("Content-Type: image/jpeg"); // or image/png if you store PNGs
        echo $row['Parcel_image'];
    } else {
        // Optionally output a placeholder image or error
        http_response_code(404);
        echo "Image not found";
    }
} else {
    http_response_code(400);
    echo "No Parcel_id provided";
}
?>
