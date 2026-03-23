<?php
include 'db.php';

$id = $_GET['id'];

// Fetch old data
$result = $conn->query("SELECT * FROM events WHERE id='$id'");
$event = $result->fetch_assoc();

$message = "";

if(isset($_POST['update'])){
    $title = $_POST['title'];
    $description = $_POST['description'];
    $date = $_POST['date'];
    $location = $_POST['location'];
    $price = $_POST['price'];

    // Check if new image uploaded
    if($_FILES['image']['name'] != ""){
        $image = $_FILES['image']['name'];
        $temp = $_FILES['image']['tmp_name'];
        move_uploaded_file($temp, "uploads/".$image);

        $sql = "UPDATE events SET 
                title='$title',
                description='$description',
                date='$date',
                location='$location',
                price='$price',
                image='$image'
                WHERE id='$id'";
    } else {
        $sql = "UPDATE events SET 
                title='$title',
                description='$description',
                date='$date',
                location='$location',
                price='$price'
                WHERE id='$id'";
    }

    if($conn->query($sql)){
        $message = "Event Updated Successfully!";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Event</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="container">
    <div class="form-box">

        <h2>Edit Event ✏️</h2>

        <form method="POST" enctype="multipart/form-data" class="card">

            <?php if($message != "") echo "<p class='success'>$message</p>"; ?>

            <input name="title" value="<?php echo $event['title']; ?>" required>
            <textarea name="description"><?php echo $event['description']; ?></textarea>
            <input type="date" name="date" value="<?php echo $event['date']; ?>">
            <input name="location" value="<?php echo $event['location']; ?>">
            <input name="price" type="number" value="<?php echo $event['price']; ?>">

            <!-- Show current image -->
            <img src="uploads/<?php echo $event['image']; ?>" width="100">

            <input type="file" name="image">

            <button class="btn" name="update">Update Event</button>

        </form>

    </div>
</div>

</body>
</html>