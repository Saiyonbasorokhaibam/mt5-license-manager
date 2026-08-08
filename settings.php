<?php
/**
 * Admin Settings & API Configuration: settings.php
 * MetaTrader 5 EA License Manager
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin System Settings - MT5 License Manager</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-color: #0d1117;
            --card-bg: #161b22;
            --border-color: #30363d;
            --accent-color: #2f81f7;
            --accent-hover: #58a6ff;
            --success-color: #238636;
            --text-main: #f0f6fc;
            --text-sub: #8b949e;
            --input-bg: #0d1117;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Plus Jakarta Sans', sans-serif; }

        body {
            background-color: var(--bg-color);
            color: var(--text-main);
            min-height: 100vh;
            padding: 2rem 1.5rem;
        }

        .navbar {
            max-width: 800px;
            margin: 0 auto 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .brand { font-size: 1.25rem; font-weight: 800; display: flex; align-items: center; gap: 0.6rem; color: var(--text-main); text-decoration: none; }
        .nav-links { display: flex; gap: 1rem; align-items: center; }
        .nav-links a { color: var(--text-sub); text-decoration: none; font-size: 0.9rem; font-weight: 600; transition: color 0.2s; }
        .nav-links a:hover, .nav-links a.active { color: var(--accent-hover); }

        .container { max-width: 800px; margin: 0 auto; }

        .card {
            background: var(--card-bg); border: 1px solid var(--border-color); border-radius: 14px; padding: 2rem; margin-bottom: 2rem;
        }

        .card-title { font-size: 1.2rem; font-weight: 700; margin-bottom: 1.25rem; border-bottom: 1px solid var(--border-color); padding-bottom: 0.75rem; }

        .form-group { margin-bottom: 1.5rem; }
        label { display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 0.4rem; }
        .help-text { font-size: 0.8rem; color: var(--text-sub); margin-top: 0.25rem; }

        input[type="text"], input[type="password"], input[type="number"] {
            width: 100%; padding: 0.75rem 1rem; background: var(--input-bg); border: 1px solid var(--border-color); border-radius: 8px; color: var(--text-main); font-size: 0.9rem;
        }

        .btn-save {
            padding: 0.75rem 1.5rem; background: var(--accent-color); color: #fff; border: none; border-radius: 8px; font-weight: 700; cursor: pointer; transition: background 0.2s;
        }
        .btn-save:hover { background: var(--accent-hover); }
    </style>
</head>
<body>

<div class="navbar">
    <a href="admin.php" class="brand">⚡ MT5 License Admin</a>
    <div class="nav-links">
        <a href="admin.php">Dashboard</a>
        <a href="analytics.php">Analytics</a>
        <a href="settings.php" class="active">Settings</a>
        <a href="index.php">Public Site</a>
    </div>
</div>

<div class="container">
    <div class="card">
        <h2 class="card-title">🔐 Administrator Credentials</h2>
        <form method="POST" action="settings.php">
            <input type="hidden" name="action" value="update_auth">
            <div class="form-group">
                <label for="admin_password">Admin Dashboard Password</label>
                <input type="password" id="admin_password" name="admin_password" value="admin123" required>
                <div class="help-text">Password used to authenticate into the admin panel.</div>
            </div>
            <button type="submit" class="btn-save">Update Admin Password</button>
        </form>
    </div>

    <div class="card">
        <h2 class="card-title">🧮 License Key Generation Algorithm</h2>
        <form method="POST" action="settings.php">
            <input type="hidden" name="action" value="update_formula">
            <div class="form-group">
                <label for="key_multiplier">Account Multiplier Factor</label>
                <input type="number" id="key_multiplier" name="key_multiplier" value="4" required>
                <div class="help-text">Formula: <code>(Account Number * Multiplier) + Offset + Expiry Days</code></div>
            </div>
            <div class="form-group">
                <label for="key_offset">Fixed Offset</label>
                <input type="number" id="key_offset" name="key_offset" value="550" required>
            </div>
            <button type="submit" class="btn-save">Save Algorithm Settings</button>
        </form>
    </div>
</div>

</body>
</html>
