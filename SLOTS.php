<?php
session_start();
if (!isset($_SESSION['uid'])) {
    header('Location: STUDENT_DASHBOARD.php');
    exit();
}
include('INCLUDES/db.php');

$uid = $_SESSION['uid'];
$message = "";

// Handle delete request
if (isset($_POST['delete']) && isset($_POST['bookingId'])) {
    $bookingId = intval($_POST['bookingId']);
    $stmt = $conn->prepare("DELETE FROM slotbookings WHERE BookingID=? AND UserID=?");
    $stmt->bind_param("ii", $bookingId, $uid);

    if ($stmt->execute()) {
        $message = "✅ Slot deleted successfully.";
    } else {
        $message = "❌ Error deleting slot.";
    }
    $stmt->close();
}

// Handle reschedule request (slot only, same date)
if (isset($_POST['reschedule']) && isset($_POST['rescheduleBookingId'])) {
    $bookingId = intval($_POST['rescheduleBookingId']);
    $newSlot   = $_POST['newSlot'];

    $stmt = $conn->prepare("UPDATE slotbookings SET Slot=?, Status='Rescheduled' WHERE BookingID=? AND UserID=?");
    $stmt->bind_param("sii", $newSlot, $bookingId, $uid);

    if ($stmt->execute()) {
        $dateStmt = $conn->prepare("SELECT BookingDateChosen FROM slotbookings WHERE BookingID=? AND UserID=?");
        $dateStmt->bind_param("ii", $bookingId, $uid);
        $dateStmt->execute();
        $dateStmt->bind_result($bookingDate);
        $dateStmt->fetch();
        $dateStmt->close();

        $payStmt = $conn->prepare("UPDATE payments SET Slot=?, BookingDateChosen=? WHERE BookingID=? AND UserID=?");
        $payStmt->bind_param("ssii", $newSlot, $bookingDate, $bookingId, $uid);
        $payStmt->execute();
        $payStmt->close();

        $message = "✅ Slot rescheduled successfully.";
    } else {
        $message = "❌ Error rescheduling slot.";
    }
    $stmt->close();
}

// ✅ Fetch only upcoming bookings
$today = date('Y-m-d');
$bookingSql = "
    SELECT b.BookingID, u.name AS FullName, u.email AS Email, u.contact AS ContactNumber,
           b.Slot, b.BookingDateChosen, b.Status, b.created_at AS BookingDate, 
           s.start_time, s.end_time, b.attendance_status
    FROM slotbookings b
    JOIN users u ON b.UserID = u.id
    JOIN slots s ON b.Slot = s.Slot
    WHERE b.UserID = ?
      AND b.BookingDateChosen >= ?
    ORDER BY b.BookingDateChosen ASC
";
$stmt = $conn->prepare($bookingSql);
$stmt->bind_param("is", $uid, $today);
$stmt->execute();
$result = $stmt->get_result();
$allSlots = [];
while ($row = $result->fetch_assoc()) {
    $allSlots[] = $row;
}
$stmt->close();

/* ============================
   Fetch past bookings (before today)
   ============================ */
$pastSql = "
    SELECT b.BookingID, u.name AS FullName, u.email AS Email, u.contact AS ContactNumber,
           b.Slot, b.BookingDateChosen, b.Status, b.created_at AS BookingDate, 
           s.start_time, s.end_time, b.attendance_status
    FROM slotbookings b
    JOIN users u ON b.UserID = u.id
    JOIN slots s ON b.Slot = s.Slot
    WHERE b.UserID = ?
      AND b.BookingDateChosen < ?
    ORDER BY b.BookingDateChosen DESC
