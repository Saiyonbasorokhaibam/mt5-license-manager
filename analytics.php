<?php
/**
 * Admin Sales & Revenue Analytics: analytics.php
 * MetaTrader 5 EA License Manager
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sales & Revenue Analytics - MT5 EA License Manager</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-color: #0d1117;
            --card-bg: #161b22;
            --border-color: #30363d;
            --accent-color: #2f81f7;
            --accent-hover: #58a6ff;
            --success-color: #238636;
            --warning-color: #d29922;
            --danger-color: #da3633;
            --text-main: #f0f6fc;
            --text-sub: #8b949e;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Plus Jakarta Sans', sans-serif; }

        body {
            background-color: var(--bg-color);
            color: var(--text-main);
            min-height: 100vh;
            padding: 2rem 1.5rem;
        }

        .navbar {
            max-width: 1100px;
            margin: 0 auto 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .brand { font-size: 1.25rem; font-weight: 800; display: flex; align-items: center; gap: 0.6rem; color: var(--text-main); text-decoration: none; }
        .nav-links { display: flex; gap: 1rem; align-items: center; }
        .nav-links a { color: var(--text-sub); text-decoration: none; font-size: 0.9rem; font-weight: 600; transition: color 0.2s; }
        .nav-links a:hover, .nav-links a.active { color: var(--accent-hover); }

        .container { max-width: 1100px; margin: 0 auto; }

        .header { margin-bottom: 2rem; }
        .header h1 { font-size: 1.75rem; font-weight: 800; }
        .header p { color: var(--text-sub); font-size: 0.95rem; }

        .grid-4 { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1.25rem; margin-bottom: 2rem; }

        .card {
            background: var(--card-bg); border: 1px solid var(--border-color); border-radius: 14px; padding: 1.5rem;
        }

        .stat-label { font-size: 0.85rem; color: var(--text-sub); font-weight: 600; }
        .stat-val { font-size: 2rem; font-weight: 800; margin-top: 0.5rem; color: var(--accent-hover); }

        .chart-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 2rem; }
        @media(max-width: 768px) { .chart-grid { grid-template-columns: 1fr; } }

        .progress-bar-bg { background: #0d1117; border-radius: 10px; height: 12px; overflow: hidden; margin-top: 0.5rem; }
        .progress-fill { height: 100%; background: var(--accent-color); border-radius: 10px; }

        .plan-row { margin-bottom: 1.25rem; }
        .plan-info { display: flex; justify-content: space-between; font-size: 0.9rem; font-weight: 600; }
    </style>
</head>
<body>

<div class="navbar">
    <a href="admin.php" class="brand">⚡ MT5 License Admin</a>
    <div class="nav-links">
        <a href="admin.php">Dashboard</a>
        <a href="analytics.php" class="active">Analytics</a>
        <a href="settings.php">Settings</a>
        <a href="index.php">Public Site</a>
    </div>
</div>

<div class="container">
    <div class="header">
        <h1>Sales & Revenue Analytics</h1>
        <p>Real-time financial performance and subscription breakdown metrics</p>
    </div>

    <div class="grid-4" id="statsGrid">
        <!-- Rendered by JS or PHP -->
    </div>

    <div class="chart-grid">
        <div class="card">
            <h3 style="margin-bottom: 1.5rem; font-size: 1.1rem;">Subscription Plan Revenue Share</h3>
            <div id="planBreakdown"></div>
        </div>

        <div class="card">
            <h3 style="margin-bottom: 1.5rem; font-size: 1.1rem;">License Lifecycle Health</h3>
            <div id="healthBreakdown"></div>
        </div>
    </div>
</div>

</body>
</html>
