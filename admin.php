<?php
/**
 * Admin Dashboard: admin.php
 * MetaTrader 5 EA License Manager
 */

session_start();
require_once __DIR__ . '/config.php';

// Hardcoded Password
define('ADMIN_PASSWORD', 'admin123');

$login_error = '';

// Handle Logout
if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    unset($_SESSION['admin_logged_in']);
    session_destroy();
    header("Location: admin.php");
    exit;
}

// Handle Login Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login_password'])) {
    if ($_POST['login_password'] === ADMIN_PASSWORD) {
        $_SESSION['admin_logged_in'] = true;
        header("Location: admin.php");
        exit;
    } else {
        $login_error = "Invalid administrator password.";
    }
}

// If not logged in, display modern Login Form
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true):
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - EA License Manager</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-color: #0d1117;
            --card-bg: #161b22;
            --border-color: #30363d;
            --accent-color: #2f81f7;
            --text-main: #f0f6fc;
            --text-sub: #8b949e;
            --danger-color: #da3633;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Plus Jakarta Sans', sans-serif; }

        body {
            background-color: var(--bg-color);
            color: var(--text-main);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem;
        }

        .login-card {
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 16px;
            padding: 2.5rem;
            width: 100%;
            max-width: 400px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.5);
            text-align: center;
        }

        .login-icon {
            width: 50px;
            height: 50px;
            background: rgba(47, 129, 247, 0.1);
            border: 1px solid rgba(47, 129, 247, 0.2);
            border-radius: 12px;
            color: var(--accent-color);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1.25rem;
        }

        .login-card h2 { font-size: 1.5rem; font-weight: 700; margin-bottom: 0.5rem; }
        .login-card p { color: var(--text-sub); font-size: 0.9rem; margin-bottom: 1.5rem; }

        .alert-error {
            background: rgba(218, 54, 51, 0.1);
            border: 1px solid var(--danger-color);
            color: #ff7b72;
            padding: 0.75rem;
            border-radius: 8px;
            font-size: 0.85rem;
            margin-bottom: 1.25rem;
        }

        .form-group { text-align: left; margin-bottom: 1.25rem; }
        label { display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 0.4rem; }

        input[type="password"] {
            width: 100%;
            padding: 0.8rem 1rem;
            background: #0d1117;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            color: var(--text-main);
            font-size: 0.95rem;
        }

        input:focus { outline: none; border-color: var(--accent-color); }

        .btn-login {
            width: 100%;
            padding: 0.85rem;
            background: var(--accent-color);
            color: #fff;
            border: none;
            border-radius: 8px;
            font-weight: 700;
            font-size: 0.95rem;
            cursor: pointer;
            transition: background 0.2s ease;
        }

        .btn-login:hover { background: #58a6ff; }
        .back-link { display: block; margin-top: 1.25rem; color: var(--text-sub); font-size: 0.85rem; text-decoration: none; }
        .back-link:hover { color: var(--accent-color); }
    </style>
</head>
<body>

<div class="login-card">
    <div class="login-icon">
        <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
        </svg>
    </div>
    <h2>Admin Dashboard</h2>
    <p>Please authenticate to access controls</p>

    <?php if ($login_error): ?>
        <div class="alert-error"><?= htmlspecialchars($login_error) ?></div>
    <?php endif; ?>

    <form method="POST" action="">
        <div class="form-group">
            <label for="login_password">Password</label>
            <input type="password" id="login_password" name="login_password" placeholder="Enter admin password" required>
        </div>
        <button type="submit" class="btn-login">Login to Dashboard</button>
    </form>
    <a href="index.php" class="back-link">← Return to Public Generator</a>
</div>

</body>
</html>
<?php
exit;
endif;

// --- LOGGED IN ADMIN ACTIONS & DATA RETRIEVAL ---
$conn = getDBConnection();
$message = '';
$message_type = 'success';

// Handle Revoke Action
if (isset($_GET['action']) && $_GET['action'] === 'revoke' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    if ($conn) {
        $stmt = $conn->prepare("UPDATE licenses SET status = 'revoked' WHERE id = ?");
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            $message = "License #$id successfully revoked.";
        } else {
            $message = "Error revoking license.";
            $message_type = 'danger';
        }
        $stmt->close();
    }
}

