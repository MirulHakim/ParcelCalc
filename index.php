<?php
require_once "pages/pdo.php";

$successMsg = '';
$errorMsg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $student_id = trim($_POST['student_id'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm'] ?? '';

    if (!$name || !$student_id || !$phone || !$password || !$confirm) {
        $errorMsg = 'All fields are required.';
    } elseif ($password !== $confirm) {
        $errorMsg = 'Passwords do not match.';
    } else {
        // Check if student_id already exists
        $stmt = $pdo->prepare("SELECT * FROM student WHERE Student_id = :id");
        $stmt->execute([':id' => $student_id]);
        if ($stmt->fetch()) {
            $errorMsg = 'Student ID already registered.';
        } else {
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO student (Student_id, Name_student, PhoneNum, Password) VALUES (:id, :name, :phone, :pass)");
            try {
                $stmt->execute([
                    ':id' => $student_id,
                    ':name' => $name,
                    ':phone' => $phone,
                    ':pass' => $hashed
                ]);
                $successMsg = 'Registration successful! You can now log in.';
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
    <title>Student Registration</title>
    <link rel="icon" type="image/x-icon" href="resources/favicon.ico" />
    <link rel="stylesheet" href="css/style.css" />
    <link rel="stylesheet" href="css/index.css" />
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
    </div>

    <!-- Main content below header -->
    <div class="main-content">
        <div class="register-card">
            <h2>Student Registration</h2>
            <?php if ($successMsg): ?>
                <div class="success-message"><?= htmlspecialchars($successMsg) ?></div>
            <?php endif; ?>
            <?php if ($errorMsg): ?>
                <div class="error-message"><?= htmlspecialchars($errorMsg) ?></div>
            <?php endif; ?>
            <form method="POST" action="">
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
                <button type="submit" class="add-button">Register</button><br>
            </form>
            <div style="text-align:center; margin-top: 18px; color: #495bbf;">
                Already have an account?
                <a href="pages/Login.php"
                    style="color:#fff; background:#495bbf; padding:10px 24px; border-radius:8px; font-weight:600; text-decoration:none; margin-left:8px; display:inline-block;">Login</a>
            </div>
        </div>
    </div>

    <div class="trademark">
        Trademark ® 2025 Parcel Serumpun. All Rights Reserved
    </div>
</body>

</html>