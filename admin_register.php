<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Setup — EventVault</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<?php
include 'db.php';

// ============================================================
//  🔐 CHANGE THIS SECRET KEY TO SOMETHING ONLY YOU KNOW!
// ============================================================
define('ADMIN_SECRET', 'harsha@admin2026');
// ============================================================

$message = "";
$error   = "";
$unlocked = false;

// Step 1: Check secret key
if(isset($_POST['unlock'])){
    if($_POST['secret_key'] === ADMIN_SECRET){
        $unlocked = true;
    } else {
        $error = "Wrong secret key. Access denied.";
    }
}

// Step 2: Register admin
if(isset($_POST['register_admin'])){
    // Re-verify secret key on submit too (security)
    if($_POST['secret_key'] !== ADMIN_SECRET){
        $error = "Unauthorized.";
    } else {
        $name     = mysqli_real_escape_string($conn, $_POST['name']);
        $email    = mysqli_real_escape_string($conn, $_POST['email']);
        $password = $_POST['password'];

        $hashed = password_hash($password, PASSWORD_DEFAULT);

        $check = $conn->query("SELECT * FROM users WHERE email='$email'");

        if($check->num_rows > 0){
            $error   = "This email is already registered.";
            $unlocked = true;
        } else {
            $sql = "INSERT INTO users (name, email, password, role)
                    VALUES ('$name', '$email', '$hashed', 'admin')";

            if($conn->query($sql)){
                $message = "Admin account created! You can now login.";
            } else {
                $error   = "Database error. Please try again.";
                $unlocked = true;
            }
        }
    }
}
?>

<!-- Navbar -->
<nav class="navbar">
    <a href="index.php" class="navbar-brand">🎟 EventVault</a>
    <div class="navbar-links">
        <a href="login.php" class="nav-cta">Login</a>
    </div>
</nav>

<div class="auth-page">
<div class="auth-card" style="max-width:460px;">

    <?php if($message){ ?>

        <!-- SUCCESS STATE -->
        <div class="logo-area">
            <span class="logo-icon">🎉</span>
            <h2>Admin Created!</h2>
            <p class="subtitle">Your admin account is ready to use.</p>
        </div>

        <div class="alert alert-success">
            ✅ <?php echo $message; ?>
        </div>

        <a href="login.php" class="btn btn-primary form-full mt-16">
            Go to Login →
        </a>

        <div class="form-footer" style="margin-top:16px; font-size:0.78rem; color:var(--muted);">
            ⚠️ For security, delete or rename <code style="color:var(--accent);">admin_register.php</code> after use.
        </div>

    <?php } elseif(!$unlocked){ ?>

        <!-- STEP 1: ENTER SECRET KEY -->
        <div class="logo-area">
            <span class="logo-icon">🔑</span>
            <h2>Admin Access</h2>
            <p class="subtitle">Enter the secret key to continue</p>
        </div>

        <?php if($error){ ?>
            <div class="alert alert-error">⚠️ <?php echo $error; ?></div>
        <?php } ?>

        <form method="POST">
            <div class="form-group">
                <label>Secret Key</label>
                <input type="password" name="secret_key" placeholder="Enter secret key" required autofocus>
            </div>

            <button class="btn btn-primary form-full mt-16" name="unlock" type="submit">
                🔓 Unlock
            </button>
        </form>

        <div class="form-footer">
            <a href="login.php">← Back to Login</a>
        </div>

    <?php } else { ?>

        <!-- STEP 2: REGISTER ADMIN FORM -->
        <div class="logo-area">
            <span class="logo-icon">👑</span>
            <h2>Create Admin</h2>
            <p class="subtitle">Set up the admin account details</p>
        </div>

        <?php if($error){ ?>
            <div class="alert alert-error">⚠️ <?php echo $error; ?></div>
        <?php } ?>

        <form method="POST">

            <!-- Pass secret key through hidden field -->
            <input type="hidden" name="secret_key" value="<?php echo htmlspecialchars($_POST['secret_key']); ?>">

            <div class="form-group">
                <label>Full Name</label>
                <input type="text" name="name" placeholder="Admin Name" required autofocus>
            </div>

            <div class="form-group">
                <label>Email Address</label>
                <input type="email" name="email" placeholder="admin@example.com" required>
            </div>

            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" placeholder="Strong password" required>
            </div>

            <!-- Role indicator (read-only) -->
            <div class="form-group">
                <label>Role</label>
                <div style="background:var(--surface2); border:1px solid var(--border);
                            border-radius:var(--radius-sm); padding:13px 16px;
                            display:flex; align-items:center; gap:8px;">
                    <span style="color:var(--accent); font-weight:600;">👑 Admin</span>
                    <span style="color:var(--muted); font-size:0.8rem;">(full access)</span>
                </div>
            </div>

            <button class="btn btn-primary form-full mt-16" name="register_admin" type="submit">
                👑 Create Admin Account
            </button>

        </form>

        <div class="form-footer" style="font-size:0.78rem;">
            ⚠️ Delete <code style="color:var(--accent);">admin_register.php</code> after creating your account.
        </div>

    <?php } ?>

</div>
</div>

</body>
</html>