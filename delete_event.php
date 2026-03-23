<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete Event — EventVault</title>
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

$id    = intval($_GET['id']);
$event = $conn->query("SELECT * FROM events WHERE id='$id'")->fetch_assoc();

if(isset($_POST['confirm_delete'])){
    $conn->query("DELETE FROM events WHERE id='$id'");
    header("Location: view_events.php");
    exit();
}
?>

<nav class="navbar">
    <a href="dashboard.php" class="navbar-brand">🎟 EventVault</a>
    <div class="navbar-links">
        <a href="dashboard.php">Dashboard</a>
        <a href="view_events.php">Events</a>
        <a href="admin.php">Admin</a>
        <a href="logout.php" class="nav-cta">Logout</a>
    </div>
</nav>

<div class="auth-page">
<div class="auth-card" style="max-width:480px;">

    <div class="logo-area">
        <span class="logo-icon">🗑️</span>
        <h2>Delete Event</h2>
        <p class="subtitle">This action cannot be undone</p>
    </div>

    <div class="alert alert-error">
        ⚠️ You are about to permanently delete this event and all its bookings.
    </div>

    <?php if($event['image']){ ?>
        <img src="uploads/<?php echo htmlspecialchars($event['image']); ?>"
             style="width:100%; height:150px; object-fit:cover; border-radius:10px; margin-bottom:16px; border:1px solid var(--border);">
    <?php } ?>

    <div style="background:var(--surface2); border-radius:10px; padding:16px; margin-bottom:24px;">
        <p style="font-family:'Playfair Display',serif; font-size:1.1rem; margin-bottom:8px;">
            <?php echo htmlspecialchars($event['title']); ?>
        </p>
        <p style="color:var(--muted); font-size:0.85rem;">📅 <?php echo date('D, M d Y', strtotime($event['date'])); ?></p>
        <p style="color:var(--muted); font-size:0.85rem;">📍 <?php echo htmlspecialchars($event['location']); ?></p>
        <p style="color:var(--accent); font-size:0.85rem; margin-top:4px;">₹<?php echo number_format($event['price']); ?> / ticket</p>
    </div>

    <form method="POST" style="display:flex; gap:12px;">
        <a href="view_events.php" class="btn btn-ghost" style="flex:1; justify-content:center;">
            ← Cancel
        </a>
        <button name="confirm_delete" type="submit"
                class="btn btn-danger" style="flex:1; justify-content:center; border-radius:50px;">
            🗑 Yes, Delete
        </button>
    </form>

</div>
</div>

</body>
</html>