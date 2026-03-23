<?php
session_start();
include 'db.php';

if(!isset($_SESSION['user'])){
    header("Location: login.php");
    exit();
}

$email = $_SESSION['user'];
$user = $conn->query("SELECT * FROM users WHERE email='$email'")->fetch_assoc();

// Stats
$total_events = $conn->query("SELECT COUNT(*) as total FROM events")->fetch_assoc()['total'];
$total_bookings = $conn->query("SELECT COUNT(*) as total FROM bookings")->fetch_assoc()['total'];
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>

<!-- Navbar -->
<div class="navbar">
    <div><b>Event System</b></div>
    <div>
        <a href="logout.php">🚪 Logout</a>
    </div>
</div>

<div class="dashboard">

    <!-- Welcome -->
    <div class="welcome">
        <h1>Welcome, <?php echo $user['name']; ?> 👋</h1>
        <p>Your central hub for event management.</p>
    </div>

    <!-- Dashboard Cards -->
    <div class="dashboard-grid">

        <!-- Create -->
        <div class="dashboard-card">
            <img src="https://cdn-icons-png.flaticon.com/512/2921/2921222.png">
            <h3>CREATE & DESIGN</h3>
            <p>Create and manage your own events easily.</p>
            <a class="btn" href="create_event.php">GO TO CREATE</a>
        </div>

        <!-- Explore -->
        <div class="dashboard-card">
            <img src="https://cdn-icons-png.flaticon.com/512/747/747310.png">
            <h3>EXPLORE EVENTS</h3>
            <p>Browse and discover events happening around you.</p>
            <a class="btn" href="view_events.php">VIEW EVENTS</a>
        </div>

        <!-- Tickets -->
        <div class="dashboard-card">
            <img src="https://cdn-icons-png.flaticon.com/512/1048/1048313.png">
            <h3>TICKETS & BOOKINGS</h3>
            <p>Track and manage your bookings.</p>
            <a class="btn" href="my_bookings.php">MANAGE TICKETS</a>
        </div>

    </div>

    <!-- Stats Section -->
    <div class="stats">

        <div class="stat-box">
            <h2><?php echo $total_events; ?></h2>
            <p>Active Events</p>
        </div>

        <div class="stat-box">
            <h2><?php echo $total_bookings; ?></h2>
            <p>Total Bookings</p>
        </div>

        <div class="stat-box">
            <h2><?php echo ($user['role'] == 'admin') ? 'Admin' : 'User'; ?></h2>
            <p>Account Type</p>
        </div>

    </div>

    <!-- Recent Activity -->
    <div class="activity">

        <h2>Recent Activity</h2>

        <?php
        $activity = $conn->query("
            SELECT events.title, bookings.created_at 
            FROM bookings
            JOIN events ON bookings.event_id = events.id
            ORDER BY bookings.id DESC LIMIT 5
        ");

        if($activity->num_rows > 0){
            while($row = $activity->fetch_assoc()){
                echo "<p>🎟 Booked: <b>{$row['title']}</b> <span style='color:#777;'>({$row['created_at']})</span></p>";
            }
        } else {
            echo "<p>No recent activity</p>";
        }
        ?>

    </div>

</div>

</body>
</html>