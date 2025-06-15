<?php
require('fpdf.php');
$pdo = new PDO('mysql:host=localhost;dbname=parcelsystem', 'root', '');

$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 12);

// Optional: Add table headers
$pdf->Cell(40, 10, 'Parcel ID');
$pdf->Cell(60, 10, 'Parcel Owner');
$pdf->Cell(60, 10, 'Parcel Type');
$pdf->Ln(); // move to next line

$pdf->SetFont('Arial', '', 12); //set the font

// Fetch data
$stmt = $pdo->query("SELECT * FROM parcel_info");
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $pdf->Cell(40, 10, $row['Parcel_id']);
    $pdf->Cell(60, 10, $row['Parcel_owner']);
    $pdf->Cell(60, 10, $row['Parcel_type']);
    $pdf->Ln();//move next line
}

$pdf->Output('D', 'parcel_report.pdf'); // D = force download, I = inline display
?>

