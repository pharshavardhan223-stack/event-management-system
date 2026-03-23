<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users — EventVault</title>
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

$users       = $conn->query("SELECT * FROM users ORDER BY id DESC");
$total_users = $conn->query("SELECT COUNT(*) as t FROM users")->fetch_assoc()['t'];
$admins      = $conn->query("SELECT COUNT(*) as t FROM users WHERE role='admin'")->fetch_assoc()['t'];
$members     = $conn->query("SELECT COUNT(*) as t FROM users WHERE role='user'")->fetch_assoc()['t'];
?>

<nav class="navbar">
    <a href="dashboard.php" class="navbar-brand">🎟 EventVault</a>
    <div class="navbar-links">
        <a href="admin.php">Admin</a>
        <a href="view_events.php">Events</a>
        <a href="logout.php" class="nav-cta">Logout</a>
    </div>
</nav>

<div class="page-wrap">
<div class="admin-page">

    <div class="page-header">
        <div>
            <p class="section-label">⚙️ Admin</p>
            <h2 style="font-family:'Playfair Display',serif; font-size:2rem;">Manage Users</h2>
        </div>
        <a href="admin.php" class="btn btn-ghost btn-sm">← Back to Admin</a>
    </div>

    <!-- Stats -->
    <div class="stats-row anim-1" style="margin-bottom:28px;">
        <div class="stat-card">
            <span class="stat-num"><?php echo $total_users; ?></span>
            <span class="stat-label">Total Users</span>
        </div>
        <div class="stat-card">
            <span class="stat-num" style="color:var(--accent);"><?php echo $admins; ?></span>
            <span class="stat-label">Admins</span>
        </div>
        <div class="stat-card">
            <span class="stat-num"><?php echo $members; ?></span>
            <span class="stat-label">Members</span>
        </div>
    </div>

    <!-- Table -->
    <div class="table-wrap anim-2">
        <?php if($users->num_rows == 0){ ?>
            <div style="text-align:center; padding:60px; color:var(--muted);">No users found.</div>
        <?php } else { ?>
        <table class="data-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Bookings</th>
                </tr>
            </thead>
            <tbody>
            <?php while($u = $users->fetch_assoc()){
                $isAdmin   = ($u['role'] == 'admin');
                $bookCount = $conn->query("SELECT COUNT(*) as t FROM bookings WHERE user_id='{$u['id']}'")->fetch_assoc()['t'];
            ?>
                <tr>
                    <td style="color:var(--muted);">#<?php echo $u['id']; ?></td>
                    <td>
                        <div style="display:flex; align-items:center; gap:10px;">
                            <div style="width:34px; height:34px; border-radius:50%; background:var(--accent-glow);
                                        border:1px solid rgba(201,168,76,0.3);
                                        display:flex; align-items:center; justify-content:center;
                                        font-size:0.85rem; font-weight:700; color:var(--accent); flex-shrink:0;">
                                <?php echo strtoupper(substr($u['name'], 0, 1)); ?>
                            </div>
                            <span style="font-weight:600;"><?php echo htmlspecialchars($u['name']); ?></span>
                        </div>
                    </td>
                    <td style="color:var(--muted);"><?php echo htmlspecialchars($u['email']); ?></td>
                    <td>
                        <span class="ticket-status <?php echo $isAdmin ? 'status-paid' : ''; ?>"
                              style="<?php echo !$isAdmin ? 'background:var(--surface2); color:var(--muted); border:1px solid var(--border);' : ''; ?>">
                            <?php echo $isAdmin ? '⭐ Admin' : '👤 User'; ?>
                        </span>
                    </td>
                    <td style="text-align:center; color:var(--accent); font-weight:600;"><?php echo $bookCount; ?></td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
        <?php } ?>
    </div>

</div>
</div>

<footer class="footer"><p>© 2026 EventVault — Admin Panel</p></footer>

</body>
</html>