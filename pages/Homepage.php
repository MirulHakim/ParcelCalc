<?php
session_start();
if (!isset($_SESSION['student_logged_in']) || $_SESSION['student_logged_in'] !== true) {
  header("Location: Login.php");
  exit();
}

require_once "../controllers/pdo.php";

// Flash messages for report submission
$reportSuccess = '';
$reportError = '';
if (isset($_SESSION['report_success'])) {
  $reportSuccess = $_SESSION['report_success'];
  unset($_SESSION['report_success']);
}
if (isset($_SESSION['report_error'])) {
  $reportError = $_SESSION['report_error'];
  unset($_SESSION['report_error']);
}

// Handle new report submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_report'])) {
  // CSRF check
  if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
    die("Invalid CSRF token.");
  }
  $student_id = $_SESSION['student_id'];
  $parcel_id = trim($_POST['parcel_id'] ?? '');
  $report_text = trim($_POST['report_text'] ?? '');
  if (empty($parcel_id) || empty($report_text)) {
    $_SESSION['report_error'] = "Please select a parcel and enter your report.";
  } else {
    try {
      $stmt = $pdo->prepare("INSERT INTO report (Parcel_id, Student_id, Report, Status) VALUES (?, ?, ?, 0)");
      $stmt->execute([$parcel_id, $student_id, $report_text]);
      $_SESSION['report_success'] = "Report submitted successfully!";
    } catch (PDOException $e) {
      $_SESSION['report_error'] = "Error submitting report: " . $e->getMessage();
    }
  }
  // Redirect to prevent form resubmission
  header("Location: Homepage.php");
  exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="icon" type="image/x-icon" href="../resources/favicon.ico" />
  <link rel="stylesheet" href="../css/Homepage.css" />
  <link rel="stylesheet" href="../css/style.css" />
  <link rel="stylesheet" href="../css/mousetrailer.css" />
  <title>Parcel Serumpun</title>
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

  <div class="content">
    <h2>Search for your parcel<br />here</h2>
    <p>
      Search for your parcel id number to check its availability in<br />Parcel
      Serumpun's database
    </p>
    <form action="ParcelView.php" method="post">
      <input class="search" type="text" id="parcel_id" name="parcel_id" placeholder="Enter your parcel ID">
    </form>

    <div class="divide">
      <div class="divider"></div>
      <div class="or">or</div>
      <div class="divider"></div>
    </div>

    <h2>Enter parcel arrival date<br />here</h2>
    <p>
      Generate a PDF report for the list of parcels arrived at a specific date
    </p>
    <form action="PDF.php" method="post">
      <div class="date-wrapper">
        <input class="date" type="date" id="arrival_date" name="arrival_date" required>
      </div><br>
      <input class="submit" type="submit" value="Generate PDF">
    </form>

    <div class="trademark">
      Trademark ® 2025 Parcel Serumpun. All Rights Reserved
    </div>

    <div class="reports-main-container">
      <!-- New Report Form -->
      <div class="new-report-section">
        <h2>Submit a New Report to Admin</h2>
        <?php if (!empty($reportSuccess)): ?>
          <div style="color: #4caf50; margin-bottom: 10px;"> <?= htmlspecialchars($reportSuccess) ?> </div>
        <?php endif; ?>
        <?php if (!empty($reportError)): ?>
          <div style="color: #f44336; margin-bottom: 10px;"> <?= htmlspecialchars($reportError) ?> </div>
        <?php endif; ?>
        <form method="POST" action="">
          <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

          <label for="parcel_id">Parcel ID</label>
          <select name="parcel_id" id="parcel_id" required>
            <option value="">Select a Parcel...</option>
            <?php
            $stmt = $pdo->query("SELECT Parcel_id FROM parcel_info ORDER BY Parcel_id DESC");
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
              echo '<option value="' . htmlspecialchars($row['Parcel_id']) . '">' . htmlspecialchars($row['Parcel_id']) . '</option>';
            }
            ?>
          </select>

          <label for="report_text">Report Details</label>
          <textarea name="report_text" id="report_text" rows="4" required
            placeholder="Please describe the issue..."></textarea>

          <button type="submit" name="submit_report" class="action-btn">Submit Report</button>
        </form>
      </div>

      <!-- Student Reports Section -->
      <div class="student-reports" style="margin-top: 40px;">
        <h2>Your Reports to Admin</h2>
        <?php
        $student_id = $_SESSION['student_id'];
        // The LEFT JOIN was removed to prevent row duplication due to data type mismatch (INT vs VARCHAR) on Parcel_id.
        // This is a temporary fix. The correct solution is to alter the 'report.Parcel_id' column to VARCHAR(10).
        $stmt = $pdo->prepare("SELECT Report_id, Parcel_id, Report, Status FROM report WHERE Student_id = ? ORDER BY Report_id DESC");
        $stmt->execute([$student_id]);
        $reports = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if (count($reports) === 0): ?>
          <p style="color: #888;">You have not submitted any reports yet.</p>
        <?php else: ?>
          <table class="reports-table">
            <thead>
              <tr>
                <th class="report-id-col">Report ID</th>
                <th class="parcel-id-col">Parcel ID</th>
                <th>Report</th>
                <th class="status-col">Status</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($reports as $report): ?>
                <tr>
                  <td class="report-id-col"> <?= htmlspecialchars($report['Report_id']) ?> </td>
                  <td class="parcel-id-col"> <?= htmlspecialchars($report['Parcel_id']) ?> </td>
                  <td> <?= nl2br(htmlspecialchars($report['Report'])) ?> </td>
                  <td class="status-col">
                    <span class="status-<?= $report['Status'] ? 'received' : 'unreceived' ?>">
                      <?= $report['Status'] ? 'Received' : 'Unreceived' ?>
                    </span>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        <?php endif; ?>
      </div>
    </div>
  </div>
</body>
<script src="../js/clock.js" defer></script>
<script src="../js/mousetrailer.js" defer></script>

</html>