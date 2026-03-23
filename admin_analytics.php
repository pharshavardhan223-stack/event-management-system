<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Analytics — EventVault</title>
    <link rel="stylesheet" href="style.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>
    <style>
        .chart-card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 28px;
            margin-bottom: 24px;
        }
        .chart-card h3 {
            font-family: 'Playfair Display', serif;
            font-size: 1.1rem;
            margin-bottom: 6px;
        }
        .chart-card .chart-sub {
            color: var(--muted);
            font-size: 0.8rem;
            margin-bottom: 24px;
        }
        .chart-wrap {
            position: relative;
            height: 280px;
        }
        .charts-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 24px;
        }
        @media(max-width:768px){ .charts-grid { grid-template-columns: 1fr; } }

        .top-event-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 12px 0;
            border-bottom: 1px solid var(--border);
            font-size: 0.875rem;
        }
        .top-event-row:last-child { border-bottom: none; }
        .top-event-bar-wrap {
            flex: 1; margin: 0 16px;
            background: var(--surface2);
            border-radius: 50px;
            height: 6px;
            overflow: hidden;
        }
        .top-event-bar {
            height: 100%;
            background: linear-gradient(90deg, var(--accent), var(--accent2));
            border-radius: 50px;
            transition: width 1s ease;
        }
    </style>
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

// ── Stats ────────────────────────────────────────────────
$total_events    = $conn->query("SELECT COUNT(*) as t FROM events")->fetch_assoc()['t'];
$total_bookings  = $conn->query("SELECT COUNT(*) as t FROM bookings")->fetch_assoc()['t'];
$total_users     = $conn->query("SELECT COUNT(*) as t FROM users")->fetch_assoc()['t'];
$total_revenue   = $conn->query("SELECT SUM(total_price) as t FROM bookings WHERE payment_status='Paid'")->fetch_assoc()['t'] ?? 0;
$cancelled_count = $conn->query("SELECT COUNT(*) as t FROM bookings WHERE payment_status='Cancelled'")->fetch_assoc()['t'];
$paid_count      = $conn->query("SELECT COUNT(*) as t FROM bookings WHERE payment_status='Paid'")->fetch_assoc()['t'];

