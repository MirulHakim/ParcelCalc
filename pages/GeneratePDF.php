<?php
require_once __DIR__ . '/vendor/autoload.php';

// Database connection
$host = 'localhost';
$db = 'parcelsystem';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Fetch data
    $stmt = $pdo->query("SELECT Parcel_id, Recipient_name, Address, Weight, Status FROM parcel_info");
    $parcels = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Start HTML content
    $html = '
    <h2>Parcel Report</h2>
    <table border="1" width="100%" style="border-collapse: collapse;">
        <thead>
            <tr>
                <th>Parcel ID</th>
                <th>Recipient</th>
                <th>Address</th>
                <th>Weight</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
    ';

    // Populate table with data
    foreach ($parcels as $parcel) {
        $html .= '
        <tr>
            <td>' . htmlspecialchars($parcel['Parcel_id']) . '</td>
            <td>' . htmlspecialchars($parcel['Recipient_name']) . '</td>
            <td>' . htmlspecialchars($parcel['Address']) . '</td>
            <td>' . htmlspecialchars($parcel['Weight']) . '</td>
            <td>' . htmlspecialchars($parcel['Status']) . '</td>
        </tr>
        ';
    }

    $html .= '
        </tbody>
    </table>';

    // Create PDF
    $mpdf = new \Mpdf\Mpdf();
    $mpdf->WriteHTML($html);
    $mpdf->Output("Parcel.pdf", "D"); // Download as Parcel.pdf

} catch (PDOException $e) {
    echo "Database error: " . $e->getMessage();
}
?>
