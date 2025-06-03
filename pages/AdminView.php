<?php
// Check if the user is logged in (assuming you set a session variable like 'username' or 'loggedin' after login)
if (!isset($_SESSION['username'])) {
    // Not logged in, redirect to login page
    header("Location: Login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    
</body>
</html>