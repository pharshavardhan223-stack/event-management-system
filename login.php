<?php
session_start();
include 'db.php';

$error = "";

if(isset($_POST['login'])){
    $email = $_POST['email'];
    $password = $_POST['password'];

    $result = $conn->query("SELECT * FROM users WHERE email='$email' AND password='$password'");

    if($result->num_rows > 0){
        $_SESSION['user'] = $email;
        header("Location: dashboard.php");
        exit();
    } else {
        $error = "Invalid Email or Password!";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login - Event System</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>

<!-- Navbar -->
<div class="navbar">
    <div><b>Event System</b></div>
    <div>
        <a href="index.php">Home</a>
        <a href="register.php">Register</a>
    </div>
</div>

<!-- Login Form -->
<div class="full-center">

    <div class="auth-box">

        <h2>Login 🔐</h2>

        <form method="POST" class="card">

            <?php if($error != "") { ?>
                <p class="error"><?php echo $error; ?></p>
            <?php } ?>

            <input name="email" placeholder="Enter Email" required>
            <input name="password" type="password" placeholder="Enter Password" required>

            <button class="btn" name="login">Login</button>

            <p style="text-align:center; margin-top:15px;">
                Don't have an account? <a href="register.php">Register</a>
            </p>

        </form>

    </div>

</div>

</body>
</html>