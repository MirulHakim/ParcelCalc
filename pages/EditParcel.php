<?php
session_start();
require_once "../controllers/pdo.php";

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

    $id = $_POST["id"];
    $newOwner = $_POST["new_owner_name"] ?? null;
    $newType = $_POST["new_type"] ?? null;
    $newStatus = $_POST["new_status"] ?? null;
    $newContact = $_POST["new_contact"] ?? null;

    $query = "UPDATE Parcel_info SET 
    Parcel_owner = COALESCE(:owner, Parcel_owner),
    Parcel_type = COALESCE(:type, Parcel_type),
    Status = COALESCE(:status, Status),
    PhoneNum = COALESCE(:contact, PhoneNum)";

    $params = [
        ':owner' => $newOwner ?: null,
        ':type' => $newType ?: null,
        ':status' => $newStatus,
        ':contact' => $newContact ?: null,
        ':id' => $id
    ];

    // If status is set to "1" (Claimed), add Date_received = NOW()
    if ($newStatus === "1") {
        $query .= ", Date_received = NOW()";
    }

    // Handle new image upload
    if (isset($_FILES['new_image']) && $_FILES['new_image']['error'] === UPLOAD_ERR_OK && getimagesize($_FILES['new_image']['tmp_name'])) {
        $newImage = file_get_contents($_FILES['new_image']['tmp_name']);
        $query .= ", Parcel_image = :new_image";
        $params[':new_image'] = $newImage;
    }

    $query .= " WHERE Parcel_id = :id";

    $stmt = $pdo->prepare($query);
    $stmt->execute($params);

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
} elseif (isset($_GET["parcel_id"])) { // Accept parcel_id from query string
    $searchId = $_GET["parcel_id"];
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="icon" type="image/x-icon" href="../resources/favicon.ico" />
    <link rel="stylesheet" href="../css/EditParcel.css" />
    <link rel="stylesheet" href="../css/style.css" />
    <link rel="stylesheet" href="../css/mousetrailer.css" />
    <title>Edit Parcel</title>
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
        <a href="../controllers/logout.php">
            <button class="login-button">LOGOUT</button>
        </a>
        <div id="clock"></div>
    </div>

    <!-- Back button & title -->
    <div class="row">
        <a href="AdminView.php"><img class="back" src="../resources/Login/arrow-back0.svg" /></a>
        <p class="title">EDIT/DELETE PARCEL INFO</p>
    </div>

    <!-- Searchbar Parcel ID -->
    <div class="searchbar-center">
        <form action="" method="post">
            <input class="search" type="text" name="search_id" placeholder="Enter parcel ID" required
                value="<?= htmlspecialchars($searchId ?? '') ?>" />
            <button type="submit" class="btn confirm">Search</button>
        </form>
    </div>

    <?php if ($parcel): ?>
        <!-- Parcel Detail -->
        <div class="edit-parcel-container">
            <!-- Show current image and live preview -->
            <div style="text-align:center; margin-bottom: 20px;">
                <img id="parcel-image-preview"
                    src="<?php echo !empty($parcel['Parcel_image']) ? '../controllers/get_image.php?Parcel_id=' . urlencode($parcel['Parcel_id']) : ''; ?>"
                    alt="Parcel Image"
                    style="max-width:220px; max-height:220px; border-radius:8px; border:2px solid #ccc; <?php echo empty($parcel['Parcel_image']) ? 'display:none;' : ''; ?>" />
                <?php if (empty($parcel['Parcel_image'])): ?>
                    <div id="no-image-placeholder" style="color:#495bbf; font-style:italic;">No Image Available</div>
                <?php endif; ?>
            </div>
            <!-- Update Parcel Form -->
            <form class="edit-parcel-form" method="POST" action="" enctype="multipart/form-data">
                <input type="hidden" name="id" value="<?= htmlspecialchars($parcel['Parcel_id']) ?>" />
                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>" />
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>" />

                <div class="form-grid">
                    <div class="form-group">
                        <label>Owner's Name</label>
                        <input type="text" disabled class="old-owner"
                            value="<?= htmlspecialchars($parcel['Parcel_owner']) ?>" />
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
                            <option value="KOTAK">KOTAK</option>
                            <option value="PUTIH">PUTIH</option>
                            <option value="HITAM">HITAM</option>
                            <option value="KELABU">KELABU</option>
                            <option value="OTHERS">OTHERS</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Owner's Contact Info</label>
                        <input type="text" disabled class="old-owner"
                            value="<?= htmlspecialchars($parcel['PhoneNum']) ?>" />
                        <input type="text" name="new_contact" placeholder="New contact info" />
                    </div>

                    <div class="form-group">
                        <label>Change Parcel Image</label>
                        <input type="file" name="new_image" accept="image/*" id="new-image-input" />
                    </div>
                </div>

                <button type="submit" name="update" class="btn confirm">Confirm</button>
            </form>

            <script>
                const fileInput = document.getElementById('new-image-input');
                const imgPreview = document.getElementById('parcel-image-preview');
                const noImgPlaceholder = document.getElementById('no-image-placeholder');
                const originalImgSrc = imgPreview ? imgPreview.src : '';

                if (fileInput) {
                    fileInput.addEventListener('change', function (e) {
                        if (this.files && this.files[0]) {
                            const reader = new FileReader();
                            reader.onload = function (ev) {
                                if (imgPreview) {
                                    imgPreview.src = ev.target.result;
                                    imgPreview.style.display = 'inline-block';
                                }
                                if (noImgPlaceholder) {
                                    noImgPlaceholder.style.display = 'none';
                                }
                            };
                            reader.readAsDataURL(this.files[0]);
                        } else {
                            if (imgPreview) {
                                imgPreview.src = originalImgSrc;
                                if (!originalImgSrc) imgPreview.style.display = 'none';
                            }
                            if (noImgPlaceholder && !originalImgSrc) {
                                noImgPlaceholder.style.display = 'block';
                            }
                        }
                    });
                }
            </script>

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

    <div class="trademark">
        Trademark ® 2025 Parcel Serumpun. All Rights Reserved
    </div>

    <script>
        <?php if (!empty($successMessage)): ?>
            window.successMsg = <?= json_encode($successMessage) ?>;
        <?php endif; ?>
    </script>
</body>
<script src="../js/clock.js" defer></script>
<script src="../js/mousetrailer.js" defer></script>
<script src="../js/formAlerts.js" defer></script>

</html>