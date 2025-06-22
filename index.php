<?php
require_once "pages/pdo.php";

session_start();
// Generate CSRF token if not already set
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$successMsg = '';
$errorMsg = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // CSRF token check
    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        die("Invalid CSRF token.");
    }

    $name = trim($_POST['name'] ?? '');
    $student_id = trim($_POST['student_id'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm'] ?? '';

    // Validation
    if (empty($name) || empty($student_id) || empty($phone) || empty($password)) {
        $errorMsg = "All fields are required.";
    } elseif ($password !== $confirm) {
        $errorMsg = "Passwords do not match.";
    } elseif (strlen($password) < 6) {
        $errorMsg = "Password must be at least 6 characters long.";
    } else {
        // Check if student ID already exists
        $stmt = $pdo->prepare("SELECT Student_id FROM student WHERE Student_id = ?");
        $stmt->execute([$student_id]);

        if ($stmt->fetch()) {
            $errorMsg = "Student ID already exists. Please use a different ID or login instead.";
        } else {
            // Hash the password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // Insert new student
            try {
                $stmt = $pdo->prepare("INSERT INTO student (Student_id, Name_student, PhoneNum, password) VALUES (?, ?, ?, ?)");
                $stmt->execute([$student_id, $name, $phone, $hashed_password]);

                $successMsg = "Registration successful! You can now login.";

                // Clear form data after successful registration
                $_POST = array();
            } catch (PDOException $e) {
                $errorMsg = "Registration failed: " . $e->getMessage();
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
    <title>Student Registration</title>
    <link rel="icon" type="image/x-icon" href="resources/favicon.ico" />
    <link rel="stylesheet" href="css/style.css" />
    <link rel="stylesheet" href="css/index.css" />
    <link rel="stylesheet" href="css/mousetrailer.css" />
</head>

<body>
    <div class="header">
        <div class="row" style="gap: 0px">
            <div class="box blue" style="position: relative; z-index: 0"></div>
            <div class="box trapezium" style="position: relative; z-index: 1"></div>
            <div class="row logos">
                <img class="logo" src="resources/Header/image-10.png" />
                <div class="x">X</div>
                <img class="logo" src="resources/Header/logo-k-14-10.png" />
            </div>
        </div>
        <div id="clock"></div>
    </div>

    <!-- Main content below header -->
    <div class="main-content">
        <div class="register-card">
            <h2>Student Registration</h2>
            <form method="POST" action="">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                <label for="name">Full Name</label><br>
                <input type="text" id="name" name="name" required /><br>
                <label for="student_id">Student ID</label><br>
                <input type="text" id="student_id" name="student_id" required /><br>
                <label for="phone">Phone Number</label><br>
                <input type="text" id="phone" name="phone" required /><br>
                <label for="password">Password</label><br>
                <input type="password" id="password" name="password" required /><br>
                <label for="confirm">Confirm Password</label><br>
                <input type="password" id="confirm" name="confirm" required /><br>
                <button type="submit" class="add-button" style="width: 100%;">Register</button><br>
            </form>
            <div style="text-align:center; margin-top: 18px; color: #495bbf;">
                Already have an account?
                <a href="pages/Login.php" class="login-link-btn">Login</a>
            </div>
        </div>
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
<script src="js/clock.js" defer></script>
<script src="js/mousetrailer.js" defer></script>
<script src="js/formAlerts.js" defer></script>

</html>