<?php
/**
 * Customer License Lookup Page: lookup.php
 * MetaTrader 5 EA License Manager
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Check EA License Status</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
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
            --input-bg: #0d1117;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Plus Jakarta Sans', sans-serif; }

        body {
            background-color: var(--bg-color);
            color: var(--text-main);
            min-height: 100vh;
            padding: 2rem 1rem;
            background-image: 
                radial-gradient(at 20% 20%, rgba(47, 129, 247, 0.15) 0px, transparent 50%),
                radial-gradient(at 80% 80%, rgba(35, 134, 54, 0.1) 0px, transparent 50%);
        }

        .navbar {
            max-width: 650px;
            margin: 0 auto 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            padding: 1rem 1.5rem;
            border-radius: 12px;
        }

        .brand { font-size: 1.1rem; font-weight: 800; display: flex; align-items: center; gap: 0.5rem; color: var(--text-main); text-decoration: none; }
        .nav-links { display: flex; gap: 1rem; align-items: center; }
        .nav-links a { color: var(--text-sub); text-decoration: none; font-size: 0.9rem; font-weight: 600; transition: color 0.2s; }
        .nav-links a:hover, .nav-links a.active { color: var(--accent-hover); }

        .container { width: 100%; max-width: 650px; margin: 0 auto; }

        .card {
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 16px;
            padding: 2.5rem;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.4);
        }

        .header { text-align: center; margin-bottom: 2rem; }
        .header h1 { font-size: 1.75rem; font-weight: 800; margin-bottom: 0.5rem; }
        .header p { color: var(--text-sub); font-size: 0.95rem; }

        .form-group { margin-bottom: 1.5rem; }
        label { display: block; font-size: 0.875rem; font-weight: 600; margin-bottom: 0.5rem; }

        input[type="text"] {
            width: 100%;
            padding: 0.85rem 1rem;
            background: var(--input-bg);
            border: 1px solid var(--border-color);
            border-radius: 10px;
            color: var(--text-main);
            font-size: 0.95rem;
        }

        input:focus { outline: none; border-color: var(--accent-color); }

        .btn-submit {
            width: 100%; padding: 1rem; background: var(--accent-color); color: #ffffff;
            border: none; border-radius: 10px; font-size: 1rem; font-weight: 700;
            cursor: pointer; transition: all 0.2s ease;
        }
        .btn-submit:hover { background: var(--accent-hover); }

        .result-box {
            background: #0d1117;
            border: 1px solid var(--border-color);
            border-radius: 14px;
            padding: 1.5rem;
            margin-top: 2rem;
        }

        .status-badge {
            display: inline-block; padding: 0.3rem 0.8rem; border-radius: 20px; font-size: 0.8rem; font-weight: 700; text-transform: uppercase; margin-bottom: 1rem;
        }
        .status-active { background: rgba(46, 160, 67, 0.15); color: #56d364; border: 1px solid rgba(46, 160, 67, 0.4); }
        .status-expired { background: rgba(210, 153, 34, 0.15); color: #e3b341; border: 1px solid rgba(210, 153, 34, 0.4); }
        .status-revoked { background: rgba(218, 54, 51, 0.15); color: #ff7b72; border: 1px solid rgba(218, 54, 51, 0.4); }

        .info-row { display: flex; justify-content: space-between; padding: 0.6rem 0; border-bottom: 1px solid var(--border-color); font-size: 0.9rem; }
        .info-row:last-child { border-bottom: none; }
        .info-label { color: var(--text-sub); }
        .info-value { font-weight: 700; color: var(--text-main); }
    </style>
</head>
<body>

<div class="navbar">
    <a href="index.php" class="brand">⚡ MT5 EA Manager</a>
    <div class="nav-links">
        <a href="index.php">Generator</a>
        <a href="lookup.php" class="active">Check License</a>
        <a href="setup.php">Setup Guide</a>
        <a href="admin.php">Admin</a>
    </div>
</div>

<div class="container">
    <div class="card">
        <div class="header">
            <h1>License Status Lookup</h1>
            <p>Enter your MetaTrader 5 Account Number or License Key to check status</p>
        </div>

        <form method="GET" action="lookup.php">
            <div class="form-group">
                <label for="query">MT5 Account Number or License Key</label>
                <input type="text" id="query" name="query" placeholder="e.g. 12345678 or 49383307" required>
            </div>
            <button type="submit" class="btn-submit">Search License</button>
        </form>

        <div id="results"></div>
    </div>
</div>

</body>
</html>
