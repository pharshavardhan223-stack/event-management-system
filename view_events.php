<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Events — EventVault</title>
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

$email  = $_SESSION['user'];
$user   = $conn->query("SELECT * FROM users WHERE email='$email'")->fetch_assoc();
$result = $conn->query("SELECT * FROM events ORDER BY date ASC");
?>

<!-- Navbar -->
<nav class="navbar">
    <a href="dashboard.php" class="navbar-brand">🎟 EventVault</a>
    <div class="navbar-links">
        <a href="dashboard.php">Home</a>
        <a href="view_events.php">Events</a>
        <a href="my_bookings.php">My Tickets</a>
        <?php if($user['role'] == 'admin'){ ?>
            <a href="admin.php">Admin</a>
        <?php } ?>
        <a href="logout.php" class="nav-cta">Logout</a>
    </div>
</nav>

<div class="page-wrap">
<div class="events-page">

    <!-- Page Header -->
    <div class="page-header">
        <div>
            <p class="section-label">✦ Discover</p>
            <h2>Available Events</h2>
        </div>
        <?php if($user['role'] == 'admin'){ ?>
            <a href="create_event.php" class="btn btn-primary btn-sm">+ Create Event</a>
        <?php } ?>
    </div>

    <!-- Events Grid -->
    <?php if($result->num_rows == 0){ ?>
        <div style="text-align:center; padding:80px 20px;">
            <div style="font-size:4rem; margin-bottom:16px;">🎭</div>
            <h3 style="font-family:'Playfair Display',serif; margin-bottom:8px;">No events yet</h3>
            <p style="color:var(--muted);">Check back soon for upcoming events!</p>
        </div>
    <?php } else { ?>

    <div class="events-grid">

        <?php $i = 0; while($row = $result->fetch_assoc()){ ?>

            <div class="event-card anim-<?php echo min($i+1,4); ?>">

                <!-- Image -->
                <?php if($row['image']){ ?>
                    <img class="event-card-img"
                         src="uploads/<?php echo htmlspecialchars($row['image']); ?>"
                         alt="<?php echo htmlspecialchars($row['title']); ?>">
                <?php } else { ?>
                    <div class="event-card-img-placeholder">🎭</div>
                <?php } ?>

                <!-- Body -->
                <div class="event-card-body">

                    <span class="event-badge">🎫 Event</span>

                    <h3><?php echo htmlspecialchars($row['title']); ?></h3>

                    <div class="event-meta">
                        <div class="event-meta-item">
                            📅 <?php echo date('D, M d Y', strtotime($row['date'])); ?>
                        </div>
                        <div class="event-meta-item">
                            📍 <?php echo htmlspecialchars($row['location']); ?>
                        </div>
                    </div>

                    <div class="event-price">
                        ₹<?php echo number_format($row['price']); ?>
                        <span>/ ticket</span>
                    </div>

                    <!-- Buttons -->
                    <div class="event-actions">

                        <a class="btn btn-ghost btn-sm"
                           href="event_detail.php?id=<?php echo $row['id']; ?>">
                            👁 Details
                        </a>
                        <a class="btn btn-primary btn-sm"
                           href="book_ticket.php?id=<?php echo $row['id']; ?>">
                            🎟 Book
                        </a>

                        <?php if($user['role'] == 'admin'){ ?>
                            <a class="btn btn-ghost btn-sm"
                               href="edit_event.php?id=<?php echo $row['id']; ?>">
                                ✏️ Edit
                            </a>

                            <a class="btn btn-danger btn-sm"
                               href="delete_event.php?id=<?php echo $row['id']; ?>"
                               onclick="return confirm('Delete this event? This cannot be undone.');">
                                🗑
                            </a>
                        <?php } ?>

                    </div>

                </div>

            </div>

        <?php $i++; } ?>

    </div>

    <?php } ?>

</div>
</div>

<footer class="footer">
    <p>© 2026 EventVault — Harsha Vardhan</p>
</footer>

</body>
</html>