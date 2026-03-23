<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard — EventVault</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<?php
session_start();
include 'db.php';

if(!isset($_SESSION['user'])){
    header("Location: login.php");
    exit();
}

$email = $_SESSION['user'];
$user  = $conn->query("SELECT * FROM users WHERE email='$email'")->fetch_assoc();

$total_events   = $conn->query("SELECT COUNT(*) as total FROM events")->fetch_assoc()['total'];
$total_bookings = $conn->query("SELECT COUNT(*) as total FROM bookings WHERE user_id='{$user['id']}'")->fetch_assoc()['total'];
$total_all_bookings = $conn->query("SELECT COUNT(*) as total FROM bookings")->fetch_assoc()['total'];
?>

<!-- Navbar -->
<nav class="navbar">
    <a href="dashboard.php" class="navbar-brand">🎟 EventVault</a>
    <div class="navbar-links">
        <a href="view_events.php">Events</a>
        <a href="my_bookings.php">My Tickets</a>
        <?php if($user['role'] == 'admin'){ ?>
            <a href="admin.php">Admin</a>
        <?php } ?>
        <a href="logout.php" class="nav-cta">Logout</a>
    </div>
</nav>

<div class="page-wrap">
<div class="dashboard-page">

    <!-- Welcome Banner -->
    <div class="welcome-banner anim-1">
        <p class="greeting">✦ Welcome Back</p>
        <h1><?php echo htmlspecialchars($user['name']); ?> 👋</h1>
        <p>Your central hub for events and bookings.</p>
    </div>

    <!-- Quick Actions -->
    <div class="dash-grid anim-2">

        <a class="dash-card" href="create_event.php">
            <span class="dash-card-icon">🎨</span>
            <h3>Create Event</h3>
            <p>Design and publish your own event for others to discover and book.</p>
            <span class="btn btn-primary btn-sm">Create Now</span>
        </a>

        <a class="dash-card" href="view_events.php">
            <span class="dash-card-icon">🗓️</span>
            <h3>Explore Events</h3>
            <p>Browse upcoming events happening around you and grab your tickets.</p>
            <span class="btn btn-ghost btn-sm">View Events</span>
        </a>

        <a class="dash-card" href="my_bookings.php">
            <span class="dash-card-icon">🎟️</span>
            <h3>My Tickets</h3>
            <p>Access all your booked tickets with QR codes for entry.</p>
            <span class="btn btn-ghost btn-sm">View Tickets</span>
        </a>

    </div>

    <!-- Stats -->
    <div class="stats-row anim-3">
        <div class="stat-card">
            <span class="stat-num"><?php echo $total_events; ?></span>
            <span class="stat-label">Active Events</span>
        </div>
        <div class="stat-card">
            <span class="stat-num"><?php echo $total_bookings; ?></span>
            <span class="stat-label">My Bookings</span>
        </div>
        <div class="stat-card">
            <span class="stat-num"><?php echo ($user['role'] == 'admin') ? '⭐' : '👤'; ?></span>
            <span class="stat-label"><?php echo ucfirst($user['role']); ?> Account</span>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="activity-box anim-4">
        <h3>🕐 Recent Bookings</h3>

        <?php
        $activity = $conn->query("
            SELECT events.title, bookings.created_at, bookings.total_price
            FROM bookings
            JOIN events ON bookings.event_id = events.id
            WHERE bookings.user_id = '{$user['id']}'
            ORDER BY bookings.id DESC LIMIT 5
        ");

        if($activity->num_rows > 0){
            while($row = $activity->fetch_assoc()){
                echo '
                <div class="activity-item">
                    <div class="activity-dot"></div>
                    <span>Booked <b>' . htmlspecialchars($row['title']) . '</b> — ₹' . $row['total_price'] . '</span>
                    <span class="act-time">' . date('M d, Y', strtotime($row['created_at'])) . '</span>
                </div>';
            }
        } else {
            echo '<p class="text-muted" style="font-size:0.875rem;">No bookings yet. <a href="view_events.php" style="color:var(--accent)">Explore events →</a></p>';
        }
        ?>
    </div>

</div>
</div>

<footer class="footer">
    <p>© 2026 EventVault — Harsha Vardhan</p>
</footer>

</body>
</html>