";
$stmt2 = $conn->prepare($pastSql);
$stmt2->bind_param("is", $uid, $today);
$stmt2->execute();
$result2 = $stmt2->get_result();
$pastSlots = [];
while ($row = $result2->fetch_assoc()) {
    $pastSlots[] = $row;
}
$stmt2->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>MY SLOTS | EduAxis</title>
  <link rel="icon" type="image/x-icon" href="IMGS/LOGO.ico">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
  <style>
    body { font-family: "DM Sans","sans-serif"; margin-top: 2px; }
    .container { display: flex; justify-content: center; padding: 20px; }
    .content { width: 95%; text-align: center; }
    .logo img { width: 150px; max-width: 80vw; border-radius: 8px; margin-bottom: 20px; }
    table th, table td { vertical-align: middle; text-align: center; }

    /* Mobile view */
    @media (max-width: 768px) {
      .table thead { display: none; }
      .table, .table tbody, .table tr, .table td {
        display: block; width: 100%;
      }
      .table tr {
        margin-bottom: 1rem; border: 1px solid #dee2e6;
        border-radius: 0.5rem; padding: 0.5rem;
        background: #fff; box-shadow: 0 1px 3px rgba(0,0,0,0.1);
      }
      .table td {
        text-align: left; padding: 0.5rem; border: none;
        border-bottom: 1px solid #f1f1f1; position: relative;
      }
      .table td:last-child { border-bottom: none; }
      .table td::before {
        content: attr(data-label) " "; font-weight: bold;
        display: block; margin-bottom: 0.2rem; color: #333;
      }
    }
  </style>
