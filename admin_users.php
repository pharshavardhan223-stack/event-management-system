<?php
session_start();
include 'db.php';

$email = $_SESSION['user'];
$user = $conn->query("SELECT * FROM users WHERE email='$email'")->fetch_assoc();

if($user['role'] != 'admin'){
    exit("Access Denied");
}

$result = $conn->query("
    SELECT bookings.*, users.name, events.title 
    FROM bookings
    JOIN users ON bookings.user_id = users.id
    JOIN events ON bookings.event_id = events.id
");
?>

<!DOCTYPE html>
<html>
<head>
    <title>All Bookings</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>

<div class="navbar">
    <div><b>All Bookings</b></div>
</div>

<div class="container">

<?php while($row = $result->fetch_assoc()){ ?>

<div class="card">
    <h3><?php echo $row['title']; ?></h3>
    <p>User: <?php echo $row['name']; ?></p>
    <p>Tickets: <?php echo $row['tickets']; ?></p>
    <p>Total: ₹<?php echo $row['total_price']; ?></p>
    <p>Status: <?php echo $row['payment_status']; ?></p>
</div>

<?php } ?>

</div>

</body>
</html>