// ── Bookings per event (bar chart) ───────────────────────
$bpe = $conn->query("
    SELECT events.title, COUNT(bookings.id) as total
    FROM events
    LEFT JOIN bookings ON events.id = bookings.event_id AND bookings.payment_status='Paid'
    GROUP BY events.id
    ORDER BY total DESC
    LIMIT 8
");
$bpe_labels = []; $bpe_data = [];
while($r = $bpe->fetch_assoc()){
    $bpe_labels[] = strlen($r['title']) > 20 ? substr($r['title'],0,20).'…' : $r['title'];
    $bpe_data[]   = (int)$r['total'];
}

// ── Revenue per event ────────────────────────────────────
$rpe = $conn->query("
    SELECT events.title, SUM(bookings.total_price) as revenue
    FROM bookings
    JOIN events ON bookings.event_id = events.id
    WHERE bookings.payment_status='Paid'
    GROUP BY events.id
    ORDER BY revenue DESC
    LIMIT 6
");
$rpe_labels = []; $rpe_data = [];
while($r = $rpe->fetch_assoc()){
    $rpe_labels[] = strlen($r['title']) > 18 ? substr($r['title'],0,18).'…' : $r['title'];
    $rpe_data[]   = (float)$r['revenue'];
}

// ── Top events by bookings (for table) ──────────────────
$top_events = $conn->query("
    SELECT events.title, COUNT(bookings.id) as total, SUM(bookings.total_price) as revenue
    FROM events
    LEFT JOIN bookings ON events.id = bookings.event_id AND bookings.payment_status='Paid'
    GROUP BY events.id
    ORDER BY total DESC
    LIMIT 5
");
$top_max = max(1, $bpe_data[0] ?? 1);

// ── Registrations per month ──────────────────────────────
$monthly = $conn->query("
    SELECT DATE_FORMAT(created_at, '%b') as month,
           DATE_FORMAT(created_at, '%Y-%m') as ym,
           COUNT(*) as total
    FROM bookings
    GROUP BY ym
    ORDER BY ym ASC
    LIMIT 6
");
$month_labels = []; $month_data = [];
while($r = $monthly->fetch_assoc()){
    $month_labels[] = $r['month'];
    $month_data[]   = (int)$r['total'];
}
if(empty($month_labels)){ $month_labels = ['No data']; $month_data = [0]; }
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
<div class="dashboard-page">

    <div class="page-header anim-1">
        <div>
            <p class="section-label">⚙️ Admin</p>
            <h2 style="font-family:'Playfair Display',serif; font-size:2rem;">Analytics</h2>
        </div>
        <a href="admin.php" class="btn btn-ghost btn-sm">← Back to Admin</a>
    </div>

    <!-- KPI Stats -->
    <div class="stats-row anim-2" style="grid-template-columns:repeat(4,1fr); margin-bottom:28px;">
        <div class="stat-card">
            <span class="stat-num"><?php echo $total_events; ?></span>
            <span class="stat-label">Total Events</span>
        </div>
        <div class="stat-card">
            <span class="stat-num"><?php echo $total_bookings; ?></span>
            <span class="stat-label">Total Bookings</span>
        </div>
        <div class="stat-card">
            <span class="stat-num"><?php echo $total_users; ?></span>
            <span class="stat-label">Registered Users</span>
        </div>
        <div class="stat-card">
            <span class="stat-num">₹<?php echo number_format($total_revenue); ?></span>
            <span class="stat-label">Total Revenue</span>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="charts-grid anim-3">

        <!-- Bookings per Event (Bar) -->
        <div class="chart-card">
            <h3>Bookings per Event</h3>
            <p class="chart-sub">Number of confirmed ticket bookings per event</p>
            <div class="chart-wrap">
                <canvas id="bookingsChart"></canvas>
            </div>
        </div>

        <!-- Booking Status (Doughnut) -->
        <div class="chart-card">
            <h3>Booking Status</h3>
            <p class="chart-sub">Ratio of confirmed vs cancelled bookings</p>
            <div class="chart-wrap">
                <canvas id="statusChart"></canvas>
            </div>
        </div>

    </div>

    <!-- Monthly Bookings (Line) -->
    <div class="chart-card anim-4">
        <h3>Monthly Booking Trend</h3>
        <p class="chart-sub">Total bookings recorded each month</p>
        <div class="chart-wrap" style="height:220px;">
            <canvas id="monthlyChart"></canvas>
        </div>
    </div>

    <!-- Revenue per Event (Horizontal Bar) -->
    <?php if(!empty($rpe_labels)){ ?>
    <div class="chart-card anim-4">
        <h3>Revenue per Event</h3>
        <p class="chart-sub">Total revenue generated per event (confirmed bookings only)</p>
        <div class="chart-wrap" style="height:<?php echo max(200, count($rpe_labels) * 50); ?>px;">
            <canvas id="revenueChart"></canvas>
        </div>
    </div>
    <?php } ?>

    <!-- Top Events Table -->
    <div class="chart-card anim-4">
        <h3>Top Events by Bookings</h3>
        <p class="chart-sub">Most booked events on the platform</p>

        <?php
        $top_events->data_seek(0);
        $i = 1;
        while($row = $top_events->fetch_assoc()){
            $pct = $top_max > 0 ? round(($row['total'] / $top_max) * 100) : 0;
        ?>
        <div class="top-event-row">
            <span style="color:var(--muted); width:20px;"><?php echo $i; ?></span>
            <span style="font-weight:500; width:200px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">
                <?php echo htmlspecialchars($row['title']); ?>
            </span>
            <div class="top-event-bar-wrap">
                <div class="top-event-bar" style="width:<?php echo $pct; ?>%"></div>
            </div>
            <span style="color:var(--accent); font-weight:600; width:60px; text-align:right;">
                <?php echo $row['total']; ?> 🎟
            </span>
            <span style="color:var(--muted); width:90px; text-align:right; font-size:0.8rem;">
                ₹<?php echo number_format($row['revenue'] ?? 0); ?>
            </span>
        </div>
        <?php $i++; } ?>
    </div>

</div>
</div>

<footer class="footer"><p>© 2026 EventVault — Analytics</p></footer>

<script>
// ── Chart.js Global Defaults ────────────────────────────
Chart.defaults.color = '#7a7a8c';
Chart.defaults.borderColor = 'rgba(255,255,255,0.07)';
Chart.defaults.font.family = "'DM Sans', sans-serif";

const gold  = '#c9a84c';
const gold2 = '#e8c97a';
const red   = '#e05c5c';
const green = '#4caf7d';
const blue  = '#5b8dee';

// ── 1. Bookings per Event (Bar) ─────────────────────────
new Chart(document.getElementById('bookingsChart'), {
    type: 'bar',
    data: {
        labels: <?php echo json_encode($bpe_labels); ?>,
        datasets: [{
            label: 'Bookings',
            data: <?php echo json_encode($bpe_data); ?>,
            backgroundColor: 'rgba(201,168,76,0.25)',
            borderColor: gold,
            borderWidth: 2,
            borderRadius: 8,
            borderSkipped: false,
        }]
    },
    options: {
        responsive: true, maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: {
            x: { grid: { color: 'rgba(255,255,255,0.05)' }, ticks: { maxRotation: 30 } },
            y: { grid: { color: 'rgba(255,255,255,0.05)' }, beginAtZero: true, ticks: { stepSize: 1 } }
        }
    }
});

// ── 2. Booking Status (Doughnut) ────────────────────────
new Chart(document.getElementById('statusChart'), {
    type: 'doughnut',
    data: {
        labels: ['Confirmed', 'Cancelled'],
        datasets: [{
            data: [<?php echo $paid_count; ?>, <?php echo $cancelled_count; ?>],
            backgroundColor: ['rgba(76,175,125,0.8)', 'rgba(224,92,92,0.8)'],
            borderColor: ['#4caf7d', '#e05c5c'],
            borderWidth: 2,
            hoverOffset: 8,
        }]
    },
    options: {
        responsive: true, maintainAspectRatio: false,
        cutout: '65%',
        plugins: {
            legend: {
                position: 'bottom',
                labels: { padding: 20, usePointStyle: true }
            }
        }
    }
});

// ── 3. Monthly Trend (Line) ──────────────────────────────
new Chart(document.getElementById('monthlyChart'), {
    type: 'line',
    data: {
        labels: <?php echo json_encode($month_labels); ?>,
        datasets: [{
            label: 'Bookings',
            data: <?php echo json_encode($month_data); ?>,
            borderColor: gold,
            backgroundColor: 'rgba(201,168,76,0.1)',
            borderWidth: 2.5,
            pointBackgroundColor: gold,
            pointRadius: 5,
            pointHoverRadius: 7,
            fill: true,
            tension: 0.4,
        }]
    },
    options: {
        responsive: true, maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: {
            x: { grid: { color: 'rgba(255,255,255,0.05)' } },
            y: { grid: { color: 'rgba(255,255,255,0.05)' }, beginAtZero: true, ticks: { stepSize: 1 } }
        }
    }
});

<?php if(!empty($rpe_labels)){ ?>
// ── 4. Revenue per Event (Horizontal Bar) ───────────────
new Chart(document.getElementById('revenueChart'), {
    type: 'bar',
    data: {
        labels: <?php echo json_encode($rpe_labels); ?>,
        datasets: [{
            label: 'Revenue (₹)',
            data: <?php echo json_encode($rpe_data); ?>,
            backgroundColor: [
                'rgba(201,168,76,0.7)','rgba(91,141,238,0.7)','rgba(76,175,125,0.7)',
                'rgba(224,92,92,0.7)', 'rgba(232,201,122,0.7)','rgba(155,89,182,0.7)'
            ],
            borderRadius: 8,
            borderSkipped: false,
        }]
    },
    options: {
        indexAxis: 'y',
        responsive: true, maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: {
            x: { grid: { color: 'rgba(255,255,255,0.05)' }, beginAtZero: true },
            y: { grid: { color: 'rgba(255,255,255,0.05)' } }
        }
    }
});
<?php } ?>
</script>

</body>
</html>