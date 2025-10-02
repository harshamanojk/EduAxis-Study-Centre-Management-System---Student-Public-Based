<?php
session_start();
if (!isset($_SESSION['uid'])) {
    header("Location: HOME PAGE.php");
    exit();
}

include('INCLUDES/db.php');
$uid = $_SESSION['uid'];

/* --------------------------
   FETCH USER INFO
--------------------------- */
$thisuser = null;
$stmt = $conn->prepare("SELECT * FROM users WHERE id=?");
$stmt->bind_param("i", $uid);
$stmt->execute();
$thisuser = $stmt->get_result()->fetch_assoc();
$stmt->close();

/* --------------------------
   FETCH LAST BOOKING (LATEST BOOKED SLOT)
--------------------------- */
$bookingData = null;
$stmt = $conn->prepare("
    SELECT b.*, s.start_time, s.end_time
    FROM slotbookings b 
    JOIN slots s ON b.Slot = s.Slot 
    WHERE b.UserID = ? 
    ORDER BY b.created_at DESC, s.start_time DESC 
    LIMIT 1
");
$stmt->bind_param("i", $uid);
$stmt->execute();
$bookingData = $stmt->get_result()->fetch_assoc();
$stmt->close();

/* --------------------------
   FETCH PAYMENT FOR LAST BOOKING
--------------------------- */
$paymentData = null;
if ($bookingData) {
    $BookingID = $bookingData['BookingID'];
    $stmt = $conn->prepare("
        SELECT p.*, s.start_time, s.end_time
        FROM payments p
        JOIN slots s ON p.Slot = s.Slot
        JOIN slotbookings b ON p.BookingID = b.BookingID
        WHERE p.UserID=? AND p.BookingID=? 
        ORDER BY p.created_at DESC 
        LIMIT 1
    ");
    $stmt->bind_param("ii", $uid, $BookingID);
    $stmt->execute();
    $paymentData = $stmt->get_result()->fetch_assoc();
    $stmt->close();
}

/* --------------------------
   FETCH LATEST WAITLIST (if any)
--------------------------- */
$waitlistData = null;
$stmt = $conn->prepare("
    SELECT w.*, s.start_time, s.end_time
    FROM waitlist w 
    JOIN slots s ON w.Slot = s.Slot 
    WHERE w.UserID = ? 
    ORDER BY w.request_date DESC 
    LIMIT 1
");
$stmt->bind_param("i", $uid);
$stmt->execute();
$waitlistData = $stmt->get_result()->fetch_assoc();
$stmt->close();

/* --------------------------
   DECIDE WHICH BOOKING TO SHOW
   Priority: Confirmed booking > Waitlist
--------------------------- */
$displayBooking = $bookingData ?? $waitlistData;

// Helper: format timings
function formatTiming($start, $end) {
    if (!$start || !$end) return "N/A";
    return date("h:i A", strtotime($start)) . " - " . date("h:i A", strtotime($end));
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>STUDENT DASHBOARD | EduAxis</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="CSS/dashboard.css">
    <link rel="icon" type="image/x-icon" href="IMGS/LOGO.ico">
</head>
<body>
    <!-- USER INFO -->
    <div class="user-info text-center">
        <div class="nav-item dropdown">
            <?php if (!empty($thisuser['img'])): ?>
                <img src="<?= htmlspecialchars($thisuser['img']) ?>" alt="User Image" width="40" height="40">
            <?php else: ?>
                <img src="IMGS/USER.jpg" alt="Default User Image" width="40" height="40">
            <?php endif; ?>

            <div class="welcome-msg">
                <span>Welcome,</span><br>
                <strong><?= htmlspecialchars($_SESSION['fullname'] ?? $thisuser['name']) ?></strong>
            </div>

            <div class="dropdown">
                <div class="dropdown-content">
                    <a href="UPDATE_PROFILE.php" target="_blank">Update Details</a>
                    <a href="LOGOUT.php">Log Out</a>
                </div>
            </div>
        </div>
    </div>

    <!-- LOGO -->
    <img src="IMGS/LOGO.jpg" alt="LOGO" 
         style="display:block; margin:5px auto; width:150px; max-width:80vw; height:auto; border-radius:8px">

    <!-- DASHBOARD WELCOME -->
    <div class="dashboard-container text-center">
        <h1>WELCOME,</h1>
        <h1><strong><?= htmlspecialchars($_SESSION['fullname'] ?? $thisuser['name']) ?></strong></h1>
    </div>

    <!-- DASHBOARD CARDS -->
    <div class="cards-wrapper text-center">
        <!-- BOOKING STATUS -->
        <div class="card1 card">
            <h4><b><u>BOOKING STATUS</u></b></h4>
            <?php if($displayBooking): ?>
                <p>SLOT: <?= htmlspecialchars($displayBooking['Slot']) ?></p>
                <p>TIMING: <?= formatTiming($displayBooking['start_time'] ?? null, $displayBooking['end_time'] ?? null) ?></p>
                <p>DATE: <?= htmlspecialchars($displayBooking['BookingDateChosen'] ?? $displayBooking['request_date']) ?></p>
                <p>STATUS: <?= htmlspecialchars($displayBooking['Status']) ?></p>
                <small><em>(This card shows the slot you booked last. To view all your slots, go to MY SLOTS.)</em></small>
            <?php else: ?>
                <p>SLOT: N/A</p>
                <p>TIMINGS: N/A</p>
                <p>DATE: N/A</p>
                <p>STATUS: N/A</p>
            <?php endif; ?>
        </div>

        <!-- PAYMENT STATUS -->
        <div class="card2 card">
            <h4><b><u>PAYMENT STATUS</u></b></h4>
            <?php if($paymentData): ?>
                <p>SLOT: <?= htmlspecialchars($paymentData['Slot'] ?? $displayBooking['Slot'] ?? 'N/A') ?></p>
                <p>TIMING: <?= formatTiming($paymentData['start_time'] ?? null, $paymentData['end_time'] ?? null) ?></p>
                <p>DATE: <?= htmlspecialchars($paymentData['BookingDateChosen'] ?? $displayBooking['BookingDateChosen'] ?? $displayBooking['request_date'] ?? 'N/A') ?></p>
                <p>STATUS: <?= htmlspecialchars($paymentData['payment_status']) ?></p>
                <small><em>(This card shows the payments you paid last. To view all your payments, go to MY PAYMENTS.)</em></small>
            <?php elseif($waitlistData && !$bookingData): ?>
                <p>SLOT: <?= htmlspecialchars($waitlistData['Slot']) ?></p>
                <p>TIMING: <?= formatTiming($waitlistData['start_time'] ?? null, $waitlistData['end_time'] ?? null) ?></p>
                <p>DATE: <?= htmlspecialchars($waitlistData['BookingDateChosen']) ?></p>
                <p>STATUS: Paid</p>
                <small><em>(This card shows the payments you paid last. To view all your payments, go to MY PAYMENTS.)</em></small>
            <?php else: ?>
                <p>SLOT: N/A</p>
                <p>TIMINGS: N/A</p>
                <p>DATE: N/A</p>
                <p>STATUS: N/A</p>
            <?php endif; ?>
        </div>

        <!-- BUTTONS -->
        <div class="card3 card">
            <a href="SLOT_BOOKING.php" target="_blank" class="btn-link">BOOK SLOT</a>
        </div>
        <div class="card4 card">
            <a href="PAYMENT_PORTAL.php" target="_blank" class="btn-link">MAKE PAYMENT</a>
        </div>
        <div class="card5 card">
            <a href="SLOTS.php" target="_blank" class="btn-link">MY SLOTS</a>
        </div>
        <div class="card6 card">
            <a href="PAYMENT.php" target="_blank" class="btn-link">MY PAYMENTS</a>
        </div>
        <div class="card7 card">
            <a href="QR_CODE.php" target="_blank" class="btn-link"> QR CODE </a>
    </div>

    <!-- FOOTER -->
    <footer class="text-center py-4 bg-dark text-light mt-4">
        <p>&copy; 2025 EduAxis. All rights reserved.</p>
    </footer>
</body>
</html>
