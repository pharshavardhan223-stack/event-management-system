<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Tickets — EventVault</title>
    <link rel="stylesheet" href="style.css">
    <!-- QR Code library (no backend needed) -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
</head>
<body>

<?php
session_start();
include 'db.php';

if(!isset($_SESSION['user'])){
    header("Location: login.php");
    exit();
}

$email   = $_SESSION['user'];
$user    = $conn->query("SELECT * FROM users WHERE email='$email'")->fetch_assoc();
$user_id = $user['id'];

$result = $conn->query("
    SELECT bookings.*, events.title, events.date, events.location, events.image
    FROM bookings 
    JOIN events ON bookings.event_id = events.id
    WHERE bookings.user_id = '$user_id'
    ORDER BY bookings.id DESC
");
?>

<!-- Navbar -->
<nav class="navbar">
    <a href="dashboard.php" class="navbar-brand">🎟 EventVault</a>
    <div class="navbar-links">
        <a href="dashboard.php">Home</a>
        <a href="view_events.php">Events</a>
        <a href="my_bookings.php">My Tickets</a>
        <a href="logout.php" class="nav-cta">Logout</a>
    </div>
</nav>

<div class="page-wrap">
<div class="tickets-page">

    <div class="page-header">
        <div>
            <p class="section-label">✦ Your Collection</p>
            <h2 style="font-family:'Playfair Display',serif; font-size:2rem;">My Tickets</h2>
        </div>
        <a href="view_events.php" class="btn btn-ghost btn-sm">+ Book New Event</a>
    </div>

    <?php if($result->num_rows == 0){ ?>
        <div style="text-align:center; padding:80px 20px;">
            <div style="font-size:4rem; margin-bottom:16px;">🎟️</div>
            <h3 style="font-family:'Playfair Display',serif; margin-bottom:8px;">No tickets yet</h3>
            <p style="color:var(--muted); margin-bottom:24px;">Discover events and book your first ticket!</p>
            <a href="view_events.php" class="btn btn-primary">Explore Events</a>
        </div>
    <?php } else { ?>

    <div class="tickets-grid">

        <?php $idx = 0; while($row = $result->fetch_assoc()){ 
            $isCancelled = ($row['payment_status'] == 'Cancelled');
            
            // Build QR data string
            $qr_data = "EVENTVAULT TICKET\n" .
                       "Event: " . $row['title'] . "\n" .
                       "Date: " . date('D, M d Y', strtotime($row['date'])) . "\n" .
                       "Location: " . $row['location'] . "\n" .
                       "Tickets: " . $row['tickets'] . "\n" .
                       "Seats: " . $row['section_numbers'] . "\n" .
                       "Total: Rs." . $row['total_price'] . "\n" .
                       "Booking ID: #" . $row['id'] . "\n" .
                       "Status: " . $row['payment_status'];
        ?>

            <div class="ticket-card anim-<?php echo min($idx+1, 4); ?>" style="<?php echo $isCancelled ? 'opacity:0.65;' : ''; ?>">

                <!-- TOP SECTION -->
                <div class="ticket-top">

                    <?php if($row['image']){ ?>
                        <img class="ticket-event-img" src="uploads/<?php echo $row['image']; ?>" alt="Event Image">
                    <?php } else { ?>
                        <div class="ticket-event-img-placeholder">🎭</div>
                    <?php } ?>

                    <div class="ticket-title"><?php echo htmlspecialchars($row['title']); ?></div>

                    <div class="ticket-meta">
                        <div class="ticket-meta-row">📅 <?php echo date('D, M d Y', strtotime($row['date'])); ?></div>
                        <div class="ticket-meta-row">📍 <?php echo htmlspecialchars($row['location']); ?></div>
                    </div>

                </div>

                <!-- PERFORATION -->
                <div class="ticket-perforation">
                    <div class="circle" style="margin-left:-11px;"></div>
                    <div class="circle" style="margin-right:-11px;"></div>
                </div>

                <!-- BOTTOM SECTION: QR + Details -->
                <div class="ticket-bottom">

                    <!-- QR CODE -->
                    <div class="ticket-qr">
                        <div id="qr-<?php echo $row['id']; ?>"></div>
                        <div class="ticket-qr-label">Scan to Verify</div>
                    </div>

                    <!-- TICKET DETAILS -->
                    <div class="ticket-details">
                        <div class="ticket-detail-row">
                            <span class="label">Booking #</span>
                            <span class="value"><?php echo $row['id']; ?></span>
                        </div>
                        <div class="ticket-detail-row">
                            <span class="label">Tickets</span>
                            <span class="value"><?php echo $row['tickets']; ?></span>
                        </div>
                        <div class="ticket-detail-row">
                            <span class="label">Seats</span>
                            <span class="value" style="font-size:0.72rem;"><?php echo $row['section_numbers']; ?></span>
                        </div>
                        <div class="ticket-detail-row">
                            <span class="label">Total Paid</span>
                            <span class="value text-accent">₹<?php echo number_format($row['total_price']); ?></span>
                        </div>

                        <span class="ticket-status <?php echo $isCancelled ? 'status-cancelled' : 'status-paid'; ?>">
                            <?php echo $isCancelled ? '✗ Cancelled' : '✓ Confirmed'; ?>
                        </span>
                    </div>

                </div>

                <!-- ACTIONS -->
                <?php if(!$isCancelled){ ?>
                    <div class="ticket-actions" style="display:flex; gap:8px;">
                        <a href="print_ticket.php?id=<?php echo $row['id']; ?>"
                           class="btn btn-ghost btn-sm" style="flex:1; justify-content:center;"
                           target="_blank">
                            🖨️ Print
                        </a>
                        <a href="cancel_booking.php?id=<?php echo $row['id']; ?>"
                           class="btn btn-danger btn-sm" style="flex:1; justify-content:center;"
                           onclick="return confirm('Cancel this ticket? This cannot be undone.');">
                            Cancel
                        </a>
                    </div>
                <?php } else { ?>
                    <div class="ticket-actions">
                        <a href="print_ticket.php?id=<?php echo $row['id']; ?>"
                           class="btn btn-ghost btn-sm form-full" style="justify-content:center;"
                           target="_blank">
                            🖨️ Print Ticket
                        </a>
                        <p style="font-size:0.75rem; color:var(--muted); text-align:center; margin-top:8px;">This ticket has been cancelled.</p>
                    </div>
                <?php } ?>

            </div>

            <!-- QR Code Generator Script for this ticket -->
            <script>
            document.addEventListener("DOMContentLoaded", function(){
                new QRCode(document.getElementById("qr-<?php echo $row['id']; ?>"), {
                    text: <?php echo json_encode($qr_data); ?>,
                    width: 90,
                    height: 90,
                    colorDark: "#f0ede8",
                    colorLight: "#13131a",
                    correctLevel: QRCode.CorrectLevel.M
                });
            });
            </script>

        <?php $idx++; } ?>

    </div>

    <?php } ?>

</div>
</div>

<footer class="footer">
    <p>© 2026 EventVault — Harsha Vardhan</p>
</footer>

</body>
</html>