<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Detail — EventVault</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .detail-hero {
            position: relative;
            width: 100%;
            height: 380px;
            overflow: hidden;
            border-radius: 20px;
            margin-bottom: 36px;
        }
        .detail-hero img {
            width: 100%; height: 100%;
            object-fit: cover;
            display: block;
        }
        .detail-hero-overlay {
            position: absolute;
            inset: 0;
            background: linear-gradient(to top, rgba(10,10,15,0.95) 0%, rgba(10,10,15,0.3) 60%, transparent 100%);
        }
        .detail-hero-content {
            position: absolute;
            bottom: 32px; left: 36px; right: 36px;
        }
        .detail-hero-placeholder {
            width: 100%; height: 100%;
            background: linear-gradient(135deg, var(--surface2), var(--bg));
            display: flex; align-items: center; justify-content: center;
            font-size: 5rem;
        }
        .detail-layout {
            display: grid;
            grid-template-columns: 1fr 360px;
            gap: 28px;
            align-items: start;
        }
        @media(max-width:800px){ .detail-layout { grid-template-columns: 1fr; } }

        .detail-section {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 28px;
            margin-bottom: 20px;
        }
        .detail-section h3 {
            font-family: 'Playfair Display', serif;
            font-size: 1.1rem;
            margin-bottom: 16px;
            padding-bottom: 12px;
            border-bottom: 1px solid var(--border);
        }
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
        }
        .info-item .info-label {
            font-size: 0.72rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: var(--muted);
            margin-bottom: 4px;
        }
        .info-item .info-value {
            font-size: 0.95rem;
            font-weight: 500;
        }
        .booking-sticky {
            position: sticky;
            top: 88px;
        }
        .price-big {
            font-family: 'Playfair Display', serif;
            font-size: 2.5rem;
            color: var(--accent);
            line-height: 1;
            margin-bottom: 4px;
        }
        .price-big span {
            font-size: 0.9rem;
            color: var(--muted);
            font-family: 'DM Sans', sans-serif;
        }
    </style>
</head>
<body>

<?php
session_start();
include 'db.php';

if(!isset($_SESSION['user'])){
    header("Location: login.php");
    exit();
}

$id    = intval($_GET['id']);
$event = $conn->query("SELECT * FROM events WHERE id='$id'")->fetch_assoc();

if(!$event){
    header("Location: view_events.php");
    exit();
}

$email = $_SESSION['user'];
$user  = $conn->query("SELECT * FROM users WHERE email='$email'")->fetch_assoc();

// Booking count for this event
$booked = $conn->query("SELECT SUM(tickets) as t FROM bookings WHERE event_id='$id' AND payment_status='Paid'")->fetch_assoc()['t'] ?? 0;
?>

<nav class="navbar">
    <a href="dashboard.php" class="navbar-brand">🎟 EventVault</a>
    <div class="navbar-links">
        <a href="dashboard.php">Home</a>
        <a href="view_events.php">Events</a>
        <a href="my_bookings.php">My Tickets</a>
        <?php if($user['role'] == 'admin'){ ?><a href="admin.php">Admin</a><?php } ?>
        <a href="logout.php" class="nav-cta">Logout</a>
    </div>
</nav>

