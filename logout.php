<link rel="stylesheet" href="style.css">
<div style="background:black; padding:10px; color:white;">
    <a href="dashboard.php" style="color:white;">Home</a> |
    <a href="view_events.php" style="color:white;">Events</a> |
    <a href="my_bookings.php" style="color:white;">Bookings</a> |
    <a href="logout.php" style="color:white;">Logout</a>
</div>
<?php
session_start();
session_destroy();
header("Location: login.php");
?>