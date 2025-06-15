<?php
// 1. Show errors if any
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// 2. Load mPDF
require_once __DIR__ . '/vendor/autoload.php';

// 3. Connect to database
$pdo = new PDO('mysql:host=localhost;dbname=parcelsystem', 'root', '');

// 4. Check if the form was submitted
if (isset($_POST["submit"])) {

    // 5. Start building PDF content
    $data = '<h1>Parcel</h1>';
    $data .= '<table border="1" cellspacing="0" cellpadding="5">';
    $data .= '<tr>
                <th>Parcel ID</th>
                <th>Parcel Owner</th>
                <th>Parcel Type</th>
              </tr>';

    // 6. Fetch data
    $stmt = $pdo->query("SELECT * FROM parcel_info");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $data .= '<tr>';
        $data .= '<td>' . $row['Parcel_id'] . '</td>';
        $data .= '<td>' . $row['Parcel_owner'] . '</td>';
        $data .= '<td>' . $row['Parcel_type'] . '</td>';
        $data .= '</tr>';
    }

    $data .= '</table>';

    // 7. Create PDF
    $mpdf = new \Mpdf\Mpdf();
    $mpdf->WriteHTML($data);
    $mpdf->Output("parcelsystem.pdf", "D");  // "D" = Download the file
}
?>
