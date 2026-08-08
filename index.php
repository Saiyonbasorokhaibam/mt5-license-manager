<?php
/**
 * Customer License Generation Page: index.php
 * MetaTrader 5 EA License Manager
 */

require_once __DIR__ . '/config.php';

$errors = [];
$generated_key = null;
$license_details = null;

// Form submission processing
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name           = isset($_POST['customer_name']) ? trim($_POST['customer_name']) : '';
    $email          = isset($_POST['customer_email']) ? trim($_POST['customer_email']) : '';
    $account_number = isset($_POST['account_number']) ? trim($_POST['account_number']) : '';
    $plan           = isset($_POST['plan']) ? trim($_POST['plan']) : '';

    // Field Validation
    if (empty($name)) {
        $errors[] = "Customer Name is required.";
    }

    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "A valid Customer Email address is required.";
    }

    if (empty($account_number) || !is_numeric($account_number) || (int)$account_number <= 0) {
        $errors[] = "MetaTrader Account Number must be a positive integer.";
    } else {
        $account_number = (int)$account_number;
    }

    // Determine plan days
    $plan_days = [
        '1month'   => 30,
        '3month'   => 90,
        '1year'    => 365,
        'lifetime' => 9999
    ];

    if (!array_key_exists($plan, $plan_days)) {
        $errors[] = "Please select a valid subscription plan.";
    }

    // If validation passes, calculate license key and save to database
    if (empty($errors)) {
        $expiry_days = $plan_days[$plan];
        
        // License Key Formula: (Account Number * 4) + 550 + Expiry Days
        $calculated_key = ($account_number * 4) + 550 + $expiry_days;
        $license_key = (string)$calculated_key;

        // Calculate Expiry Date
        $expiry_date = date('Y-m-d', strtotime("+$expiry_days days"));

        $conn = getDBConnection();
        if (!$conn) {
            $errors[] = "Database connection failed. Please try again later.";
        } else {
            // Prepared statement to insert record into MySQL
            $stmt = $conn->prepare("INSERT INTO licenses (customer_name, customer_email, account_number, plan, license_key, expiry_days, expiry_date, status) VALUES (?, ?, ?, ?, ?, ?, ?, 'active') ON DUPLICATE KEY UPDATE status='active', expiry_days=?, expiry_date=?");
            
            if ($stmt) {
                $stmt->bind_param("ssissiiss", $name, $email, $account_number, $plan, $license_key, $expiry_days, $expiry_date, $expiry_days, $expiry_date);
                if ($stmt->execute()) {
                    $generated_key = $license_key;
                    $license_details = [
                        'name'        => $name,
                        'email'       => $email,
                        'account'     => $account_number,
                        'plan'        => $plan,
                        'key'         => $license_key,
                        'expiry_date' => $expiry_date
                    ];
                } else {
                    $errors[] = "Failed to save license record: " . $stmt->error;
                }
                $stmt->close();
            } else {
                $errors[] = "Database query preparation error.";
            }
            $conn->close();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MetaTrader 5 EA License Generator</title>
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
            --success-glow: rgba(46, 160, 67, 0.4);
            --text-main: #f0f6fc;
            --text-sub: #8b949e;
            --input-bg: #0d1117;
            --danger-color: #da3633;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
        }

        body {
            background-color: var(--bg-color);
            color: var(--text-main);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem 1rem;
            background-image: 
                radial-gradient(at 20% 20%, rgba(47, 129, 247, 0.15) 0px, transparent 50%),
                radial-gradient(at 80% 80%, rgba(35, 134, 54, 0.1) 0px, transparent 50%);
        }

        .container {
            width: 100%;
            max-width: 650px;
        }

        .card {
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 16px;
            padding: 2.5rem;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.4);
            backdrop-filter: blur(10px);
        }

        .header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .header-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 56px;
            height: 56px;
            background: rgba(47, 129, 247, 0.1);
            border-radius: 14px;
            color: var(--accent-hover);
            margin-bottom: 1rem;
            border: 1px solid rgba(47, 129, 247, 0.2);
        }

        .header h1 {
            font-size: 1.75rem;
            font-weight: 800;
            letter-spacing: -0.02em;
            margin-bottom: 0.5rem;
        }

        .header p {
            color: var(--text-sub);
            font-size: 0.95rem;
        }

        .alert {
            background: rgba(218, 54, 51, 0.1);
            border: 1px solid var(--danger-color);
            color: #ff7b72;
            padding: 1rem;
            border-radius: 10px;
            margin-bottom: 1.5rem;
            font-size: 0.9rem;
        }

        .alert ul {
            padding-left: 1.25rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        label {
            display: block;
            font-size: 0.875rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
            color: var(--text-main);
        }

        input[type="text"],
        input[type="email"],
        input[type="number"] {
            width: 100%;
            padding: 0.85rem 1rem;
            background: var(--input-bg);
            border: 1px solid var(--border-color);
            border-radius: 10px;
            color: var(--text-main);
            font-size: 0.95rem;
            transition: all 0.2s ease;
        }

        input:focus {
            outline: none;
            border-color: var(--accent-color);
            box-shadow: 0 0 0 3px rgba(47, 129, 247, 0.25);
        }

        .plans-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1rem;
            margin-top: 0.5rem;
        }

        @media (max-width: 500px) {
            .plans-grid {
                grid-template-columns: 1fr;
            }
        }

        .plan-option {
            position: relative;
        }

        .plan-option input[type="radio"] {
            position: absolute;
            opacity: 0;
            width: 0;
            height: 0;
        }

        .plan-box {
            display: flex;
            flex-direction: column;
            padding: 1rem;
            background: var(--input-bg);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .plan-option input[type="radio"]:checked + .plan-box {
            border-color: var(--accent-color);
            background: rgba(47, 129, 247, 0.08);
            box-shadow: 0 0 0 2px var(--accent-color);
        }

        .plan-name {
            font-weight: 700;
            font-size: 0.95rem;
            color: var(--text-main);
        }

        .plan-price {
            font-size: 1.25rem;
            font-weight: 800;
            color: var(--accent-hover);
            margin-top: 0.25rem;
        }

        .plan-duration {
            font-size: 0.8rem;
            color: var(--text-sub);
            margin-top: 0.15rem;
        }

        .btn-submit {
            width: 100%;
            padding: 1rem;
            background: var(--accent-color);
            color: #ffffff;
            border: none;
            border-radius: 10px;
            font-size: 1rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.2s ease;
            margin-top: 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }

        .btn-submit:hover {
            background: var(--accent-hover);
            transform: translateY(-1px);
        }

        /* Generated Key Modal / Display Section */
        .result-box {
            background: rgba(35, 134, 54, 0.1);
            border: 1px solid var(--success-color);
            border-radius: 14px;
            padding: 1.75rem;
            margin-top: 2rem;
            text-align: center;
            animation: fadeIn 0.4s ease;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .result-title {
            color: #56d364;
            font-size: 1.1rem;
            font-weight: 700;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }

        .key-display {
            background: #0d1117;
            border: 1px dashed var(--success-color);
            padding: 1rem;
            border-radius: 10px;
            font-family: monospace;
            font-size: 1.5rem;
            font-weight: 700;
            color: #56d364;
            letter-spacing: 2px;
            margin-bottom: 1rem;
            word-break: break-all;
        }

        .actions-group {
            display: flex;
            gap: 0.75rem;
            justify-content: center;
            flex-wrap: wrap;
        }

        .btn-action {
            padding: 0.75rem 1.25rem;
            border-radius: 8px;
            font-weight: 600;
            font-size: 0.9rem;
            text-decoration: none;
            cursor: pointer;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn-copy {
            background: #21262d;
            color: var(--text-main);
            border: 1px solid var(--border-color);
        }

        .btn-copy:hover {
            background: #30363d;
        }

        .btn-download {
            background: var(--success-color);
            color: #ffffff;
            border: none;
        }

        .btn-download:hover {
            background: #2ea043;
            box-shadow: 0 0 15px var(--success-glow);
        }

        .footer-link {
            text-align: center;
            margin-top: 1.5rem;
        }

        .footer-link a {
            color: var(--text-sub);
            text-decoration: none;
            font-size: 0.85rem;
            transition: color 0.2s ease;
        }

        .footer-link a:hover {
            color: var(--accent-hover);
        }
    </style>
</head>
<body>

<div class="container">
    <div class="card">
        <div class="header">
            <div class="header-icon">
                <svg width="28" height="28" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path>
                </svg>
            </div>
            <h1>MetaTrader 5 EA License Generator</h1>
            <p>Generate your instant license key for trading automation</p>
        </div>

        <?php if (!empty($errors)): ?>
            <div class="alert">
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?= htmlspecialchars($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="form-group">
                <label for="customer_name">Customer Full Name</label>
                <input type="text" id="customer_name" name="customer_name" placeholder="John Doe" value="<?= isset($_POST['customer_name']) ? htmlspecialchars($_POST['customer_name']) : '' ?>" required>
            </div>

            <div class="form-group">
                <label for="customer_email">Email Address</label>
                <input type="email" id="customer_email" name="customer_email" placeholder="john@example.com" value="<?= isset($_POST['customer_email']) ? htmlspecialchars($_POST['customer_email']) : '' ?>" required>
            </div>

            <div class="form-group">
                <label for="account_number">MetaTrader 5 Account Number</label>
                <input type="number" id="account_number" name="account_number" placeholder="e.g. 12345678" value="<?= isset($_POST['account_number']) ? htmlspecialchars($_POST['account_number']) : '' ?>" required>
            </div>

            <div class="form-group">
                <label>Select Plan</label>
                <div class="plans-grid">
                    <label class="plan-option">
                        <input type="radio" name="plan" value="1month" <?= (!isset($_POST['plan']) || $_POST['plan'] === '1month') ? 'checked' : '' ?>>
                        <div class="plan-box">
                            <span class="plan-name">1 Month</span>
                            <span class="plan-price">$29</span>
                            <span class="plan-duration">30 Days Access</span>
                        </div>
                    </label>

                    <label class="plan-option">
                        <input type="radio" name="plan" value="3month" <?= (isset($_POST['plan']) && $_POST['plan'] === '3month') ? 'checked' : '' ?>>
                        <div class="plan-box">
                            <span class="plan-name">3 Months</span>
                            <span class="plan-price">$79</span>
                            <span class="plan-duration">90 Days Access</span>
                        </div>
                    </label>

                    <label class="plan-option">
                        <input type="radio" name="plan" value="1year" <?= (isset($_POST['plan']) && $_POST['plan'] === '1year') ? 'checked' : '' ?>>
                        <div class="plan-box">
                            <span class="plan-name">1 Year</span>
                            <span class="plan-price">$199</span>
                            <span class="plan-duration">365 Days Access</span>
                        </div>
                    </label>

                    <label class="plan-option">
                        <input type="radio" name="plan" value="lifetime" <?= (isset($_POST['plan']) && $_POST['plan'] === 'lifetime') ? 'checked' : '' ?>>
                        <div class="plan-box">
                            <span class="plan-name">Lifetime</span>
                            <span class="plan-price">$499</span>
                            <span class="plan-duration">9999 Days Access</span>
                        </div>
                    </label>
                </div>
            </div>

            <button type="submit" class="btn-submit">
                <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                </svg>
                Generate License Key
            </button>
        </form>

        <?php if ($generated_key && $license_details): ?>
            <div class="result-box">
                <div class="result-title">
                    <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    License Key Generated Successfully!
                </div>
                <div class="key-display" id="keyText"><?= htmlspecialchars($generated_key) ?></div>
                <p style="color: var(--text-sub); font-size: 0.85rem; margin-bottom: 1.25rem;">
                    Valid until: <strong><?= htmlspecialchars($license_details['expiry_date']) ?></strong> for MT5 Account #<strong><?= htmlspecialchars($license_details['account']) ?></strong>
                </p>

                <div class="actions-group">
                    <button type="button" class="btn-action btn-copy" onclick="copyKey()">
                        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                        </svg>
                        <span id="copyBtnText">Copy Key</span>
                    </button>
                    <a href="downloads/MetaTrader5_EA.ex5" class="btn-action btn-download" download>
                        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                        </svg>
                        Download EA (.ex5)
                    </a>
                </div>
            </div>
        <?php endif; ?>

        <div class="footer-link">
            <a href="admin.php">🔑 Admin Dashboard Access</a>
        </div>
    </div>
</div>

<script>
function copyKey() {
    const keyText = document.getElementById('keyText').innerText;
    navigator.clipboard.writeText(keyText).then(() => {
        const btnText = document.getElementById('copyBtnText');
        btnText.innerText = 'Copied!';
        setTimeout(() => {
            btnText.innerText = 'Copy Key';
        }, 2000);
    });
}
</script>

</body>
</html>
