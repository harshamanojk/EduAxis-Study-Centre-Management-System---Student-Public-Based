<?php
session_start();
include 'INCLUDES/db.php'; // Database connection

if (!isset($_SESSION['uid'])) {
    die("Please login first.");
}

$user_id = $_SESSION['uid'];

// Fetch student info
$stmt = $conn->prepare("SELECT name, qr_token FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user) {
    die("User not found.");
}

// Generate a qr_token if empty
if (empty($user['qr_token'])) {
    $user['qr_token'] = bin2hex(random_bytes(16)); // 32-character token
    $update = $conn->prepare("UPDATE users SET qr_token = ? WHERE id = ?");
    $update->bind_param("si", $user['qr_token'], $user_id);
    $update->execute();
}

// Load QR generator
require_once 'TCPDF-main/tcpdf_barcodes_2d.php';
$qr = new TCPDF2DBarcode($user['qr_token'], 'QRCODE,H');

// Generate PNG QR code and encode it as Base64
$qrPng = $qr->getBarcodePngData(6, 6, [0,0,0]);
$qrBase64 = base64_encode($qrPng);
?>
<!DOCTYPE html>
<html>
<head>
    <title>QR CODE FOR ENTRY/EXIT</title>
    <link rel="icon" type="image/x-icon" href="IMGS/LOGO.ico">
    <style>
        body { 
            font-family: Arial, sans-serif; 
            text-align: center; 
            margin-top: 50px; 
        }
        .qr-container { 
            display: inline-block; 
            padding: 20px; 
            border: 1px solid #ccc; 
            border-radius: 10px;
        }
        h2 { margin-bottom: 10px; }
        img.qr {
            width: 250px;        
            max-width: 80vw;     
            height: auto;
        }
        @media (max-width: 600px) {
            img.qr {
                width: 180px;    
            }
        }
    </style>
</head>
<body>
    <div class="slot-container">
    <!-- LOGO -->
    <div style="text-align: center; margin-bottom: 20px;">
        <img src="IMGS/LOGO.jpg" alt="Logo" 
             style="width: 150px; max-width: 80vw; height: auto; border-radius: 8px;">
    </div>
    <h2>Hello, <?php echo htmlspecialchars($user['name']); ?>!</h2>
    <p>Use this QR code to scan IN/OUT at the centre.</p>
    <div class="qr-container">
        <img class="qr" src="data:image/png;base64,<?php echo $qrBase64; ?>" alt="QR Code" id="qrImage">
    </div>
    <p><b>Your ID:</b> <?php echo htmlspecialchars($user['qr_token']); ?></p>
</body>
</html>
