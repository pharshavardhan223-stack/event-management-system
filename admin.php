<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel — EventVault</title>
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

if($user['role'] != 'admin'){
    header("Location: dashboard.php");
    exit();
}

$total_events   = $conn->query("SELECT COUNT(*) as t FROM events")->fetch_assoc()['t'];
$total_bookings = $conn->query("SELECT COUNT(*) as t FROM bookings")->fetch_assoc()['t'];
$total_users    = $conn->query("SELECT COUNT(*) as t FROM users")->fetch_assoc()['t'];
$total_revenue  = $conn->query("SELECT SUM(total_price) as t FROM bookings WHERE payment_status='Paid'")->fetch_assoc()['t'];
?>

<nav class="navbar">
    <a href="dashboard.php" class="navbar-brand">🎟 EventVault</a>
    <div class="navbar-links">
        <a href="dashboard.php">Dashboard</a>
        <a href="view_events.php">Events</a>
        <a href="logout.php" class="nav-cta">Logout</a>
    </div>
</nav>

<div class="page-wrap">
<div class="dashboard-page">

    <div class="welcome-banner anim-1">
        <p class="greeting">⚙️ Admin Panel</p>
        <h1>Control Centre</h1>
        <p>Manage events, bookings and users from one place.</p>
    </div>

    <div class="stats-row anim-2" style="grid-template-columns:repeat(4,1fr);">
        <div class="stat-card">
            <span class="stat-num"><?php echo $total_events; ?></span>
            <span class="stat-label">Total Events</span>
        </div>
        <div class="stat-card">
            <span class="stat-num"><?php echo $total_bookings; ?></span>
            <span class="stat-label">Total Bookings</span>
        </div>
        <div class="stat-card">
            <span class="stat-num"><?php echo $total_users; ?></span>
            <span class="stat-label">Registered Users</span>
        </div>
        <div class="stat-card">
            <span class="stat-num">₹<?php echo number_format($total_revenue ?? 0); ?></span>
            <span class="stat-label">Total Revenue</span>
        </div>
    </div>

    <div class="dash-grid anim-3" style="grid-template-columns:repeat(4,1fr);">
        <a class="dash-card" href="create_event.php">
            <span class="dash-card-icon">🎤</span>
            <h3>Create Event</h3>
            <p>Publish a new event for users to book.</p>
            <span class="btn btn-primary btn-sm">Create</span>
        </a>
        <a class="dash-card" href="view_events.php">
            <span class="dash-card-icon">🗓️</span>
            <h3>Manage Events</h3>
            <p>Edit or delete existing events.</p>
            <span class="btn btn-ghost btn-sm">Manage</span>
        </a>
        <a class="dash-card" href="admin_booking.php">
            <span class="dash-card-icon">🎟️</span>
            <h3>All Bookings</h3>
            <p>View all ticket bookings.</p>
            <span class="btn btn-ghost btn-sm">View</span>
        </a>
        <a class="dash-card" href="admin_users.php">
            <span class="dash-card-icon">👥</span>
            <h3>Manage Users</h3>
            <p>View all registered users.</p>
            <span class="btn btn-ghost btn-sm">View</span>
        </a>
    </div>

    <div class="activity-box anim-4">
        <h3>🕐 Latest Bookings</h3>
        <?php
        $recent = $conn->query("
            SELECT bookings.*, users.name, events.title
            FROM bookings
            JOIN users ON bookings.user_id = users.id
            JOIN events ON bookings.event_id = events.id
            ORDER BY bookings.id DESC LIMIT 5
        ");
        if($recent->num_rows > 0){
            while($row = $recent->fetch_assoc()){
                $sc = ($row['payment_status'] == 'Cancelled') ? 'var(--red)' : 'var(--green)';
                echo '
                <div class="activity-item">
                    <div class="activity-dot"></div>
                    <span><b>' . htmlspecialchars($row['name']) . '</b> booked <b>' . htmlspecialchars($row['title']) . '</b> — ₹' . number_format($row['total_price']) . '</span>
                    <span style="color:' . $sc . '; font-size:0.75rem; font-weight:600; margin-left:auto;">' . $row['payment_status'] . '</span>
                </div>';
            }
        } else {
            echo '<p class="text-muted" style="font-size:0.875rem;">No bookings yet.</p>';
        }
        ?>
    </div>

</div>
</div>

<footer class="footer"><p>© 2026 EventVault — Admin Panel</p></footer>

</body>
</html>