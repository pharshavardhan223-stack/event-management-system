<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Event — EventVault</title>
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

$message = "";
$error   = "";

if(isset($_POST['submit'])){
    $title       = mysqli_real_escape_string($conn, $_POST['title']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $date        = $_POST['date'];
    $location    = mysqli_real_escape_string($conn, $_POST['location']);
    $price       = intval($_POST['price']);

    $image  = $_FILES['image']['name'];
    $temp   = $_FILES['image']['tmp_name'];
    $folder = "uploads/" . basename($image);

    if(move_uploaded_file($temp, $folder)){
        $sql = "INSERT INTO events (title, description, date, location, price, image)
                VALUES ('$title','$description','$date','$location','$price','$image')";
        if($conn->query($sql)){
            $message = "Event created successfully!";
        } else {
            $error = "Database error. Please try again.";
        }
    } else {
        $error = "Image upload failed. Check uploads/ folder permissions.";
    }
}
?>

<!-- Navbar -->
<nav class="navbar">
    <a href="dashboard.php" class="navbar-brand">🎟 EventVault</a>
    <div class="navbar-links">
        <a href="dashboard.php">Dashboard</a>
        <a href="view_events.php">Events</a>
        <a href="admin.php">Admin</a>
        <a href="logout.php" class="nav-cta">Logout</a>
    </div>
</nav>

<div class="auth-page" style="align-items:flex-start; padding-top:100px;">
<div class="auth-card" style="max-width:560px;">

    <div class="logo-area">
        <span class="logo-icon">🎤</span>
        <h2>Create Event</h2>
        <p class="subtitle">Publish a new event for users to discover</p>
    </div>

    <?php if($message){ ?>
        <div class="alert alert-success">✅ <?php echo $message; ?>
            <a href="view_events.php" style="color:var(--green); margin-left:8px; font-weight:600;">View Events →</a>
        </div>
    <?php } ?>
    <?php if($error){ ?>
        <div class="alert alert-error">⚠️ <?php echo $error; ?></div>
    <?php } ?>

    <form method="POST" enctype="multipart/form-data">

        <div class="form-group">
            <label>Event Title</label>
            <input type="text" name="title" placeholder="e.g. Tech Summit 2026" required>
        </div>

        <div class="form-group">
            <label>Description</label>
            <textarea name="description" placeholder="Describe the event..." rows="4" style="resize:vertical;" required></textarea>
        </div>

        <div class="form-group">
            <label>Event Date</label>
            <input type="date" name="date" required>
        </div>

        <div class="form-group">
            <label>Location</label>
            <input type="text" name="location" placeholder="e.g. Chennai Trade Centre" required>
        </div>

        <div class="form-group">
            <label>Ticket Price (₹)</label>
            <input type="number" name="price" placeholder="e.g. 499" min="0" required>
        </div>

        <div class="form-group">
            <label>Event Banner Image</label>
            <input type="file" name="image" accept="image/*" required style="padding:10px; cursor:pointer;">
        </div>

        <button class="btn btn-primary form-full mt-16" name="submit" type="submit">
            🎤 Publish Event
        </button>

        <div class="form-footer">
            <a href="view_events.php">← Back to Events</a>
        </div>

    </form>
</div>
</div>

</body>
</html>