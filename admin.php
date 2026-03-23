<?php
session_start();
include 'db.php';

if(!isset($_SESSION['user'])){
    header("Location: login.php");
    exit();
}

$email = $_SESSION['user'];
$user = $conn->query("SELECT * FROM users WHERE email='$email'")->fetch_assoc();

// Only admin allowed
if($user['role'] != 'admin'){
    echo "Access Denied!";
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Panel</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>

<div class="navbar">
    <div><b>Admin Panel</b></div>
    <div>
        <a href="dashboard.php">Dashboard</a>
        <a href="logout.php">Logout</a>
    </div>
</div>

<div class="dashboard">

    <h2 style="text-align:center;">Admin Controls ⚙️</h2>

    <div class="dashboard-grid">

        <div class="dashboard-card">
            <h3>Manage Events</h3>
            <a class="btn" href="view_events.php">Go</a>
        </div>

        <div class="dashboard-card">
            <h3>View Bookings</h3>
            <a class="btn" href="admin_bookings.php">Go</a>
        </div>

        <div class="dashboard-card">
            <h3>Manage Users</h3>
            <a class="btn" href="admin_users.php">Go</a>
        </div>

    </div>

</div>

</body>
</html>