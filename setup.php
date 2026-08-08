<?php
/**
 * MT5 EA Setup & Installation Guide: setup.php
 * MetaTrader 5 EA License Manager
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MT5 EA Setup & Installation Guide</title>
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
            --text-main: #f0f6fc;
            --text-sub: #8b949e;
            --code-bg: #010409;
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
            max-width: 900px;
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

        .container { width: 100%; max-width: 900px; margin: 0 auto; }

        .header { text-align: center; margin-bottom: 2.5rem; }
        .header h1 { font-size: 2rem; font-weight: 800; margin-bottom: 0.5rem; }
        .header p { color: var(--text-sub); font-size: 1rem; }

        .step-card {
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 16px;
            padding: 2rem;
            margin-bottom: 1.75rem;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
        }

        .step-num {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 36px;
            height: 36px;
            background: rgba(47, 129, 247, 0.15);
            color: var(--accent-hover);
            border: 1px solid rgba(47, 129, 247, 0.3);
            border-radius: 10px;
            font-weight: 800;
            font-size: 1.1rem;
            margin-bottom: 1rem;
        }

        .step-title { font-size: 1.25rem; font-weight: 700; margin-bottom: 0.75rem; }
        .step-desc { color: var(--text-sub); font-size: 0.95rem; line-height: 1.6; margin-bottom: 1.25rem; }

        .url-box {
            background: var(--code-bg);
            border: 1px dashed var(--accent-color);
            border-radius: 8px;
            padding: 1rem;
            font-family: monospace;
            font-size: 1rem;
            color: #56d364;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }

        .btn-copy {
            padding: 0.4rem 0.8rem;
            background: #21262d;
            border: 1px solid var(--border-color);
            color: var(--text-main);
            border-radius: 6px;
            font-size: 0.8rem;
            cursor: pointer;
        }

        .tip-box {
            background: rgba(210, 153, 34, 0.1);
            border: 1px solid var(--warning-color);
            border-radius: 8px;
            padding: 1rem;
            color: #e3b341;
            font-size: 0.9rem;
            line-height: 1.5;
        }

        ol { padding-left: 1.5rem; color: var(--text-main); margin-bottom: 1rem; }
        li { margin-bottom: 0.5rem; font-size: 0.95rem; }
    </style>
</head>
<body>

<div class="navbar">
    <a href="index.php" class="brand">⚡ MT5 EA Manager</a>
    <div class="nav-links">
        <a href="index.php">Generator</a>
        <a href="lookup.php">Check License</a>
        <a href="setup.php" class="active">Setup Guide</a>
        <a href="admin.php">Admin</a>
    </div>
</div>

<div class="container">
    <div class="header">
        <h1>MetaTrader 5 EA Installation & WebRequest Setup Guide</h1>
        <p>Follow these 3 simple steps to connect your Expert Advisor to the licensing server</p>
    </div>

    <!-- Step 1 -->
    <div class="step-card">
        <div class="step-num">1</div>
        <h2 class="step-title">Enable WebRequest in MetaTrader 5 Settings</h2>
        <p class="step-desc">
            To allow the EA to verify its license status dynamically in real-time, you must authorize our verification URL in MetaTrader 5.
        </p>
        <ol>
            <li>Open MetaTrader 5 on your desktop.</li>
            <li>Go to top menu: <strong>Tools</strong> ➔ <strong>Options</strong> (or press <code>Ctrl + O</code>).</li>
            <li>Click on the <strong>Expert Advisors</strong> tab.</li>
            <li>Check the box <strong>"Allow WebRequest for listed URL"</strong>.</li>
            <li>Double-click <strong>"Add new URL..."</strong> and paste the following endpoint URL:</li>
        </ol>

        <div class="url-box">
            <span id="apiUrl">http://localhost:3000/check.php</span>
            <button class="btn-copy" onclick="copyUrl()">Copy URL</button>
        </div>

        <div class="tip-box">
            ⚠️ <strong>Important:</strong> If WebRequest is not enabled, MetaTrader 5 will block network requests and your EA will display a <code>WebRequest Error</code>.
        </div>
    </div>

    <!-- Step 2 -->
    <div class="step-card">
        <div class="step-num">2</div>
        <h2 class="step-title">Install EA File into MQL5 Directory</h2>
        <p class="step-desc">
            Place your downloaded <code>MetaTrader5_EA.ex5</code> file into the MetaTrader 5 Experts folder.
        </p>
        <ol>
            <li>In MT5, click <strong>File</strong> ➔ <strong>Open Data Folder</strong>.</li>
            <li>Navigate to <code>MQL5</code> ➔ <code>Experts</code>.</li>
            <li>Copy and paste your <code>MetaTrader5_EA.ex5</code> file into this folder.</li>
            <li>Return to MT5, right-click <strong>Expert Advisors</strong> in the Navigator panel, and click <strong>Refresh</strong>.</li>
        </ol>
    </div>

    <!-- Step 3 -->
    <div class="step-card">
        <div class="step-num">3</div>
        <h2 class="step-title">Attach EA to Chart & Enter License Key</h2>
        <p class="step-desc">
            Drag the EA onto your desired currency chart and configure your activation inputs.
        </p>
        <ol>
            <li>Drag <code>MetaTrader5_EA</code> from Navigator onto your chart.</li>
            <li>In the <strong>Inputs</strong> tab, locate the parameter <code>InpLicenseKey</code>.</li>
            <li>Paste your generated License Key (e.g. <code>49383307</code>).</li>
            <li>Ensure <strong>"Allow Algo Trading"</strong> is enabled in the Common tab.</li>
            <li>Click <strong>OK</strong>. You should see a green success message in your MT5 Experts log!</li>
        </ol>
    </div>
</div>

<script>
function copyUrl() {
    const urlText = document.getElementById('apiUrl').innerText;
    navigator.clipboard.writeText(urlText).then(() => {
        alert('Server verification URL copied to clipboard!');
    });
}
</script>
</body>
</html>
