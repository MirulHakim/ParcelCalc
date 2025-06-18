<?php
session_start();
require_once "pdo.php";

// Generate CSRF token if not already generated
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// CSRF token setup
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Initialize variables
$parcel = null;
$successMessage = null;

// Handle parcel deletion
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["delete"])) {
    // CSRF check
    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        die("Invalid CSRF token.");
    }

    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        die("Invalid CSRF token.");
    }

    $idToDelete = $_POST["id"] ?? null;

    if ($idToDelete) {
        $stmt = $pdo->prepare("DELETE FROM Parcel_info WHERE Parcel_id = :id");
        $stmt->execute([':id' => $idToDelete]);

        $_SESSION['success'] = "Parcel deleted successfully.";

        // Clear data
        $parcel = null;
        $searchId = null;
    }

    // Redirect to avoid resubmission
    header("Location: EditParcel.php");
    header("Location: AdminView.php");
    exit;
}

// Handle parcel update
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["update"])) {
    // CSRF check
    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        die("Invalid CSRF token.");
    }

    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        die("Invalid CSRF token.");
    }

    $id = $_POST["id"];
    $newOwner = $_POST["new_owner_name"] ?? null;
    $newType = $_POST["new_type"] ?? null;
    $newStatus = $_POST["new_status"] ?? null;
    $newContact = $_POST["new_contact"] ?? null;

    $stmt = $pdo->prepare("UPDATE Parcel_info SET 
        Parcel_owner = COALESCE(:owner, Parcel_owner),
        Parcel_type = COALESCE(:type, Parcel_type),
        Status = COALESCE(:status, Status),
        PhoneNum = COALESCE(:contact, PhoneNum)
        WHERE Parcel_id = :id");

    $stmt->execute([
        ':owner' => $newOwner ?: null,
        ':type' => $newType ?: null,
        ':status' => $newStatus !== '' ? $newStatus : null,
        ':contact' => $newContact ?: null,
        ':id' => $id
    ]);

    $_SESSION['success'] = 'Parcel updated successfully.';

    header("Location: EditParcel.php?search_id=" . urlencode($id));
    header("Location: AdminView.php");
    exit;
}

// Handle parcel search from POST or GET
$searchId = null;
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["search_id"])) {
    $searchId = $_POST["search_id"];
} elseif (isset($_GET["search_id"])) {
    $searchId = $_GET["search_id"];
}

if ($searchId) {
    $stmt = $pdo->prepare("SELECT * FROM Parcel_info WHERE Parcel_id = :id");
    $stmt->execute([':id' => $searchId]);
    $parcel = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$parcel) {
    $_SESSION['success'] = "No parcel found with ID: " . htmlspecialchars($searchId);
    header("Location: AdminView.php");
    exit;
}
}

// Get success message from session
if (isset($_SESSION['success'])) {
    $successMessage = $_SESSION['success'];
    unset($_SESSION['success']);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <link rel="icon" type="image/x-icon" href="../resources/favicon.ico" />
    <link rel="stylesheet" href="../css/EditParcel.css" />
    <link rel="stylesheet" href="../css/style.css" />
    <title>Parcel Serumpun - Edit Parcel</title>
</head>
<body>

<!-- Header logo -->
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
</div>

<!-- Back button & title -->
<div class="row">
    <a onclick="history.back()"><img class="back" src="../resources/Login/arrow-back0.svg" /></a>
    <p class="title">EDIT/DELETE PARCEL INFO</p>
</div>

<!-- Show success message if any -->
<?php if ($successMessage): ?>
    <script>alert("<?= htmlspecialchars($successMessage) ?>");</script>
<?php endif; ?>

<!-- Searchbar Parcel ID -->
<form action="" method="post">
    <input class="search" type="text" name="search_id" placeholder="Enter parcel ID" required
        value="<?= htmlspecialchars($searchId ?? '') ?>" />
    <button type="submit" class="btn confirm">Search</button>
</form>

<?php if ($parcel): ?>
<!-- Parcel Detail -->
<div class="edit-parcel-container">

    <!-- Update Parcel Form -->
    <form class="edit-parcel-form" method="POST" action="">
        <input type="hidden" name="id" value="<?= htmlspecialchars($parcel['Parcel_id']) ?>" />
        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>" />
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>" />

        <div class="form-grid">
            <div class="form-group">
                <label>Owner's Name</label>
                <input type="text" disabled class="old-owner" value="<?= htmlspecialchars($parcel['Parcel_owner']) ?>"  />
                <input type="text" name="new_owner_name" placeholder="New Owner's name" />
            </div>

            <div class="form-group">
                <label>Parcel Status</label>
                <select name="new_status">
                    <option value="">Select Status</option>
                    <option value="0" <?= $parcel['Status'] === '0' ? 'selected' : '' ?>>Not claimed</option>
                    <option value="1" <?= $parcel['Status'] === '1' ? 'selected' : '' ?>>Claimed</option>
                </select>
            </div>

            <div class="form-group">
                <label>Parcel Type</label>
                <select disabled class="old-owner">
                    <option><?= htmlspecialchars($parcel['Parcel_type']) ?></option>
                </select>
                <select name="new_type">
                    <option value="">Select new type</option>
                    <option value="kotak">KOTAK</option>
                    <option value="putih">PUTIH</option>
                    <option value="hitam">HITAM</option>
                    <option value="kelabu">KELABU</option>
                    <option value="others">OTHERS</option>
                </select>
            </div>

            <div class="form-group">
                <label>Owner's Contact Info</label>
                <input type="text" disabled class="old-owner" value="<?= htmlspecialchars($parcel['PhoneNum']) ?>"  />
                <input type="text" name="new_contact" placeholder="New contact info" />
            </div>
        </div>

        <button type="submit" name="update" class="btn confirm">Confirm</button>
    </form>

    <!-- Delete Parcel Form -->
    <form method="POST" action="" onsubmit="return confirm('Are you sure you want to delete this parcel?');">
        <input type="hidden" name="id" value="<?= htmlspecialchars($parcel['Parcel_id']) ?>" />
        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>" />
        <input type="hidden" name="delete" value="1" />
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>" />
        <button type="submit" class="btn delete">Delete</button>
    </form>

</div>
<?php endif; ?>

<footer class="trademark">
    Trademark ® 2025 Parcel Serumpun. All Rights Reserved
</footer>
</body>
</html>
