<?php
require_once __DIR__ . '/vendor/autoload.php';
$mpdf = new \Mpdf\Mpdf();
$mpdf->WriteHTML('<h1>Test PDF</h1><p>If you see this, mPDF works!</p>');
$mpdf->Output("test.pdf", "D");
?>