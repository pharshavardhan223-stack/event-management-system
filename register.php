<?php
include 'db.php';

$message = "";
$error = "";

if(isset($_POST['submit'])){

    // Secure input
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];

    // Encrypt password (IMPORTANT 🔐)
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Default role
    $role = "user";

    // Check existing email
    $check = $conn->query("SELECT * FROM users WHERE email='$email'");

    if($check->num_rows > 0){
        $error = "⚠️ Email already registered!";
    } else {

        $sql = "INSERT INTO users (name,email,password,role)
                VALUES ('$name','$email','$hashed_password','$role')";

        if($conn->query($sql)){
            $message = "✅ Registered Successfully! You can login now.";
        } else {
            $error = "❌ Something went wrong!";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Register - Event System</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>

<!-- Navbar -->
<div class="navbar">
    <div><b>Event System</b></div>
    <div>
        <a href="index.php">Home</a>
        <a href="login.php">Login</a>
    </div>
</div>

<!-- Register Form -->
<div class="full-center">

    <div class="auth-box">

        <div class="card">

            <h2>Register 📝</h2>

            <!-- Messages -->
            <?php if($message){ ?>
                <p class="success"><?php echo $message; ?></p>
            <?php } ?>

            <?php if($error){ ?>
                <p class="error"><?php echo $error; ?></p>
            <?php } ?>

            <form method="POST">

                <input name="name" placeholder="Enter Name" required>

                <input name="email" type="email" placeholder="Enter Email" required>

                <input name="password" type="password" placeholder="Enter Password" required>

                <button class="btn" name="submit">Register</button>

            </form>

            <p style="text-align:center; margin-top:15px;">
                Already have an account? 
                <a href="login.php">Login</a>
            </p>

        </div>

    </div>

</div>

</body>
</html>