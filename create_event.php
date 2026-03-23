<?php
session_start();
include 'db.php';

$message = "";
$error = "";

if(isset($_POST['submit'])){
    $title = $_POST['title'];
    $description = $_POST['description'];
    $date = $_POST['date'];
    $location = $_POST['location'];
    $price = $_POST['price'];

    // Image Upload
    $image = $_FILES['image']['name'];
    $temp = $_FILES['image']['tmp_name'];
    $folder = "uploads/" . $image;

    if(move_uploaded_file($temp, $folder)){

        $sql = "INSERT INTO events (title, description, date, location, price, image)
                VALUES ('$title', '$description', '$date', '$location', '$price', '$image')";

        if($conn->query($sql)){
            $message = "Event Created Successfully!";
        } else {
            $error = "Database Error!";
        }

    } else {
        $error = "Image Upload Failed!";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Create Event</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="navbar">
    <div><b>Event System</b></div>
</div>

<div class="container">
    <div class="form-box">

        <h2>Create Event 🎤</h2>

        <form method="POST" enctype="multipart/form-data" class="card">

            <?php if($message != "") echo "<p class='success'>$message</p>"; ?>
            <?php if($error != "") echo "<p class='error'>$error</p>"; ?>

            <input name="title" placeholder="Event Title" required>
            <textarea name="description" placeholder="Description" required></textarea>
            <input type="date" name="date" required>
            <input name="location" placeholder="Location" required>
            <input name="price" type="number" placeholder="Price" required>

            <input type="file" name="image" required>

            <button class="btn" name="submit">Create Event</button>

        </form>

    </div>
</div>

</body>
</html>