// Handle Delete Action
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    if ($conn) {
        $stmt = $conn->prepare("DELETE FROM licenses WHERE id = ?");
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            $message = "License #$id permanently deleted.";
        } else {
            $message = "Error deleting license.";
            $message_type = 'danger';
        }
        $stmt->close();
    }
}

// Search and Filter parameters
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$filter_status = isset($_GET['status']) ? trim($_GET['status']) : 'all';

// Retrieve Stats
$total_count = 0;
$active_count = 0;
$expired_count = 0;
$revoked_count = 0;

$licenses = [];

if ($conn) {
    // 1. Calculate Stats
    $today = date('Y-m-d');
    
    $res = $conn->query("SELECT COUNT(*) AS total FROM licenses");
    if ($res) { $total_count = $res->fetch_assoc()['total']; }

    $res = $conn->query("SELECT COUNT(*) AS active FROM licenses WHERE status = 'active' AND expiry_date >= '$today'");
    if ($res) { $active_count = $res->fetch_assoc()['active']; }

    $res = $conn->query("SELECT COUNT(*) AS expired FROM licenses WHERE status != 'revoked' AND expiry_date < '$today'");
    if ($res) { $expired_count = $res->fetch_assoc()['expired']; }

    $res = $conn->query("SELECT COUNT(*) AS revoked FROM licenses WHERE status = 'revoked'");
    if ($res) { $revoked_count = $res->fetch_assoc()['revoked']; }

    // 2. Fetch Licenses with Search & Filter
    $sql = "SELECT * FROM licenses WHERE 1=1";
    $params = [];
    $types = "";

    if (!empty($search)) {
        $sql .= " AND (customer_name LIKE ? OR customer_email LIKE ? OR account_number LIKE ? OR license_key LIKE ?)";
        $searchTerm = "%" . $search . "%";
        $params[] = &$searchTerm;
        $params[] = &$searchTerm;
        $params[] = &$searchTerm;
        $params[] = &$searchTerm;
        $types .= "ssss";
    }

    if ($filter_status === 'active') {
        $sql .= " AND status = 'active' AND expiry_date >= '$today'";
    } elseif ($filter_status === 'expired') {
        $sql .= " AND status != 'revoked' AND expiry_date < '$today'";
    } elseif ($filter_status === 'revoked') {
        $sql .= " AND status = 'revoked'";
    }

    $sql .= " ORDER BY id DESC";

    $stmt = $conn->prepare($sql);
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $licenses[] = $row;
    }
    $stmt->close();
    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - EA License Management</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-color: #0d1117;
            --card-bg: #161b22;
            --border-color: #30363d;
            --accent-color: #2f81f7;
            --accent-hover: #58a6ff;
            --text-main: #f0f6fc;
            --text-sub: #8b949e;
            --active-green: #238636;
            --expired-orange: #d97706;
            --revoked-red: #da3633;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Plus Jakarta Sans', sans-serif; }

        body {
            background-color: var(--bg-color);
            color: var(--text-main);
            min-height: 100vh;
            padding: 2rem 1.5rem;
        }

        .dashboard-container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .nav-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid var(--border-color);
        }

        .nav-header h1 {
            font-size: 1.75rem;
            font-weight: 800;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .header-actions {
            display: flex;
            gap: 1rem;
        }

        .btn {
            padding: 0.6rem 1.2rem;
            border-radius: 8px;
            font-size: 0.875rem;
            font-weight: 600;
            text-decoration: none;
            cursor: pointer;
            border: none;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
        }

        .btn-outline {
            background: #21262d;
            color: var(--text-main);
            border: 1px solid var(--border-color);
        }

        .btn-outline:hover { background: #30363d; }

        .btn-logout {
            background: rgba(218, 54, 51, 0.15);
            color: #ff7b72;
            border: 1px solid var(--revoked-red);
        }

        .btn-logout:hover { background: var(--revoked-red); color: #fff; }

        .alert-msg {
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
            font-size: 0.9rem;
            font-weight: 500;
        }

        .alert-success { background: rgba(35, 134, 54, 0.15); border: 1px solid var(--active-green); color: #56d364; }
        .alert-danger { background: rgba(218, 54, 51, 0.15); border: 1px solid var(--revoked-red); color: #ff7b72; }

        /* Stats Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 1.25rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            padding: 1.5rem;
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .stat-title {
            color: var(--text-sub);
            font-size: 0.85rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .stat-value {
            font-size: 2.25rem;
            font-weight: 800;
            color: var(--text-main);
        }

        .stat-card.total { border-left: 4px solid var(--accent-color); }
        .stat-card.active { border-left: 4px solid var(--active-green); }
        .stat-card.expired { border-left: 4px solid var(--expired-orange); }
        .stat-card.revoked { border-left: 4px solid var(--revoked-red); }

        /* Controls / Search Bar */
        .controls-card {
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            padding: 1.25rem;
            margin-bottom: 1.5rem;
        }

        .filter-form {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
        }

        .search-input {
            flex: 1;
            min-width: 250px;
            padding: 0.7rem 1rem;
            background: #0d1117;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            color: var(--text-main);
            font-size: 0.9rem;
        }

        .status-select {
            padding: 0.7rem 1rem;
            background: #0d1117;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            color: var(--text-main);
            font-size: 0.9rem;
        }

        /* Table Styling */
        .table-card {
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
            font-size: 0.9rem;
        }

        th {
            background: #1c2129;
            color: var(--text-sub);
            font-weight: 700;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.05em;
            padding: 1rem 1.25rem;
            border-bottom: 1px solid var(--border-color);
        }

        td {
            padding: 1rem 1.25rem;
            border-bottom: 1px solid var(--border-color);
            color: var(--text-main);
        }

        tr:last-child td { border-bottom: none; }
        tr:hover td { background: rgba(255, 255, 255, 0.02); }

        .badge {
            display: inline-block;
            padding: 0.25rem 0.65rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
        }

        .badge-active { background: rgba(35, 134, 54, 0.2); color: #56d364; border: 1px solid var(--active-green); }
        .badge-expired { background: rgba(217, 119, 6, 0.2); color: #fbbf24; border: 1px solid var(--expired-orange); }
        .badge-revoked { background: rgba(218, 54, 51, 0.2); color: #ff7b72; border: 1px solid var(--revoked-red); }

        .btn-action-sm {
            padding: 0.35rem 0.7rem;
            border-radius: 6px;
            font-size: 0.8rem;
            font-weight: 600;
            text-decoration: none;
            margin-right: 0.3rem;
            display: inline-block;
        }

        .btn-revoke { background: rgba(218, 54, 51, 0.2); color: #ff7b72; border: 1px solid var(--revoked-red); }
        .btn-revoke:hover { background: var(--revoked-red); color: #fff; }

        .btn-delete { background: #21262d; color: var(--text-sub); border: 1px solid var(--border-color); }
        .btn-delete:hover { background: #30363d; color: #ff7b72; }

        .empty-state {
            padding: 3rem;
            text-align: center;
            color: var(--text-sub);
        }
    </style>
</head>
<body>

<div class="dashboard-container">
    <div class="nav-header">
        <h1>
            <svg width="28" height="28" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            EA License Manager Admin
        </h1>
        <div class="header-actions">
            <a href="index.php" class="btn btn-outline" target="_blank">🌐 Public Site</a>
            <a href="admin.php?action=logout" class="btn btn-logout">Logout</a>
        </div>
    </div>

    <?php if ($message): ?>
        <div class="alert-msg alert-<?= $message_type ?>"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <!-- Statistics Cards -->
    <div class="stats-grid">
        <div class="stat-card total">
            <span class="stat-title">Total Licenses</span>
            <span class="stat-value"><?= $total_count ?></span>
        </div>
        <div class="stat-card active">
            <span class="stat-title">Active Licenses</span>
            <span class="stat-value"><?= $active_count ?></span>
        </div>
        <div class="stat-card expired">
            <span class="stat-title">Expired Licenses</span>
            <span class="stat-value"><?= $expired_count ?></span>
        </div>
        <div class="stat-card revoked">
            <span class="stat-title">Revoked Licenses</span>
            <span class="stat-value"><?= $revoked_count ?></span>
        </div>
    </div>

    <!-- Search & Filter Bar -->
    <div class="controls-card">
        <form method="GET" action="admin.php" class="filter-form">
            <input type="text" name="search" class="search-input" placeholder="Search customer, email, account # or key..." value="<?= htmlspecialchars($search) ?>">
            
            <select name="status" class="status-select" onchange="this.form.submit()">
                <option value="all" <?= $filter_status === 'all' ? 'selected' : '' ?>>All Statuses</option>
                <option value="active" <?= $filter_status === 'active' ? 'selected' : '' ?>>Active Only</option>
                <option value="expired" <?= $filter_status === 'expired' ? 'selected' : '' ?>>Expired Only</option>
                <option value="revoked" <?= $filter_status === 'revoked' ? 'selected' : '' ?>>Revoked Only</option>
            </select>

            <button type="submit" class="btn btn-outline">Filter</button>
            <?php if (!empty($search) || $filter_status !== 'all'): ?>
                <a href="admin.php" class="btn btn-outline">Reset</a>
            <?php endif; ?>
        </form>
    </div>

    <!-- Licenses Table -->
    <div class="table-card">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Customer Name</th>
                    <th>Email</th>
                    <th>Account #</th>
                    <th>Plan</th>
                    <th>License Key</th>
                    <th>Expiry Date</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($licenses)): ?>
                    <tr>
                        <td colspan="9" class="empty-state">No licenses found matching criteria.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($licenses as $row): 
                        $today = date('Y-m-d');
                        $effective_status = $row['status'];
                        
                        if ($effective_status !== 'revoked' && $today > $row['expiry_date']) {
                            $effective_status = 'expired';
                        }
                    ?>
                        <tr>
                            <td>#<?= htmlspecialchars($row['id']) ?></td>
                            <td><strong><?= htmlspecialchars($row['customer_name']) ?></strong></td>
                            <td><?= htmlspecialchars($row['customer_email']) ?></td>
                            <td><code><?= htmlspecialchars($row['account_number']) ?></code></td>
                            <td><span style="text-transform: capitalize;"><?= htmlspecialchars($row['plan']) ?></span></td>
                            <td><code style="color: var(--accent-hover); font-weight:700;"><?= htmlspecialchars($row['license_key']) ?></code></td>
                            <td><?= htmlspecialchars($row['expiry_date']) ?></td>
                            <td>
                                <?php if ($effective_status === 'active'): ?>
                                    <span class="badge badge-active">Active</span>
                                <?php elseif ($effective_status === 'expired'): ?>
                                    <span class="badge badge-expired">Expired</span>
                                <?php else: ?>
                                    <span class="badge badge-revoked">Revoked</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($effective_status !== 'revoked'): ?>
                                    <a href="admin.php?action=revoke&id=<?= $row['id'] ?>&search=<?= urlencode($search) ?>&status=<?= urlencode($filter_status) ?>" class="btn-action-sm btn-revoke" onclick="return confirm('Are you sure you want to revoke key #<?= $row['id'] ?>?')">Revoke</a>
                                <?php endif; ?>
                                <a href="admin.php?action=delete&id=<?= $row['id'] ?>&search=<?= urlencode($search) ?>&status=<?= urlencode($filter_status) ?>" class="btn-action-sm btn-delete" onclick="return confirm('Permanently delete record #<?= $row['id'] ?>?')">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>
