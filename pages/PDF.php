<?php
require_once '../controllers/pdo.php';
require_once '../vendor/autoload.php';

// This block handles on-the-fly PDF generation and streaming.
if (isset($_GET['action']) && in_array($_GET['action'], ['preview', 'download'])) {
  session_start();
  // Authentication check
  if (!isset($_SESSION['student_logged_in']) || $_SESSION['student_logged_in'] !== true) {
    http_response_code(403);
    die("Access Forbidden.");
  }

  $arrival_date = $_GET['date'] ?? date('Y-m-d');
  $formatted_date = date("d F Y", strtotime($arrival_date));

  // Fetch parcels for the given date
  $stmt = $pdo->prepare("SELECT * FROM parcel_info WHERE DATE(Date_arrived) = ? AND Status = 0");
  $stmt->execute([$arrival_date]);
  $parcels = $stmt->fetchAll(PDO::FETCH_ASSOC);

  $mpdf = new \Mpdf\Mpdf();
  $html = '<h1>Parcel Report for ' . htmlspecialchars($formatted_date) . '</h1>';

  if (empty($parcels)) {
    $html .= '<p>No unclaimed parcels found for this date.</p>';
  } else {
    $html .= '<table border="1" style="width:100%; border-collapse: collapse;"><thead><tr>
                    <th style="padding: 8px;">Parcel ID</th><th style="padding: 8px;">Owner</th>
                    <th style="padding: 8px;">Parcel Type</th><th style="padding: 8px;">Phone Number</th>
                  </tr></thead><tbody>';
    foreach ($parcels as $parcel) {
      $html .= '<tr>';
      $html .= '<td style="padding: 5px;">' . htmlspecialchars($parcel['Parcel_id']) . '</td>';
      $html .= '<td style="padding: 5px;">' . htmlspecialchars($parcel['Parcel_owner']) . '</td>';
      $html .= '<td style="padding: 5px;">' . htmlspecialchars($parcel['Parcel_type']) . '</td>';
      $html .= '<td style="padding: 5px;">' . htmlspecialchars($parcel['PhoneNum']) . '</td>';
      $html .= '</tr>';
    }
    $html .= '</tbody></table>';
  }

  $mpdf->WriteHTML($html);

  $destination = ($_GET['action'] === 'download') ? 'D' : 'I';
  $mpdf->Output('parcel-report-' . $arrival_date . '.pdf', $destination);
  exit();
}

session_start();
require_once '../controllers/pdo.php'; // Re-include for the main page rendering

// Check login for the main page
if (!isset($_SESSION['student_logged_in']) || $_SESSION['student_logged_in'] !== true) {
  header("Location: Login.php");
  exit();
}

$show_preview = false;
$errorMsg = '';
$arrival_date = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['arrival_date'])) {
  $arrival_date = $_POST['arrival_date'];

  // We just need to know whether to show the preview section.
  // The actual data check will happen in the streaming endpoint.
  $show_preview = true;

  // Optional: Pre-check if there are any parcels to avoid showing an empty preview section.
  $stmt = $pdo->prepare("SELECT 1 FROM parcel_info WHERE DATE(Date_arrived) = ? AND Status = 0 LIMIT 1");
  $stmt->execute([$arrival_date]);
  if ($stmt->fetch() === false) {
    $show_preview = false; // Don't show preview if no parcels exist
    $formatted_date = date("d F Y", strtotime($arrival_date));
    $errorMsg = 'No unclaimed parcels found for ' . htmlspecialchars($formatted_date) . '.';
  }
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $errorMsg = "No date selected. Please go back to the homepage and select a date.";
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>PDF Preview & Download</title>
  <link rel="icon" type="image/x-icon" href="../resources/favicon.ico" />
  <link rel="stylesheet" href="../css/PDF.css" />
  <link rel="stylesheet" href="../css/style.css" />
  <link rel="stylesheet" href="../css/mousetrailer.css" />
</head>

<body>
  <div class="header">
    <div class="row" style="gap: 0px">
      <div class="box blue" style="position: relative; z-index: 0"></div>
      <div class="box trapezium" style="position: relative; z-index: 1"></div>
      <div class="row logos">
        <img class="logo" src="../resources/Header/image-10.png" />
        <div class="x">X</div>
        <img class="logo" src="../resources/Header/logo-k-14-10.png" />
      </div>
      <a href="../controllers/logout.php">
        <button class="login-button">LOGOUT</button>
      </a>
    </div>
    <div id="clock"></div>
  </div>

  <div class="row">
    <a href="Homepage.php"><img class="back" src="../resources/Login/arrow-back0.svg" /></a>
    <p class="title">PARCEL LIST PDF PREVIEW</p>
  </div>

  <div class="container">
    <?php if ($errorMsg): ?>
      <div
        style="text-align: center; color: #D8000C; background-color: #FFD2D2; padding: 20px; border-radius: 8px; margin-top: 50px;">
        <p><?= htmlspecialchars($errorMsg) ?></p>
        <a href="Homepage.php" style="text-decoration: none; color: #495BFF; font-weight: bold;">Go back</a>
      </div>
    <?php endif; ?>

    <?php if ($show_preview): ?>
      <div class="pdf-preview" style="margin-bottom: 20px;">
        <iframe src="PDF.php?action=preview&date=<?= urlencode($arrival_date) ?>" width="100%" height="600px"
          style="border: 1px solid #ccc;"></iframe>
      </div>
      <div style="text-align: center;">
        <p class="parcel">REPORT FOR: <?= htmlspecialchars(date("d F Y", strtotime($arrival_date))) ?></p>
        <a href="PDF.php?action=download&date=<?= urlencode($arrival_date) ?>" class="download-button" download>DOWNLOAD
          PDF</a>
      </div>
    <?php elseif (empty($errorMsg)): ?>
      <div style="text-align: center; padding: 50px;">
        <p>This page is for previewing a PDF report. Please go to the <a href="Homepage.php"
            style="color: #495BFF; font-weight: bold;">homepage</a> to select a date.</p>
      </div>
    <?php endif; ?>
  </div>

  <div class="trademark">
    Trademark ® 2025 Parcel Serumpun. All Rights Reserved
  </div>

  <style>
    .download-button {
      display: inline-block;
      padding: 12px 25px;
      background-color: #495BFF;
      color: white;
      text-align: center;
      text-decoration: none;
      font-size: 16px;
      border-radius: 8px;
      border: none;
      cursor: pointer;
      transition: background-color 0.3s;
      font-weight: bold;
    }

    .download-button:hover {
      background-color: #3a48a3;
    }
  </style>
</body>
<script src="../js/clock.js" defer></script>
<script src="../js/mousetrailer.js" defer></script>

</html>