<?php
session_start();
if (!isset($_SESSION['uid'])) {
    header('Location: HOME PAGE.php');
    exit();
}
include('INCLUDES/db.php');

$thisuser = null;
$uid = $_SESSION['uid'];
$res = mysqli_query($conn, "SELECT * FROM `users` WHERE id='$uid'");
if ($res && mysqli_num_rows($res) > 0) {
    $thisuser = mysqli_fetch_assoc($res);
} else {
    header('Location: HOME PAGE.php');
    exit();
}

$msg = "";

// Update profile picture only
if (isset($_POST['updateAccount'])) {
    if (empty($_FILES['img']['name'])) {
        $msg = "Nothing to update! _danger";
    } else {
        // Handle image upload
        $target_dir = "IMGS/";
        $filename = basename($_FILES["img"]["name"]);
        $target_file = $target_dir . time() . "_" . preg_replace('/\s+/', '_', $filename);
        $uploadOk = 1;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        $check = getimagesize($_FILES["img"]["tmp_name"]);
        if ($check === false) {
            $msg = "File is not an image! _danger";
            $uploadOk = 0;
        }

        if ($uploadOk == 1) {
            if (move_uploaded_file($_FILES["img"]["tmp_name"], $target_file)) {
                $upq = mysqli_query($conn, "
                    UPDATE `users` 
                    SET `img`='$target_file' 
                    WHERE id = '{$thisuser['id']}'
                ");
                if ($upq) {
                    $msg = "Profile picture updated successfully! _success";
                    echo "<script>location.replace('UPDATE_PROFILE.php');</script>";
                    exit();
                } else {
                    $msg = "Unknown Error! _danger";
                }
            } else {
                $msg = "Sorry, there was an error uploading your file! _danger";
            }
        }
    }
}

// Change password
if (isset($_POST['changePassword'])) {
    $old = $_POST['old'];
    $new = $_POST['new'];
    $confirm = $_POST['confirm'];

    if (password_verify($old, $thisuser['password'])) {
        if ($new === $confirm) {
            $hash = password_hash($new, PASSWORD_DEFAULT);
            $upq = mysqli_query($conn, "UPDATE `users` SET `password`='$hash' WHERE id='{$thisuser['id']}'");
            $msg = $upq ? "Password changed successfully! _success" : "Unknown Error! _danger";
        } else {
            $msg = "Passwords do not match! _danger";
        }
    } else {
        $msg = "Incorrect old password! _danger";
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>UPDATE PROFILE | EduAxis</title>
    <link rel="icon" type="image/x-icon" href="IMGS/LOGO.ico">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous" />
    <style>
        body {
            background: #f8f9fa;
        }
        .card {
            box-shadow: 0 4px 15px rgb(0 0 0 / 0.1);
            border-radius: 8px;
        }
        #profile-img {
            max-width: 150px;
            max-height: 150px;
            object-fit: cover;
            border-radius: 50%;
            display: block;
            margin: 0 auto;
        }
        .img {
            text-align: center;
            margin-top: 5px;
        }
        .img img {
            width: 150px;
            max-width: 80vw; /* don't overflow viewport */
            height: auto;
            border-radius: 8px;
        }
    </style>
</head>

<body>
    <div class="img">
        <img src="IMGS/LOGO.jpg" alt="Logo">
    </div>
    <div class="container py-5">
        <div class="card mx-auto" style="max-width: 700px;">
            <div class="card-body">
                <h3 class="text-center mb-4">Update Profile</h3>

                <!-- Update Profile Form -->
                <form action="UPDATE_PROFILE.php" method="POST" enctype="multipart/form-data">
                    <div class="row align-items-center mb-3">
                        <div class="col-md-6">
                            <label for="img" class="form-label">Profile Image</label>
                            <input type="file" name="img" id="img" accept="image/*" class="form-control" onchange="showPreview(event);" />
                        </div>
                        <div class="col-md-6 text-center">
                            <img src="<?= htmlspecialchars($thisuser['img'] ?: 'IMGS/USER.jpg') ?>" id="profile-img" alt="Profile Image" />
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Full Name</label>
                        <input type="text" class="form-control" value="<?= htmlspecialchars($thisuser['name']) ?>" disabled>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" class="form-control" value="<?= htmlspecialchars($thisuser['email']) ?>" disabled>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Contact</label>
                        <input type="text" class="form-control" value="<?= htmlspecialchars($thisuser['contact']) ?>" disabled>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Institution Type</label>
                        <input type="text" class="form-control" value="<?= htmlspecialchars($thisuser['institution_type']) ?>" disabled>
                    </div>

                    <?php if ($thisuser['institution_type'] === "School"): ?>
                        <div class="mb-3">
                            <label class="form-label">School Name</label>
                            <input type="text" class="form-control" value="<?= htmlspecialchars($thisuser['school_name']) ?>" disabled>
                        </div>
                    <?php elseif ($thisuser['institution_type'] === "University"): ?>
                        <div class="mb-3">
                            <label class="form-label">University Name</label>
                            <input type="text" class="form-control" value="<?= htmlspecialchars($thisuser['university_name']) ?>" disabled>
                        </div>
                    <?php endif; ?>

                    <div class="d-flex justify-content-between">
                        <a href="STUDENT_DASHBOARD.php" class="btn btn-secondary">Cancel</a>
                        <button name="updateAccount" type="submit" class="btn btn-primary">Update</button>
                    </div>
                </form>

                <hr class="my-4" />

                <!-- Change Password Form -->
                <h5 class="text-center">Change Password</h5>
                <form action="UPDATE_PROFILE.php" method="POST">
                    <div class="mb-3">
                        <label for="old" class="form-label">Old Password</label>
                        <input type="password" id="old" name="old" required class="form-control">
                    </div>
                    <div class="mb-3">
                        <label for="new" class="form-label">New Password</label>
                        <input type="password" id="new" name="new" required class="form-control">
                    </div>
                    <div class="mb-3">
                        <label for="confirm" class="form-label">Confirm New Password</label>
                        <input type="password" id="confirm" name="confirm" required class="form-control">
                    </div>
                    <div class="text-end">
                        <button name="changePassword" type="submit" class="btn btn-warning">Change Password</button>
                    </div>
                </form>

                <hr class="my-4" />

                <!-- Delete Account -->
                <form action="DELETE_ACCOUNT.php" method="post" class="text-center">
                    <input type="hidden" name="action" value="delete" />
                    <button type="submit" class="btn btn-danger">Delete My Account</button>
                </form>

                <!-- Alerts -->
                <?php if (!empty($msg)) {
                    list($text, $type) = explode(' _', $msg);
                    $alertType = ($type === 'success') ? 'alert-success' : 'alert-danger';
                ?>
                    <div class="alert <?= $alertType ?> mt-3" role="alert">
                        <?= htmlspecialchars($text) ?>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>

    <script>
        function showPreview(event) {
            if (event.target.files.length > 0) {
                var src = URL.createObjectURL(event.target.files[0]);
                var preview = document.getElementById("profile-img");
                preview.src = src;
                preview.style.display = "block";
            }
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
</body>
</html>
