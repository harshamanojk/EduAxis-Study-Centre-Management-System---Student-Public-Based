<?php
session_start();
include('INCLUDES/db.php');

if (!isset($_SESSION['uid'])) {
    header('Location: SLOT_BOOKING.php');
    exit();
}

$uid = $_SESSION['uid'];
$slot = $_SESSION['slot'] ?? '';
$bookingDate = $_SESSION['booking_date'] ?? '';
$fullname = $_SESSION['fullname'] ?? '';
$email = $_SESSION['email'] ?? '';
$amount = $_SESSION['amount'] ?? 500;

if (!$slot || !$bookingDate) {
    die("Missing booking information. Go back and try again.");
}

// Ensure waitlist flag is cleared
$_SESSION['waitlist_pending'] = false;
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>CONFIRM BOOKING | EduAxis</title>
    <link rel="icon" type="image/x-icon" href="IMGS/LOGO.ico">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container text-center mt-5">
    <img src="IMGS/LOGO.jpg" alt="Logo" class="mb-4" style="width:150px; border-radius:8px;">

    <h2>Slot Booking Confirmation</h2>
    <p class="text-danger fw-bold">
        Note: <br>
        Dear <strong><?= htmlspecialchars($fullname) ?></strong>,<br>
        Payment is <u>non-refundable</u> if you cancel a slot. 
        Instead book another slot at your convenient time.
    </p>

    <p><b>Slot:</b> <?= htmlspecialchars($slot) ?></p>
    <p><b>Date:</b> <?= htmlspecialchars($bookingDate) ?></p>
    <p><b>Amount:</b> ₹<?= htmlspecialchars($amount) ?></p>

    <hr class="my-4">

    <h4>Would you like to continue?</h4>
    <div class="d-flex justify-content-center gap-3 mt-3">
        <form method="post" action="PAYMENT_PORTAL.php">
            <button type="submit" class="btn btn-success px-4 py-2 fs-5">✅ Proceed to Payment</button>
        </form>
        <a href="SLOT_BOOKING.php" class="btn btn-danger px-4 py-2 fs-5">❌ Cancel & Go Back</a>
    </div>
</div>
</body>
</html>
