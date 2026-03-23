<?php
session_start();
include 'db.php';

if(!isset($_SESSION['user'])){
    header("Location: login.php");
    exit();
}

if(isset($_GET['id'])){
    $booking_id = intval($_GET['id']);
    $email      = $_SESSION['user'];
    $user       = $conn->query("SELECT * FROM users WHERE email='$email'")->fetch_assoc();

    // Security: only cancel if booking belongs to this user
    $conn->query("UPDATE bookings SET payment_status='Cancelled'
                  WHERE id='$booking_id' AND user_id='{$user['id']}'");
}

header("Location: my_bookings.php");
exit();
?>