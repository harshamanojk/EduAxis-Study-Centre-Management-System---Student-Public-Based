<?php
session_start();
require 'vendor/autoload.php';
include('INCLUDES/db.php');

use Stripe\Stripe;
use Stripe\PaymentIntent;

// Ensure user is logged in
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
$isWaitlistPayment = $_SESSION['waitlist_pending'] ?? false;
$contactNumber = $_SESSION['contact'] ?? '';

if (!$slot || !$bookingDate) {
    die("Missing booking information. Go back and try again.");
}

// Stripe setup
Stripe::setApiKey('');// ADD SECRET KEY

$paymentIntent = PaymentIntent::create([
    'amount' => $amount * 100,
    'currency' => 'inr',
    'metadata' => [
        'user_id' => $uid,
        'slot' => $slot,
        'booking_date' => $bookingDate
    ]
]);

$clientSecret = $paymentIntent->client_secret;

// Handle AJAX POST from Stripe
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['payment_status'])) {
    $status = $_POST['payment_status'] === 'succeeded' ? 'Paid' : 'Payment Failed';

    if ($status === 'Paid') {

        if (!$isWaitlistPayment) {
            // --- Normal Booking ---
            $stmt = $conn->prepare("INSERT INTO slotbookings (UserID, name, email, Slot, BookingDateChosen, Status) VALUES (?,?,?,?,?,?)");
            $bookingStatus = 'Slot Booked';
            $stmt->bind_param("isssss", $uid, $fullname, $email, $slot, $bookingDate, $bookingStatus);
            if (!$stmt->execute()) {
                die("SlotBooking Insert Error: " . $stmt->error);
            }
            $bookingID = $stmt->insert_id;
            $stmt->close();

            $stmt = $conn->prepare("INSERT INTO payments (UserID, BookingID, Slot, BookingDateChosen, name, email, amount, payment_status, created_at) VALUES (?,?,?,?,?,?,?,?,NOW())");
            $stmt->bind_param("iissssds", $uid, $bookingID, $slot, $bookingDate, $fullname, $email, $amount, $status);
            if (!$stmt->execute()) {
                die("Payment Insert Error: " . $stmt->error);
            }
            $stmt->close();

        } else {
            // --- Waitlist Booking ---
            $stmt = $conn->prepare("INSERT INTO waitlist (UserID, Slot, BookingDateChosen, Status) VALUES (?,?,?,?)");
            $waitlistStatus = 'Waiting';
            $stmt->bind_param("isss", $uid, $slot, $bookingDate, $waitlistStatus);
            if (!$stmt->execute()) {
                die("Waitlist Insert Error: " . $stmt->error);
            }
            $stmt->close();

            // For waitlist payments, BookingID cannot be NULL due to FK; set 0
            $stmt = $conn->prepare("INSERT INTO payments (UserID, BookingID, Slot, BookingDateChosen, name, email, amount, payment_status, created_at) VALUES (?,?,?,?,?,?,?,?,NOW())");
            $bookingIDForWaitlist = 0;
            $stmt->bind_param("iissssds", $uid, $bookingIDForWaitlist, $slot, $bookingDate, $fullname, $email, $amount, $status);
            if (!$stmt->execute()) {
                die("Waitlist Payment Insert Error: " . $stmt->error);
            }
            $stmt->close();
        }

        // Clear session variables
        unset($_SESSION['slot'], $_SESSION['booking_date'], $_SESSION['amount'], $_SESSION['waitlist_pending'], $_SESSION['contact']);
    }

    echo json_encode(['success' => true]);
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>PAYMENT PORTAL | EduAxis</title>
<link rel="stylesheet" href="CSS/payment.css">
<link rel="icon" type="image/x-icon" href="IMGS/LOGO.ico">
<script src="https://js.stripe.com/v3/"></script>
</head>
<body>
<div class="payment-container">
    <div style="text-align: center; margin-bottom: 20px;">
        <img src="IMGS/LOGO.jpg" alt="Logo" style="width: 150px; max-width: 80vw; height: auto; border-radius: 8px;">
    </div>
    <h2>PAYMENT PORTAL</h2>

    <form id="payment-form">
        <label>Name:</label>
        <input type="text" value="<?= htmlspecialchars($fullname) ?>" readonly>
        <label>Email:</label>
        <input type="email" value="<?= htmlspecialchars($email) ?>" readonly>
        <label>Amount (₹):</label>
        <input type="number" value="<?= $amount ?>" readonly>
        <label>Card Details:</label>
        <div id="card-element"></div>
        <button type="submit" id="payNowBtn">Pay Now</button>
    </form>

    <div id="payment-result"></div>
    <button id="back-dashboard" style="display:none;" onclick="window.location.href='STUDENT_DASHBOARD.php'">
        Back to Dashboard
    </button>
</div>

<script>
const stripe = Stripe(''); // ADD PUBLISHABLE KEY
const elements = stripe.elements();
const card = elements.create('card', {hidePostalCode: true});
card.mount('#card-element');

const form = document.getElementById('payment-form');
const resultDiv = document.getElementById('payment-result');
const backBtn = document.getElementById('back-dashboard');

form.addEventListener('submit', async (e) => {
    e.preventDefault();
    resultDiv.style.color = '#0d6efd';
    resultDiv.textContent = "Processing payment...";

    const { paymentIntent, error } = await stripe.confirmCardPayment('<?= $clientSecret ?>', {
        payment_method: { 
            card: card, 
            billing_details: { 
                name: '<?= $fullname ?>', 
                email: '<?= $email ?>' 
            } 
        }
    });

    const status = paymentIntent ? paymentIntent.status : 'failed';

    await fetch('PAYMENT_PORTAL.php', {
        method: 'POST',
        headers: {'Content-Type':'application/x-www-form-urlencoded'},
        body: new URLSearchParams({payment_status: status})
    });

    if(error){
        resultDiv.style.color='red';
        resultDiv.textContent="❌ Payment Failed: " + error.message;
    } else {
        resultDiv.style.color='green';
        resultDiv.textContent="✅ Payment Successful!";
        form.style.display='none';
        backBtn.style.display='block';
    }
});
</script>
</body>
</html>
