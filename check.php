<?php
/**
 * API Endpoint for EA Heartbeat: check.php
 * MetaTrader 5 EA License Manager
 *
 * Checks EA license key validity and returns plain text status:
 * ACTIVE, EXPIRED, REVOKED, or INVALID.
 */

// Ensure plain text content type header for MetaTrader 5 WebRequest
header('Content-Type: text/plain; charset=utf-8');

require_once __DIR__ . '/config.php';

// Safe Database connection attempt
$conn = getDBConnection();

// Requirement: If database connection fails, return "ACTIVE" (grace period)
if (!$conn) {
    echo "ACTIVE";
    exit;
}

// Sanitize GET parameter
$key = isset($_GET['key']) ? trim($_GET['key']) : '';

if (empty($key)) {
    echo "INVALID";
    $conn->close();
    exit;
}

// Secure Prepared Statement to prevent SQL Injection
$stmt = $conn->prepare("SELECT status, expiry_date FROM licenses WHERE license_key = ?");

if (!$stmt) {
    // Fallback to grace period if statement preparation fails
    echo "ACTIVE";
    $conn->close();
    exit;
}

$stmt->bind_param("s", $key);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "INVALID";
} else {
    $row = $result->fetch_assoc();
    $status = strtolower($row['status']);
    $expiry_date = $row['expiry_date'];
    $today = date('Y-m-d');

    // Return status based on prioritized logic
    if ($status === 'revoked') {
        echo "REVOKED";
    } elseif ($today > $expiry_date) {
        echo "EXPIRED";
    } elseif ($status === 'active') {
        echo "ACTIVE";
    } else {
        echo "INVALID";
    }
}

$stmt->close();
$conn->close();
?>