<div class="page-wrap">
<div class="events-page">

    <!-- Breadcrumb -->
    <div style="margin-bottom:24px; font-size:0.85rem; color:var(--muted);">
        <a href="view_events.php" style="color:var(--muted); text-decoration:none;">Events</a>
        <span style="margin:0 8px;">›</span>
        <span style="color:var(--text);"><?php echo htmlspecialchars($event['title']); ?></span>
    </div>

    <!-- Hero Image -->
    <div class="detail-hero">
        <?php if($event['image']){ ?>
            <img src="uploads/<?php echo htmlspecialchars($event['image']); ?>" alt="<?php echo htmlspecialchars($event['title']); ?>">
        <?php } else { ?>
            <div class="detail-hero-placeholder">🎭</div>
        <?php } ?>
        <div class="detail-hero-overlay"></div>
        <div class="detail-hero-content">
            <span class="event-badge" style="margin-bottom:12px; display:inline-block;">🎫 Event</span>
            <h1 style="font-family:'Playfair Display',serif; font-size:clamp(1.5rem,3vw,2.5rem); margin-bottom:8px;">
                <?php echo htmlspecialchars($event['title']); ?>
            </h1>
            <div style="display:flex; gap:20px; flex-wrap:wrap; font-size:0.85rem; color:rgba(240,237,232,0.8);">
                <span>📅 <?php echo date('D, M d Y', strtotime($event['date'])); ?></span>
                <span>📍 <?php echo htmlspecialchars($event['location']); ?></span>
            </div>
        </div>
    </div>

    <div class="detail-layout">

        <!-- LEFT: Details -->
        <div>
            <div class="detail-section">
                <h3>About This Event</h3>
                <p style="color:var(--muted); line-height:1.8; font-size:0.9rem;">
                    <?php echo nl2br(htmlspecialchars($event['description'])); ?>
                </p>
            </div>

            <div class="detail-section">
                <h3>Event Information</h3>
                <div class="info-grid">
                    <div class="info-item">
                        <div class="info-label">📅 Date</div>
                        <div class="info-value"><?php echo date('D, M d Y', strtotime($event['date'])); ?></div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">📍 Location</div>
                        <div class="info-value"><?php echo htmlspecialchars($event['location']); ?></div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">🎟 Ticket Price</div>
                        <div class="info-value" style="color:var(--accent);">₹<?php echo number_format($event['price']); ?></div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">✅ Tickets Sold</div>
                        <div class="info-value"><?php echo $booked; ?> booked</div>
                    </div>
                </div>
            </div>

            <?php if($user['role'] == 'admin'){ ?>
            <div class="detail-section">
                <h3>Admin Actions</h3>
                <div style="display:flex; gap:12px;">
                    <a href="edit_event.php?id=<?php echo $event['id']; ?>" class="btn btn-ghost btn-sm">✏️ Edit Event</a>
                    <a href="delete_event.php?id=<?php echo $event['id']; ?>" class="btn btn-danger btn-sm">🗑 Delete</a>
                </div>
            </div>
            <?php } ?>
        </div>

        <!-- RIGHT: Booking Card (sticky) -->
        <div class="booking-sticky">
            <div class="detail-section" style="margin-bottom:0;">

                <div style="margin-bottom:20px;">
                    <div class="price-big">₹<?php echo number_format($event['price']); ?> <span>/ ticket</span></div>
                    <p style="color:var(--muted); font-size:0.8rem; margin-top:6px;">
                        📅 <?php echo date('D, M d Y', strtotime($event['date'])); ?>
                    </p>
                </div>

                <hr class="booking-divider">

                <div style="margin-bottom:20px;">
                    <div style="display:flex; justify-content:space-between; font-size:0.85rem; margin-bottom:8px;">
                        <span style="color:var(--muted);">📍 Location</span>
                        <span style="font-weight:500;"><?php echo htmlspecialchars($event['location']); ?></span>
                    </div>
                    <div style="display:flex; justify-content:space-between; font-size:0.85rem;">
                        <span style="color:var(--muted);">✅ Tickets Sold</span>
                        <span style="font-weight:500;"><?php echo $booked; ?></span>
                    </div>
                </div>

                <a href="book_ticket.php?id=<?php echo $event['id']; ?>"
                   class="btn btn-primary form-full" style="justify-content:center;">
                    🎟️ Book Now
                </a>

                <p style="text-align:center; font-size:0.75rem; color:var(--muted); margin-top:12px;">
                    Instant confirmation • QR code ticket
                </p>

            </div>
        </div>

    </div>

</div>
</div>

<footer class="footer"><p>© 2026 EventVault — Harsha Vardhan</p></footer>

</body>
</html>