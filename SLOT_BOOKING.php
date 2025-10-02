<?php
session_start();
if (!isset($_SESSION['uid'])) {
    header("Location: HOME PAGE.php");
    exit();
}

include('INCLUDES/db.php');
$uid = $_SESSION['uid'];

/* --------------------------
   FETCH USER DETAILS
--------------------------- */
$stmt = $conn->prepare("SELECT name, email, contact FROM users WHERE id=?");
$stmt->bind_param("i", $uid);
$stmt->execute();
$userData = $stmt->get_result()->fetch_assoc();
$stmt->close();

/* --------------------------
   FETCH ALL SLOTS
--------------------------- */
$slots = [];
$result = $conn->query("SELECT * FROM slots ORDER BY start_time ASC");
while ($row = $result->fetch_assoc()) {
    $slots[] = $row;
}

/* --------------------------
   HANDLE FORM SUBMISSION
--------------------------- */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $slot = $_POST['Slot'] ?? '';
    $bookingDate = $_POST['BookingDateChosen'] ?? '';
    $contact = trim($_POST['phone'] ?? '');

    if ($slot && $bookingDate && $contact) {
        $currentDate = date('Y-m-d');

        // ❌ Prevent booking for today
        if ($bookingDate === $currentDate) {
            $error = "All slots are blocked for today. Please choose another date.";
        } else {
            // Save session info for confirmation/payment
            $_SESSION['slot'] = $slot;
            $_SESSION['booking_date'] = $bookingDate;
            $_SESSION['fullname'] = $userData['name'];
            $_SESSION['email'] = $userData['email'];
            $_SESSION['contact'] = $contact;
            $_SESSION['amount'] = 500; // fixed amount

            header("Location: BOOKING_CONFIRM.php");
            exit();
        }
    } else {
        $error = "Please fill all required fields.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>SLOT BOOKING | EduAxis</title>
<link rel="stylesheet" href="CSS/form.css">
<link rel="icon" type="image/x-icon" href="IMGS/LOGO.ico">
</head>
<body>
<div class="slot-container">
    <!-- LOGO -->
    <div style="text-align: center; margin-bottom: 20px;">
        <img src="IMGS/LOGO.jpg" alt="Logo" 
             style="width: 150px; max-width: 80vw; height: auto; border-radius: 8px;">
    </div>

    <h2>SLOT BOOKING</h2>

    <?php if(!empty($error)): ?>
        <p class="text-danger" style="color:red;"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <form action="" method="POST">
        <label>Name:</label>
        <input type="text" name="FullName" 
               value="<?= htmlspecialchars($userData['name'] ?? '') ?>" readonly>

        <label>Email:</label>
        <input type="email" name="Email" 
               value="<?= htmlspecialchars($userData['email'] ?? '') ?>" readonly>

        <label>Contact:</label>
        <input type="text" name="phone" 
               value="<?= htmlspecialchars($userData['contact'] ?? '') ?>" required>

        <label>Choose Date:</label>
        <input type="date" name="BookingDateChosen" required 
               min="<?= date('Y-m-d', strtotime('+1 day')) ?>"><!-- ✅ avoid today -->

        <label>Select Slot:</label>
        <div class="slots-container">
            <?php foreach ($slots as $slotData): ?>
                <label class="slot-box">
                    <input type="radio" name="Slot" 
                           value="<?= htmlspecialchars($slotData['Slot']) ?>" required>
                    <div class="slot-content">
                        <strong><?= htmlspecialchars($slotData['Slot']) ?></strong>
                        <span>
                            <?= date("h:i A", strtotime($slotData['start_time'])) ?>
                            -
                            <?= date("h:i A", strtotime($slotData['end_time'])) ?>
                        </span>
                        <small>Capacity: <?= (int)$slotData['capacity'] ?></small>
                    </div>
                </label>
            <?php endforeach; ?>
        </div>

        <button type="submit" id="bookSlotBtn" name="bookSlotBtn">Book Slot</button>
    </form>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const dateInput = document.querySelector('input[name="BookingDateChosen"]');
    const slotInputs = document.querySelectorAll('input[name="Slot"]');

    function updateSlots() {
        const selectedDate = dateInput.value;
        const today = new Date().toISOString().split('T')[0];

        slotInputs.forEach(input => {
            const slotBox = input.closest(".slot-box");

            // Remove old messages
            const msg = slotBox.querySelector(".slot-disabled-msg");
            if (msg) msg.remove();

            if (selectedDate === today) {
                input.disabled = true;
                slotBox.style.opacity = "0.5";
                slotBox.style.pointerEvents = "none";

                const note = document.createElement("em");
                note.className = "slot-disabled-msg";
                note.style.color = "red";
                note.style.fontSize = "12px";
                note.innerText = "Not available for today";
                slotBox.querySelector(".slot-content").appendChild(note);
            } else {
                input.disabled = false;
                slotBox.style.opacity = "1";
                slotBox.style.pointerEvents = "auto";
            }
        });
    }

    dateInput.addEventListener("change", updateSlots);
});
</script>
</body>
</html>
