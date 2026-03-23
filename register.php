<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register — EventVault</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<!-- Navbar -->
<nav class="navbar">
    <a href="index.php" class="navbar-brand">🎟 EventVault</a>
    <div class="navbar-links">
        <a href="index.php">Home</a>
        <a href="login.php" class="nav-cta">Login</a>
    </div>
</nav>

<?php
include 'db.php';

$message = "";
$error = "";

if(isset($_POST['submit'])){
    $name     = mysqli_real_escape_string($conn, $_POST['name']);
    $email    = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];

    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    $role = "user";

    $check = $conn->query("SELECT * FROM users WHERE email='$email'");

    if($check->num_rows > 0){
        $error = "This email is already registered.";
    } else {
        $sql = "INSERT INTO users (name,email,password,role)
                VALUES ('$name','$email','$hashed_password','$role')";
        if($conn->query($sql)){
            $message = "Account created! You can now log in.";
        } else {
            $error = "Something went wrong. Please try again.";
        }
    }
}
?>

<div class="auth-page">
    <div class="auth-card">

        <div class="logo-area">
            <span class="logo-icon">📝</span>
            <h2>Create Account</h2>
            <p class="subtitle">Join EventVault and start booking</p>
        </div>

        <?php if($message){ ?>
            <div class="alert alert-success">✅ <?php echo $message; ?></div>
        <?php } ?>

        <?php if($error){ ?>
            <div class="alert alert-error">⚠️ <?php echo $error; ?></div>
        <?php } ?>

        <form method="POST">

            <div class="form-group">
                <label>Full Name</label>
                <input type="text" name="name" placeholder="Harsha Vardhan" required>
            </div>

            <div class="form-group">
                <label>Email Address</label>
                <input type="email" name="email" placeholder="you@example.com" required>
            </div>

            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" placeholder="Create a strong password" required>
            </div>

            <button class="btn btn-primary form-full mt-16" name="submit" type="submit">
                Create Account →
            </button>

        </form>

        <div class="form-footer">
            Already have an account? <a href="login.php">Sign in</a>
        </div>

    </div>
</div>

</body>
</html>