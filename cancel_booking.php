<?php
session_start();
include 'db.php';

if(!isset($_SESSION['user'])){
    header("Location: login.php");
    exit();
}

if(isset($_GET['id'])){

    $booking_id = $_GET['id'];

    // Update booking status
    $conn->query("UPDATE bookings SET payment_status='Cancelled' WHERE id='$booking_id'");

}

header("Location: my_bookings.php");
exit();
?>