<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — EventVault</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<!-- Navbar -->
<nav class="navbar">
    <a href="index.php" class="navbar-brand">🎟 EventVault</a>
    <div class="navbar-links">
        <a href="index.php">Home</a>
        <a href="register.php" class="nav-cta">Register</a>
    </div>
</nav>

<?php
session_start();
include 'db.php';

$error = "";

if(isset($_POST['login'])){
    $email    = $_POST['email'];
    $password = $_POST['password'];

    $result = $conn->query("SELECT * FROM users WHERE email='$email'");

    if($result->num_rows > 0){
        $user = $result->fetch_assoc();

        // Support both old plain text AND new hashed passwords
        $match = password_verify($password, $user['password'])
                 || ($password === $user['password']);

        if($match){
            // Auto-upgrade plain text password to hashed on first login
            if($password === $user['password']){
                $hashed = password_hash($password, PASSWORD_DEFAULT);
                $conn->query("UPDATE users SET password='$hashed' WHERE email='$email'");
            }
            $_SESSION['user'] = $email;
            header("Location: dashboard.php");
            exit();
        } else {
            $error = "Invalid email or password.";
        }
    } else {
        $error = "Invalid email or password.";
    }
}
?>

<div class="auth-page">
    <div class="auth-card">

        <div class="logo-area">
            <span class="logo-icon">🔐</span>
            <h2>Welcome Back</h2>
            <p class="subtitle">Sign in to your EventVault account</p>
        </div>

        <?php if($error){ ?>
            <div class="alert alert-error">⚠️ <?php echo $error; ?></div>
        <?php } ?>

        <form method="POST">

            <div class="form-group">
                <label>Email Address</label>
                <input type="email" name="email" placeholder="you@example.com" required>
            </div>

            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" placeholder="Enter your password" required>
            </div>

            <button class="btn btn-primary form-full mt-16" name="login" type="submit">
                Sign In →
            </button>

        </form>

        <div class="form-footer">
            Don't have an account? <a href="register.php">Create one</a>
        </div>

    </div>
</div>

</body>
</html>