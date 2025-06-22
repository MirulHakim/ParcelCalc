<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
    header('Location: Login.php');
    exit();
}
require_once "pdo.php";

// Generate CSRF token if not already set
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$successMsg = '';
$errorMsg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // CSRF check
    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        die("Invalid CSRF token.");
    }
    $staff_id = trim($_POST['staff_id'] ?? '');
    $name = trim($_POST['name'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm'] ?? '';

    if (!$staff_id || !$name || !$phone || !$password || !$confirm) {
        $errorMsg = 'All fields are required.';
    } elseif ($password !== $confirm) {
        $errorMsg = 'Passwords do not match.';
    } else {
        // Check if staff_id already exists
        $stmt = $pdo->prepare("SELECT * FROM staff WHERE Staff_id = :id");
        $stmt->execute([':id' => $staff_id]);
        if ($stmt->fetch()) {
            $errorMsg = 'Staff ID already registered.';
        } else {
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO staff (Staff_id, Name_staff, PhoneNum_staff, Password) VALUES (:id, :name, :phone, :pass)");
            try {
                $stmt->execute([
                    ':id' => $staff_id,
                    ':name' => $name,
                    ':phone' => $phone,
                    ':pass' => $hashed
                ]);
                $successMsg = 'Admin registration successful!';
            } catch (PDOException $e) {
                $errorMsg = 'Registration failed: ' . $e->getMessage();
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Admin Registration</title>
    <link rel="icon" type="image/x-icon" href="../resources/favicon.ico" />
    <link rel="stylesheet" href="../css/style.css" />
    <link rel="stylesheet" href="../css/Login.css" />
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
        </div>
        <div id="clock"></div>
    </div>
    <a href="AdminView.php"><img class="back" src="../resources/Login/arrow-back0.svg" /></a>
    <div class="content">
        <p class="parcel mid">Admin Registration</p>
        <form method="POST" action="">
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
            <div class="login-wrap">
                <input class="login" type="text" id="staff_id" name="staff_id" placeholder="Staff ID" required /><br />
                <input class="login" type="text" id="name" name="name" placeholder="Full Name" required /><br />
                <input class="login" type="text" id="phone" name="phone" placeholder="Phone Number" required /><br />
                <input class="login" type="password" id="password" name="password" placeholder="Password"
                    required /><br />
                <input class="login" type="password" id="confirm" name="confirm" placeholder="Confirm Password"
                    required /><br />
                <input class="submit" type="submit" value="Register" />
            </div>
        </form>
    </div>
    <div class="trademark">
        Trademark ® 2025 Parcel Serumpun. All Rights Reserved
    </div>

    <script>
        <?php if (!empty($successMsg)): ?>
            window.successMsg = <?= json_encode($successMsg) ?>;
        <?php endif; ?>
        <?php if (!empty($errorMsg)): ?>
            window.errorMsg = <?= json_encode($errorMsg) ?>;
        <?php endif; ?>
    </script>
</body>
<script src="../js/clock.js" defer></script>
<script src="../js/mousetrailer.js" defer></script>
<script src="../js/formAlerts.js" defer></script>

</html>