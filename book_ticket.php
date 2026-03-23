<?php
session_start();
include 'db.php';

if(!isset($_SESSION['user'])){
    header("Location: login.php");
    exit();
}

$event_id = $_GET['id'];

// Fetch event
$event = $conn->query("SELECT * FROM events WHERE id='$event_id'")->fetch_assoc();

$email = $_SESSION['user'];
$user = $conn->query("SELECT * FROM users WHERE email='$email'")->fetch_assoc();
$user_id = $user['id'];

$success = "";

if(isset($_POST['book'])){
    $tickets = $_POST['tickets'];
    $price = $event['price'];
    $total = $tickets * $price;

    // Generate sections
    $sections = [];
    for($i = 0; $i < $tickets; $i++){
        $sections[] = "S" . rand(100,999);
    }
    $section_numbers = implode(",", $sections);

    $conn->query("INSERT INTO bookings (user_id,event_id,tickets,section_numbers,total_price,payment_status)
                  VALUES ('$user_id','$event_id','$tickets','$section_numbers','$total','Paid')");

    $success = "Ticket Booked Successfully!";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Book Ticket</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>

<div class="navbar">
    <div><b>Event System</b></div>
    <div>
        <a href="dashboard.php">Home</a>
        <a href="view_events.php">Events</a>
        <a href="my_bookings.php">Bookings</a>
        <a href="logout.php">Logout</a>
    </div>
</div>

<div class="booking-page">

    <h2>Book Ticket 🎟️</h2>

    <div class="booking-layout">

        <!-- LEFT: EVENT DETAILS -->
        <div class="booking-card">
            <small>EVENT DETAILS</small>
            <h3><?php echo $event['title']; ?></h3>

            <p><?php echo $event['description']; ?></p>

            <hr>

            <div class="booking-info">
                <p><b>Date:</b> <?php echo $event['date']; ?></p>
                <p><b>Location:</b> <?php echo $event['location']; ?></p>
                <p><b>Price:</b> ₹<?php echo $event['price']; ?> / Ticket</p>
            </div>
        </div>

        <!-- RIGHT: BOOKING FORM -->
        <div class="booking-card">

            <small>BOOKING FORM</small>

            <?php if($success){ ?>
                <p style="color:green;"><?php echo $success; ?></p>
            <?php } ?>

            <form method="POST">

                <label>Tickets to purchase:</label>
                <input type="number" id="tickets" name="tickets" value="1" min="1" required>

                <br><br>

                <p>Ticket Price: ₹<?php echo $event['price']; ?></p>
                <p>Quantity: <span id="qty">1</span></p>
                <p><b>Subtotal: ₹<span id="total"><?php echo $event['price']; ?></span></b></p>

                <button class="booking-btn" name="book">
                    Confirm Booking
                </button>

            </form>

        </div>

    </div>

</div>

<!-- LIVE CALCULATION -->
<script>
let price = <?php echo $event['price']; ?>;
let input = document.getElementById("tickets");

input.addEventListener("input", function(){
    let qty = input.value || 1;
    document.getElementById("qty").innerText = qty;
    document.getElementById("total").innerText = qty * price;
});
</script>

</body>
</html>