<?php
session_start();

if (isset($_SESSION['uid'])) {
    header('Location: LOGIN_FORM.php');
    exit();
}

// Database connection
$servername = "localhost";
$username = "root";
$password_db = ""; // Change this to your database password
$dbname = "eduaxis";

$conn = new mysqli($servername, $username, $password_db, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Variables
$email = $password = $contact = $name = $institution_type = $school_name = $university_name = "";
$erroremail = $errorpassword = $errorcontact = $errorname = $errorinstitution = "";
$notification = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = test_input($_POST["txtname"]);
    $email = test_input($_POST["txtemail"]);
    $password = test_input($_POST["txtpassword"]);
    $contact = test_input($_POST["txtcontact"]);
    $institution_type = test_input($_POST["institution_type"]);
    $school_name = !empty($_POST["school_name"]) ? test_input($_POST["school_name"]) : null;
    $university_name = !empty($_POST["university_name"]) ? test_input($_POST["university_name"]) : null;

    // Validation
    if (empty($name)) $errorname = "* Name is required";
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) $erroremail = "* Valid email required";
    if (empty($password) || strlen($password) < 8) $errorpassword = "* Password must be at least 8 characters";
    if (empty($contact)) $errorcontact = "* Contact number is required";
    if (empty($institution_type)) {
        $errorinstitution = "* Please select an institution type";
    } elseif ($institution_type == "Other") {
        $errorinstitution = "* Only School or University students are allowed to register";
    }

    // If no errors
    if ($errorname == "" && $erroremail == "" && $errorpassword == "" && $errorcontact == "" && $errorinstitution == "") {
        $checkStmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $checkStmt->bind_param("s", $email);
        $checkStmt->execute();
        $checkStmt->store_result();

        if ($checkStmt->num_rows > 0) {
            $notification = "❌ Account already exists with this email!";
        } else {
            $password_hashed = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO users (name, email, password, contact, institution_type, school_name, university_name) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("sssssss", $name, $email, $password_hashed, $contact, $institution_type, $school_name, $university_name);

            if ($stmt->execute()) {
                echo "<script>alert('Registration successful! Please login.'); window.location='LOGIN_FORM.php';</script>";
                exit();
            } else {
                $notification = "❌ Error: " . $stmt->error;
            }
            $stmt->close();
        }
        $checkStmt->close();
    }
}

function test_input($data) {
    return htmlspecialchars(stripslashes(trim($data)));
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>REGISTER | EduAxis</title>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
<link rel="stylesheet" href="CSS/access.css">
<link rel="icon" type="image/x-icon" href="IMGS/LOGO.ico">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.3/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
</head>
<body>

<div class="container">
    <div class="form-container">
        <div class="img text-center">
            <img src="IMGS/LOGO.jpg" alt="Logo">
        </div>
        <h2 class="text-center"><u><b>EDUAXIS</b></u></h2>
        <p class="text-center" style="font-size: 18px;"><u>REGISTER</u></p>

        <?php if ($notification): ?>
            <div class="alert alert-danger text-center">
                <?= $notification ?>
            </div>
        <?php endif; ?>

        <form method="post" action="<?= htmlspecialchars($_SERVER["PHP_SELF"]) ?>">
            <div class="form-group">
                <input type="text" class="form-control" name="txtname" placeholder="Full Name" value="<?= htmlspecialchars($name) ?>">
                <span class="error"><?= $errorname ?></span>
            </div>
            <div class="form-group">
                <input type="email" class="form-control" name="txtemail" placeholder="Email" value="<?= htmlspecialchars($email) ?>">
                <span class="error"><?= $erroremail ?></span>
            </div>
            <div class="form-group">
                <input type="password" class="form-control" name="txtpassword" placeholder="Password">
                <span class="error"><?= $errorpassword ?></span>
            </div>
            <div class="form-group">
                <input type="tel" class="form-control" name="txtcontact" placeholder="Contact Number" value="<?= htmlspecialchars($contact) ?>">
                <span class="error"><?= $errorcontact ?></span>
            </div>

            <!-- Institution Dropdown -->
            <div class="form-group">
                <select name="institution_type" id="institution_type" class="form-control" onchange="toggleInstitutionFields()">
                    <option value="">-- Select Institution Type --</option>
                    <option value="School" <?= ($institution_type == "School") ? "selected" : "" ?>>School</option>
                    <option value="University" <?= ($institution_type == "University") ? "selected" : "" ?>>University</option>
                    <option value="Other" <?= ($institution_type == "Other") ? "selected" : "" ?>>Other</option>
                </select>
                <span class="error"><?= $errorinstitution ?></span>
            </div>

            <!-- School name -->
            <div class="form-group" id="schoolField" style="display:none;">
                <input type="text" class="form-control" name="school_name" placeholder="School Name" value="<?= htmlspecialchars($school_name) ?>">
            </div>

            <!-- University name -->
            <div class="form-group" id="universityField" style="display:none;">
                <input type="text" class="form-control" name="university_name" placeholder="University Name" value="<?= htmlspecialchars($university_name) ?>">
            </div>

            <button type="submit" class="btn btn-primary btn-block" name="btn-signup">Sign Up</button>
        </form>
    </div>
</div>

<footer class="text-center py-4 bg-dark text-light">
    <p>&copy; 2025 EduAxis. All rights reserved.</p>
</footer>

<script>
function toggleInstitutionFields() {
    var type = document.getElementById("institution_type").value;
    document.getElementById("schoolField").style.display = (type === "School") ? "block" : "none";
    document.getElementById("universityField").style.display = (type === "University") ? "block" : "none";
}
window.onload = toggleInstitutionFields;
</script>

</body>
</html>
