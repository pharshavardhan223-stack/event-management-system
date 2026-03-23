<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Ticket — EventVault</title>
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

$event_id = $_GET['id'];
$event    = $conn->query("SELECT * FROM events WHERE id='$event_id'")->fetch_assoc();

$email   = $_SESSION['user'];
$user    = $conn->query("SELECT * FROM users WHERE email='$email'")->fetch_assoc();
$user_id = $user['id'];

$success = "";

if(isset($_POST['book'])){
    $tickets = intval($_POST['tickets']);
    $price   = $event['price'];
    $total   = $tickets * $price;

    $sections = [];
    for($i = 0; $i < $tickets; $i++){
        $sections[] = "S" . rand(100, 999);
    }
    $section_numbers = implode(",", $sections);

    $conn->query("INSERT INTO bookings (user_id, event_id, tickets, section_numbers, total_price, payment_status)
                  VALUES ('$user_id','$event_id','$tickets','$section_numbers','$total','Paid')");

    $success = "Ticket Booked Successfully!";
}
?>

<!-- Navbar -->
<nav class="navbar">
    <a href="dashboard.php" class="navbar-brand">🎟 EventVault</a>
    <div class="navbar-links">
        <a href="dashboard.php">Home</a>
        <a href="view_events.php">Events</a>
        <a href="my_bookings.php">My Tickets</a>
        <a href="logout.php" class="nav-cta">Logout</a>
    </div>
</nav>

<div class="page-wrap">
<div class="booking-page">

    <p class="section-label">✦ Secure Booking</p>
    <h2 class="section-title" style="text-align:left; margin-bottom:0;"><?php echo htmlspecialchars($event['title']); ?></h2>

    <?php if($success){ ?>
        <div class="alert alert-success mt-24">
            ✅ <?php echo $success; ?> 
            <a href="my_bookings.php" style="color:var(--green); margin-left:8px; font-weight:600;">View Ticket →</a>
        </div>
    <?php } ?>

    <div class="booking-layout">

        <!-- LEFT: EVENT DETAILS -->
        <div class="booking-card anim-1">
            <span class="card-label">Event Details</span>
            <h3><?php echo htmlspecialchars($event['title']); ?></h3>

            <?php if($event['image']){ ?>
                <img src="uploads/<?php echo $event['image']; ?>" style="width:100%; border-radius:10px; margin-bottom:16px; height:180px; object-fit:cover;">
            <?php } ?>

            <p><?php echo htmlspecialchars($event['description']); ?></p>

            <hr class="booking-divider">

            <p>📅 <b>Date:</b> <?php echo date('D, M d Y', strtotime($event['date'])); ?></p>
            <p style="margin-top:6px;">📍 <b>Location:</b> <?php echo htmlspecialchars($event['location']); ?></p>
            <p style="margin-top:10px; font-size:1.3rem; font-weight:700; color:var(--accent);">
                ₹<?php echo number_format($event['price']); ?> <span style="font-size:0.8rem; color:var(--muted); font-weight:400;">per ticket</span>
            </p>
        </div>

        <!-- RIGHT: BOOKING FORM -->
        <div class="booking-card anim-2">
            <span class="card-label">Booking Form</span>
            <h3>Reserve Your Spot</h3>
            <hr class="booking-divider">

            <form method="POST">

                <div class="form-group">
                    <label>Number of Tickets</label>
                    <input type="number" id="tickets" name="tickets" value="1" min="1" max="10" required>
                </div>

                <div class="price-summary">
                    <div class="price-row">
                        <span>Price per ticket</span>
                        <span>₹<?php echo number_format($event['price']); ?></span>
                    </div>
                    <div class="price-row">
                        <span>Quantity</span>
                        <span id="qty-display">1</span>
                    </div>
                    <div class="price-row total">
                        <span>Total</span>
                        <span class="amount" id="total-display">₹<?php echo number_format($event['price']); ?></span>
                    </div>
                </div>

                <button class="btn btn-primary form-full" name="book" type="submit">
                    🎟️ Confirm Booking
                </button>

            </form>
        </div>

    </div>
</div>
</div>

<footer class="footer">
    <p>© 2026 EventVault — Harsha Vardhan</p>
</footer>

<script>
const price = <?php echo (int)$event['price']; ?>;
const input = document.getElementById("tickets");
const qtyDisplay   = document.getElementById("qty-display");
const totalDisplay = document.getElementById("total-display");

input.addEventListener("input", function(){
    const qty = parseInt(input.value) || 1;
    qtyDisplay.innerText   = qty;
    totalDisplay.innerText = "₹" + (qty * price).toLocaleString('en-IN');
});
</script>

</body>
</html>