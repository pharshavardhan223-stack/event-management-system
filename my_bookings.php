<?php
session_start();
include 'db.php';

if(!isset($_SESSION['user'])){
    header("Location: login.php");
    exit();
}

$email = $_SESSION['user'];

// Get user
$user = $conn->query("SELECT * FROM users WHERE email='$email'")->fetch_assoc();
$user_id = $user['id'];

// Fetch only user bookings
$result = $conn->query("
    SELECT bookings.*, events.title, events.date, events.location, events.image
    FROM bookings 
    JOIN events ON bookings.event_id = events.id
    WHERE bookings.user_id = '$user_id'
    ORDER BY bookings.id DESC
");
?>

<!DOCTYPE html>
<html>
<head>
    <title>My Tickets</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>

<!-- Navbar -->
<div class="navbar">
    <div><b>Event System</b></div>
    <div>
        <a href="dashboard.php">Home</a>
        <a href="view_events.php">Events</a>
        <a href="my_bookings.php">Bookings</a>
        <a href="logout.php">Logout</a>
    </div>
</div>

<div class="tickets-container">

    <h2 style="text-align:center;">My Tickets 🎟️</h2>

    <div class="tickets-grid">

        <?php while($row = $result->fetch_assoc()){ ?>

            <div class="ticket-card">

                <!-- HEADER -->
                <div class="ticket-header">
                    <img src="uploads/<?php echo $row['image']; ?>">
                    <div>
                        <h3><?php echo $row['title']; ?></h3>
                        <span class="ticket-badge">Event</span>
                    </div>
                </div>

                <div class="ticket-divider"></div>

                <!-- DETAILS -->
                <p><b>Date:</b> <?php echo $row['date']; ?></p>
                <p><b>Location:</b> <?php echo $row['location']; ?></p>

                <div class="ticket-divider"></div>

                <p><b>Tickets:</b> <?php echo $row['tickets']; ?></p>
                <p><b>Sections:</b> <?php echo $row['section_numbers']; ?></p>
                <p><b>Total Price:</b> ₹<?php echo $row['total_price']; ?></p>

                <!-- STATUS -->
                <p style="
                    font-weight:bold;
                    color: <?php echo ($row['payment_status'] == 'Cancelled') ? '#e74c3c' : '#27ae60'; ?>;
                ">
                    Status: <?php echo $row['payment_status']; ?>
                </p>

                <div class="ticket-divider"></div>

                <!-- ACTIONS -->
                <?php if($row['payment_status'] != 'Cancelled'){ ?>
                    <a class="btn cancel-btn"
                       href="cancel_booking.php?id=<?php echo $row['id']; ?>"
                       onclick="return confirm('Are you sure to cancel this ticket?');">
                        Cancel Ticket 
                    </a>
                <?php } else { ?>
                    <p style="color:#e74c3c; font-size:13px;">
                        This ticket has been cancelled
                    </p>
                <?php } ?>

                <div class="ticket-divider"></div>

                <div class="ticket-footer">
                    Show this ticket at entry
                </div>

            </div>

        <?php } ?>

    </div>

</div>

</body>
</html>