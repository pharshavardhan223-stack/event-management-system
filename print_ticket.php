<?php
session_start();
include 'db.php';

if(!isset($_SESSION['user'])){
    header("Location: login.php");
    exit();
}

$booking_id = intval($_GET['id']);
$email      = $_SESSION['user'];
$user       = $conn->query("SELECT * FROM users WHERE email='$email'")->fetch_assoc();

$booking = $conn->query("
    SELECT bookings.*, events.title, events.date, events.location, events.image, events.price,
           users.name AS user_name, users.email AS user_email
    FROM bookings
    JOIN events ON bookings.event_id = events.id
    JOIN users  ON bookings.user_id  = users.id
    WHERE bookings.id = '$booking_id' AND bookings.user_id = '{$user['id']}'
")->fetch_assoc();

if(!$booking){
    echo "<p style='color:red; padding:40px;'>Booking not found or access denied.</p>";
    exit();
}

$isCancelled = ($booking['payment_status'] == 'Cancelled');

$qr_data = implode("|", [
    "EVENTVAULT-TICKET",
    "ID:#" . $booking['id'],
    "EVENT:" . $booking['title'],
    "DATE:" . date('d-m-Y', strtotime($booking['date'])),
    "LOCATION:" . $booking['location'],
    "NAME:" . $booking['user_name'],
    "TICKETS:" . $booking['tickets'],
    "SEATS:" . $booking['section_numbers'],
    "TOTAL:Rs." . $booking['total_price'],
    "STATUS:" . $booking['payment_status']
]);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print Ticket — EventVault</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=DM+Sans:wght@300;400;600&display=swap');

        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'DM Sans', sans-serif;
            background: #f0ede8;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 40px 20px;
            min-height: 100vh;
        }

        /* Print/Download buttons - hidden when printing */
        .controls {
            display: flex;
            gap: 12px;
            margin-bottom: 32px;
        }

        .btn-print {
            padding: 12px 28px;
            border-radius: 50px;
            border: none;
            font-family: 'DM Sans', sans-serif;
            font-size: 0.9rem;
            font-weight: 600;
            cursor: pointer;
            display: flex; align-items: center; gap: 8px;
            transition: all 0.2s;
        }

        .btn-print.primary { background: #c9a84c; color: #0a0a0f; }
        .btn-print.primary:hover { background: #e8c97a; }
        .btn-print.ghost { background: white; color: #2c2c3a; border: 1px solid #ddd; }
        .btn-print.ghost:hover { background: #f5f5f5; }

        /* THE TICKET */
        .ticket {
            width: 680px;
            max-width: 100%;
            background: white;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 20px 60px rgba(0,0,0,0.15);
        }

        /* Top colored band */
        .ticket-band {
            background: linear-gradient(135deg, #0a0a0f 0%, #1c1c28 50%, #2a2020 100%);
            padding: 32px 36px 28px;
            position: relative;
            overflow: hidden;
        }

        .ticket-band::before {
            content: '🎟';
            position: absolute;
            right: 36px; top: 50%;
            transform: translateY(-50%);
            font-size: 5rem;
            opacity: 0.08;
        }

        .ticket-band .label {
            font-size: 0.65rem;
            letter-spacing: 3px;
            text-transform: uppercase;
            color: #c9a84c;
            margin-bottom: 8px;
        }

        .ticket-band h1 {
            font-family: 'Playfair Display', serif;
            font-size: 1.8rem;
            color: white;
            margin-bottom: 12px;
            line-height: 1.2;
        }

        .ticket-band .meta {
            display: flex; gap: 20px; flex-wrap: wrap;
            font-size: 0.82rem;
            color: rgba(255,255,255,0.65);
        }

        /* Event image strip */
        .ticket-img {
            width: 100%; height: 180px;
            object-fit: cover;
            display: block;
        }

        /* Perforation */
        .perforation {
            display: flex;
            align-items: center;
            background: #f8f6f2;
            position: relative;
        }

        .perforation::before,
        .perforation::after {
            content: '';
            flex: 1;
            border-top: 2px dashed #ddd;
        }

        .perf-circle {
            width: 24px; height: 24px;
            background: #f0ede8;
            border-radius: 50%;
            flex-shrink: 0;
        }

        /* Bottom section */
        .ticket-bottom {
            padding: 28px 36px;
            display: flex;
            gap: 28px;
            align-items: flex-start;
            background: white;
        }

        /* QR */
        .qr-section { flex-shrink: 0; text-align: center; }
        .qr-section #qr-print canvas,
        .qr-section #qr-print img {
            width: 110px !important;
            height: 110px !important;
            border-radius: 8px;
        }
        .qr-label {
            font-size: 0.6rem;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            color: #999;
            margin-top: 6px;
        }

        /* Details grid */
        .ticket-details {
            flex: 1;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px 24px;
        }

        .detail-item .d-label {
            font-size: 0.65rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #999;
            margin-bottom: 3px;
        }

        .detail-item .d-value {
            font-size: 0.9rem;
            font-weight: 600;
            color: #1a1a2e;
        }

        .detail-item .d-value.gold { color: #c9a84c; }

        /* Footer strip */
        .ticket-footer {
            background: #0a0a0f;
            padding: 14px 36px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .ticket-footer .brand {
            font-family: 'Playfair Display', serif;
            color: #c9a84c;
            font-size: 1rem;
        }

        .ticket-footer .booking-id {
            font-size: 0.75rem;
            color: rgba(255,255,255,0.4);
            letter-spacing: 2px;
        }

        .ticket-footer .status-badge {
            padding: 4px 12px;
            border-radius: 50px;
            font-size: 0.72rem;
            font-weight: 600;
        }

        .status-paid { background: rgba(76,175,125,0.2); color: #4caf7d; border: 1px solid rgba(76,175,125,0.4); }
        .status-cancelled { background: rgba(224,92,92,0.2); color: #e05c5c; border: 1px solid rgba(224,92,92,0.4); }

        /* ── PRINT STYLES ───────────────────── */
        @media print {
            body { background: white; padding: 0; }
            .controls { display: none !important; }
            .ticket { box-shadow: none; width: 100%; }
            @page { margin: 0.5cm; }
        }
    </style>
</head>
<body>

<!-- Controls -->
<div class="controls">
    <button class="btn-print primary" onclick="window.print()">🖨️ Print Ticket</button>
    <button class="btn-print ghost" onclick="window.history.back()">← Go Back</button>
</div>

<!-- THE TICKET -->
<div class="ticket">

    <!-- Top Band -->
    <div class="ticket-band">
        <div class="label">✦ EventVault — Official Ticket</div>
        <h1><?php echo htmlspecialchars($booking['title']); ?></h1>
        <div class="meta">
            <span>📅 <?php echo date('D, M d Y', strtotime($booking['date'])); ?></span>
            <span>📍 <?php echo htmlspecialchars($booking['location']); ?></span>
        </div>
    </div>

    <!-- Event Image -->
    <?php if($booking['image']){ ?>
        <img class="ticket-img" src="uploads/<?php echo htmlspecialchars($booking['image']); ?>" alt="Event">
    <?php } ?>

    <!-- Perforation -->
    <div class="perforation">
        <div class="perf-circle" style="margin-left:-12px;"></div>
        <div class="perf-circle" style="margin-right:-12px;"></div>
    </div>

    <!-- Bottom: QR + Details -->
    <div class="ticket-bottom">

        <div class="qr-section">
            <div id="qr-print"></div>
            <div class="qr-label">Scan to verify</div>
        </div>

        <div class="ticket-details">
            <div class="detail-item">
                <div class="d-label">Attendee</div>
                <div class="d-value"><?php echo htmlspecialchars($booking['user_name']); ?></div>
            </div>
            <div class="detail-item">
                <div class="d-label">Booking ID</div>
                <div class="d-value">#<?php echo $booking['id']; ?></div>
            </div>
            <div class="detail-item">
                <div class="d-label">Tickets</div>
                <div class="d-value"><?php echo $booking['tickets']; ?> ticket(s)</div>
            </div>
            <div class="detail-item">
                <div class="d-label">Seat Numbers</div>
                <div class="d-value"><?php echo $booking['section_numbers']; ?></div>
            </div>
            <div class="detail-item">
                <div class="d-label">Price per Ticket</div>
                <div class="d-value">₹<?php echo number_format($booking['price']); ?></div>
            </div>
            <div class="detail-item">
                <div class="d-label">Total Paid</div>
                <div class="d-value gold">₹<?php echo number_format($booking['total_price']); ?></div>
            </div>
        </div>

    </div>

    <!-- Footer Strip -->
    <div class="ticket-footer">
        <span class="brand">🎟 EventVault</span>
        <span class="booking-id">BOOKING #<?php echo str_pad($booking['id'], 6, '0', STR_PAD_LEFT); ?></span>
        <span class="status-badge <?php echo $isCancelled ? 'status-cancelled' : 'status-paid'; ?>">
            <?php echo $isCancelled ? '✗ Cancelled' : '✓ Confirmed'; ?>
        </span>
    </div>

</div>

<script>
document.addEventListener("DOMContentLoaded", function(){
    new QRCode(document.getElementById("qr-print"), {
        text: <?php echo json_encode($qr_data); ?>,
        width: 110,
        height: 110,
        colorDark: "#0a0a0f",
        colorLight: "#ffffff",
        correctLevel: QRCode.CorrectLevel.M
    });
});
</script>

</body>
</html>