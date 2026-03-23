<?php
session_start();
include 'db.php';

if(!isset($_SESSION['user'])){
    header("Location: login.php");
    exit();
}

$email = $_SESSION['user'];
$user = $conn->query("SELECT * FROM users WHERE email='$email'")->fetch_assoc();

$result = $conn->query("SELECT * FROM events");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Events</title>
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

<div class="events-container">

    <h2 style="text-align:center;">Available Events 🎉</h2>

    <div class="events-grid">

        <?php while($row = $result->fetch_assoc()){ ?>

            <div class="event-card">

                <!-- Image -->
                <img src="uploads/<?php echo $row['image']; ?>">

                <!-- Title -->
                <h3><?php echo $row['title']; ?></h3>

                <!-- Badge -->
                <span class="event-badge">Event</span>

                <!-- Description -->
                <p><?php echo $row['description']; ?></p>

                <p><b>Date:</b> <?php echo $row['date']; ?></p>
                <p><b>Location:</b> <?php echo $row['location']; ?></p>
                <p><b>Price:</b> ₹<?php echo $row['price']; ?></p>

                <!-- Buttons -->
                <div class="btn-group">

                    <a class="btn-small btn-book"
                       href="book_ticket.php?id=<?php echo $row['id']; ?>">
                        Book
                    </a>

                    <?php if($user['role'] == 'admin'){ ?>
                        <a class="btn-small btn-edit"
                           href="edit_event.php?id=<?php echo $row['id']; ?>">
                            Edit
                        </a>

                        <a class="btn-small btn-delete"
                           href="delete_event.php?id=<?php echo $row['id']; ?>"
                           onclick="return confirm('Delete this event?');">
                            Delete
                        </a>
                    <?php } ?>

                </div>

            </div>

        <?php } ?>

    </div>

</div>

</body>
</html>