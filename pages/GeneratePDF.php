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

    //Fetch data
    $stmt = $pdo->query("SELECT Parcel_id, Parcel_owner, Parcel_type, PhoneNum FROM parcel_info
                        WHERE Status=0");
    $parcels = $stmt->fetchAll(PDO::FETCH_ASSOC);

    //Start HTML content
    $html = '
    <h2>Parcel Report</h2>
    <table border="1" width="100%" style="border-collapse: collapse;">
        <thead>
            <tr>
                <th>Parcel ID</th>
                <th>Owner</th>
                <th>Parcel Type</th>
                <th>Phone Number</th>
            </tr>
        </thead>
        <tbody>
    ';

    // Populate table with data
    foreach ($parcels as $parcel) {
        $html .= '
        <tr>
            <td>' . htmlspecialchars($parcel['Parcel_id']) . '</td>
            <td>' . htmlspecialchars($parcel['Parcel_owner']) . '</td>
            <td>' . htmlspecialchars($parcel['Parcel_type']) . '</td>
            <td>' . htmlspecialchars($parcel['PhoneNum']) . '</td>
        </tr>
        ';
    }

    $html .= '
        </tbody>
    </table>';

    //Create PDF
    $mpdf = new \Mpdf\Mpdf();
    $mpdf->WriteHTML($html);
    $mpdf->Output("Parcel.pdf", "D");
    //D for forced download, Parcel.pdf nama file tu

} catch (PDOException $e) {
    echo "Database error: " . $e->getMessage();
}
?>
