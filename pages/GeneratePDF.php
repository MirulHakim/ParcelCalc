<?php
require_once __DIR__ . '/vendor/autoload.php';

$pdo = new PDO('mysql:host=localhost;dbname=parcelsystem', 'root', '');

if (isset($_POST["submit"])) {
    $data = '<h1>Parcel</h1>';
    $data .= '<table border="1" cellspacing="0" cellpadding="5">';
    $data .= '<tr>
                <th>Parcel ID</th>
                <th>Parcel Owner</th>
                <th>Parcel Type</th>
              </tr>';

    // Fetch data
    $stmt = $pdo->query("SELECT * FROM parcel_info");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $data .= '<tr>';
        $data .= '<td>' . $row['Parcel_id'] . '</td>';
        $data .= '<td>' . $row['Parcel_owner'] . '</td>';
        $data .= '<td>' . $row['Parcel_type'] . '</td>';
        $data .= '</tr>';
    }

    $data .= '</table>';

    // Create PDF
    $mpdf = new \Mpdf\Mpdf();
    $mpdf->WriteHTML($data);
    $mpdf->Output("parcelsystem.pdf", "D");
}
?>