</head>
<body>
  <div class="container">
    <div class="content">
      <div class="logo"><img src="IMGS/LOGO.jpg" alt="Logo"></div>
      <h2><u>MY SLOTS</u></h2>

      <?php if (!empty($message)): ?>
        <div class="alert alert-info"><?= $message ?></div>
      <?php endif; ?>

      <p>Name: <?= htmlspecialchars($_SESSION['fullname']) ?></p>
      <p>Email ID: <?= htmlspecialchars($_SESSION['email']) ?></p>

      <div class="table-responsive">
        <table class="table table-bordered table-striped mt-3">
          <thead class="table-dark">
            <tr>
              <th>Full Name</th>
              <th>Contact Number</th>
              <th>Email</th>
              <th>Slot</th>
              <th>Booking Date Chosen</th>
              <th>Booking Date</th>
              <th>Timings</th>
              <th>Status</th>
              <th>Attendance</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php if (!empty($allSlots)): ?>
              <?php foreach ($allSlots as $row): ?>
                <tr>
                  <td data-label="Full Name"><?= htmlspecialchars($row['FullName']) ?></td>
                  <td data-label="Contact Number"><?= htmlspecialchars($row['ContactNumber']) ?></td>
                  <td data-label="Email"><?= htmlspecialchars($row['Email']) ?></td>
                  <td data-label="Slot"><?= htmlspecialchars($row['Slot']) ?></td>
                  <td data-label="Booking Date Chosen"><?= htmlspecialchars($row['BookingDateChosen']) ?></td>
                  <td data-label="Booking Date"><?= htmlspecialchars($row['BookingDate']) ?></td>
                  <td data-label="Timings">
                    <?= date("h:i A", strtotime($row['start_time'])) . " - " . date("h:i A", strtotime($row['end_time'])) ?>
                  </td>
                  <td data-label="Status"><?= htmlspecialchars($row['Status']) ?></td>
                  <td data-label="Attendance" class="att-status" data-id="<?= $row['BookingID'] ?>">
                    <?php if ($row['attendance_status'] === 'Present'): ?>
                      ✅ Present
                    <?php elseif ($row['attendance_status'] === 'Absent'): ?>
                      ❌ Absent
                    <?php else: ?>
                      ⏳ Pending
                    <?php endif; ?>
                  </td>
                  <td data-label="Actions">
                    <?php if ($row['Status'] !== 'Waiting' && $row['attendance_status'] === 'Pending'): ?>
                      <div class="d-flex flex-wrap gap-2 justify-content-center">
                        <form method="POST" onsubmit="return confirm('Are you sure you want to delete this slot?');">
                          <input type="hidden" name="bookingId" value="<?= htmlspecialchars($row['BookingID']) ?>">
                          <button type="submit" name="delete" class="btn btn-danger btn-sm">Delete</button>
                        </form>
                        <button type="button" class="btn btn-warning btn-sm reschedule-btn"
                                data-bs-toggle="modal" data-bs-target="#rescheduleModal"
                                data-id="<?= htmlspecialchars($row['BookingID']) ?>"
                                data-slot="<?= htmlspecialchars($row['Slot']) ?>"
                                data-date="<?= htmlspecialchars($row['BookingDateChosen']) ?>">
                          Reschedule
                        </button>
                        <!-- ✅ Add to Calendar button -->
                        <a href="INCLUDES/calendar_action.php?id=<?= htmlspecialchars($row['BookingID']) ?>" 
                           class="btn btn-success btn-sm" target="_blank">
                          Add to Calendar
                        </a>
                      </div>
                    <?php elseif ($row['attendance_status'] !== 'Pending'): ?>
                      <span class="text-muted">⛔ Actions disabled (attendance marked)</span>
                    <?php else: ?>
                      <span class="text-muted">Waitlist</span>
                    <?php endif; ?>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr><td colspan="10">No upcoming slots found</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>

      <!-- ============================
           PAST BOOKINGS (NO ACTIONS)
           ============================ -->
      <hr class="my-4">
      <h3><u>PAST BOOKINGS</u></h3>
      <div class="table-responsive">
        <table class="table table-bordered table-striped mt-3">
          <thead class="table-dark">
            <tr>
              <th>Full Name</th>
              <th>Contact Number</th>
              <th>Email</th>
              <th>Slot</th>
              <th>Booking Date Chosen</th>
              <th>Booking Date</th>
              <th>Timings</th>
              <th>Status</th>
              <th>Attendance</th>
            </tr>
          </thead>
          <tbody>
            <?php if (!empty($pastSlots)): ?>
              <?php foreach ($pastSlots as $row): ?>
                <tr>
                  <td data-label="Full Name"><?= htmlspecialchars($row['FullName']) ?></td>
                  <td data-label="Contact Number"><?= htmlspecialchars($row['ContactNumber']) ?></td>
                  <td data-label="Email"><?= htmlspecialchars($row['Email']) ?></td>
                  <td data-label="Slot"><?= htmlspecialchars($row['Slot']) ?></td>
                  <td data-label="Booking Date Chosen"><?= htmlspecialchars($row['BookingDateChosen']) ?></td>
                  <td data-label="Booking Date"><?= htmlspecialchars($row['BookingDate']) ?></td>
                  <td data-label="Timings">
                    <?= date("h:i A", strtotime($row['start_time'])) . " - " . date("h:i A", strtotime($row['end_time'])) ?>
                  </td>
                  <td data-label="Status"><?= htmlspecialchars($row['Status']) ?></td>
                  <td data-label="Attendance">
                    <?php if ($row['attendance_status'] === 'Present'): ?>
                      ✅ Present
                    <?php elseif ($row['attendance_status'] === 'Absent'): ?>
                      ❌ Absent
                    <?php else: ?>
                      ⏳ Pending
                    <?php endif; ?>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr><td colspan="9">No past slots found</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>

  <!-- Reschedule Modal -->
  <div class="modal fade" id="rescheduleModal" tabindex="-1">
    <div class="modal-dialog">
      <div class="modal-content">
        <form method="POST">
          <div class="modal-header">
            <h5 class="modal-title">Reschedule Slot</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <input type="hidden" name="rescheduleBookingId" id="rescheduleBookingId">
            <div class="mb-3">
              <label class="form-label">Booking Date</label>
              <input type="text" class="form-control" id="rescheduleDate" readonly>
            </div>
            <div class="mb-3">
              <label class="form-label">Choose New Slot</label>
              <select class="form-control" name="newSlot" id="newSlot" required>
                <?php
                $slotResult = $conn->query("SELECT Slot, start_time, end_time FROM slots ORDER BY Slot ASC");
                while ($slotRow = $slotResult->fetch_assoc()) {
                    $timing = date("h:i A", strtotime($slotRow['start_time'])) . " - " . date("h:i A", strtotime($slotRow['end_time']));
                    echo "<option value='".htmlspecialchars($slotRow['Slot'])."'>".
                          htmlspecialchars($slotRow['Slot'])." - ".$timing.
                         "</option>";
                }
                ?>
              </select>
            </div>
          </div>
          <div class="modal-footer">
            <button type="submit" name="reschedule" class="btn btn-primary">Save changes</button>
          </div>
        </form>
      </div>
    </div>
  </div>
  
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    document.addEventListener("DOMContentLoaded", () => {
      // Reschedule modal fill
      document.querySelectorAll('.reschedule-btn').forEach(btn => {
        btn.addEventListener('click', () => {
          document.getElementById('rescheduleBookingId').value = btn.dataset.id;
          document.getElementById('newSlot').value = btn.dataset.slot;
          document.getElementById('rescheduleDate').value = btn.dataset.date;
        });
      });
    });
  </script>
</body>
</html>

<?php $conn->close(); ?>
