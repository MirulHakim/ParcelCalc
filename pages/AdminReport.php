<?php
require_once "pdo.php";
require_once 'vendor/autoload.php';

// This block handles on-the-fly PDF generation and streaming.
if (isset($_GET['action']) && in_array($_GET['action'], ['preview', 'download'])) {
    session_start();
    // Authentication check
    if (!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
        http_response_code(403);
        die("Access Forbidden.");
    }

    $report_date = $_GET['date'] ?? date('Y-m-d');
    $formatted_date = date("d F Y", strtotime($report_date));

    // Fetch parcels for the given date (unclaimed only)
    $stmt = $pdo->prepare("SELECT * FROM parcel_info WHERE DATE(Date_arrived) = ? AND Status = 0");
    $stmt->execute([$report_date]);
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
    $mpdf->Output('admin-report-' . $report_date . '.pdf', $destination);
    exit();
}

session_start();
require_once "pdo.php"; // Re-include for the main page rendering

// Check login for the main page
if (!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
    header("Location: Login.php");
    exit();
}

$show_preview = false;
$report_date = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['report_date'])) {
    $report_date = $_POST['report_date'];
    $show_preview = true;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Admin Report Generation</title>
    <link rel="icon" type="image/x-icon" href="../resources/favicon.ico" />
    <link rel="stylesheet" href="../css/AdminView.css" />
    <link rel="stylesheet" href="../css/AdminReport.css" />
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
            <a href="logout.php">
                <button class="login-button">LOGOUT</button>
            </a>
            <div id="clock"></div>
        </div>
    </div>

    <a href="AdminView.php"><img class="back" src="../resources/Login/arrow-back0.svg"
            style="top: 130px; left: 30px;" /></a>

    <div class="report-container">
        <h2>Generate Parcel Report</h2>
        <p>Select a date to generate a report of all unclaimed parcels that arrived on that day.</p>

        <form action="AdminReport.php" method="POST" style="margin-top: 20px;">
            <label for="report_date">Report Date:</label><br>
            <input type="date" id="report_date" name="report_date" value="<?= htmlspecialchars($report_date) ?>"
                required
                style="padding: 10px; border-radius: 5px; border: 1px solid #ccc; margin-top: 5px; width: 250px;">
            <br>
            <button type="submit" name="generate_report" class="button"
                style="margin-top: 15px; background: #495bbf; color:white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer;">Generate
                Report</button>
        </form>

        <?php if ($show_preview): ?>
            <hr style="margin: 30px 0;">
            <h3>Report for: <?= htmlspecialchars(date("d F Y", strtotime($report_date))) ?></h3>
            <div class="pdf-preview" style="margin-bottom: 20px; width:100%; height:500px;">
                <iframe src="AdminReport.php?action=preview&date=<?= urlencode($report_date) ?>" width="100%" height="100%"
                    style="border: 1px solid #ccc;"></iframe>
            </div>
            <a href="AdminReport.php?action=download&date=<?= urlencode($report_date) ?>" class="download-button"
                style="background: #28a745; color:white; padding: 12px 25px; border-radius: 5px; text-decoration:none;">
                Download Report
            </a>
        <?php endif; ?>
    </div>

    <div class="trademark-report" style="text-align: center;">
        Trademark ® 2025 Parcel Serumpun. All Rights Reserved
    </div>
</body>
<script src="../js/clock.js" defer></script>
<script src="../js/mousetrailer.js" defer></script>

</html>