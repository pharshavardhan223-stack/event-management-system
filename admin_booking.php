<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Bookings — EventVault</title>
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

$result = $conn->query("
    SELECT bookings.*, users.name AS user_name, users.email AS user_email, events.title AS event_title, events.date AS event_date
    FROM bookings
    JOIN users  ON bookings.user_id  = users.id
    JOIN events ON bookings.event_id = events.id
    ORDER BY bookings.id DESC
");

$total_revenue = $conn->query("SELECT SUM(total_price) as t FROM bookings WHERE payment_status='Paid'")->fetch_assoc()['t'];
$total_bookings = $conn->query("SELECT COUNT(*) as t FROM bookings")->fetch_assoc()['t'];
$cancelled = $conn->query("SELECT COUNT(*) as t FROM bookings WHERE payment_status='Cancelled'")->fetch_assoc()['t'];
?>

<nav class="navbar">
    <a href="dashboard.php" class="navbar-brand">🎟 EventVault</a>
    <div class="navbar-links">
        <a href="admin.php">Admin</a>
        <a href="view_events.php">Events</a>
        <a href="logout.php" class="nav-cta">Logout</a>
    </div>
</nav>

<div class="page-wrap">
<div class="admin-page">

    <div class="page-header">
        <div>
            <p class="section-label">⚙️ Admin</p>
            <h2 style="font-family:'Playfair Display',serif; font-size:2rem;">All Bookings</h2>
        </div>
        <a href="admin.php" class="btn btn-ghost btn-sm">← Back to Admin</a>
    </div>

    <!-- Stats -->
    <div class="stats-row anim-1" style="margin-bottom:28px;">
        <div class="stat-card">
            <span class="stat-num"><?php echo $total_bookings; ?></span>
            <span class="stat-label">Total Bookings</span>
        </div>
        <div class="stat-card">
            <span class="stat-num" style="color:var(--red);"><?php echo $cancelled; ?></span>
            <span class="stat-label">Cancelled</span>
        </div>
        <div class="stat-card">
            <span class="stat-num">₹<?php echo number_format($total_revenue ?? 0); ?></span>
            <span class="stat-label">Total Revenue</span>
        </div>
    </div>

    <!-- Table -->
    <div class="table-wrap anim-2">
        <?php if($result->num_rows == 0){ ?>
            <div style="text-align:center; padding:60px; color:var(--muted);">No bookings found.</div>
        <?php } else { ?>
        <table class="data-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>User</th>
                    <th>Event</th>
                    <th>Date</th>
                    <th>Tickets</th>
                    <th>Seats</th>
                    <th>Total</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
            <?php while($row = $result->fetch_assoc()){
                $isPaid = ($row['payment_status'] == 'Paid');
            ?>
                <tr>
                    <td style="color:var(--muted);">#<?php echo $row['id']; ?></td>
                    <td>
                        <div style="font-weight:600;"><?php echo htmlspecialchars($row['user_name']); ?></div>
                        <div style="color:var(--muted); font-size:0.75rem;"><?php echo htmlspecialchars($row['user_email']); ?></div>
                    </td>
                    <td style="font-weight:500;"><?php echo htmlspecialchars($row['event_title']); ?></td>
                    <td style="color:var(--muted);"><?php echo date('M d, Y', strtotime($row['event_date'])); ?></td>
                    <td style="text-align:center;"><?php echo $row['tickets']; ?></td>
                    <td style="color:var(--muted); font-size:0.8rem;"><?php echo $row['section_numbers']; ?></td>
                    <td style="color:var(--accent); font-weight:600;">₹<?php echo number_format($row['total_price']); ?></td>
                    <td>
                        <span class="ticket-status <?php echo $isPaid ? 'status-paid' : 'status-cancelled'; ?>">
                            <?php echo $isPaid ? '✓ Paid' : '✗ Cancelled'; ?>
                        </span>
                    </td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
        <?php } ?>
    </div>

</div>
</div>

<footer class="footer"><p>© 2026 EventVault — Admin Panel</p></footer>

</body>
</html>