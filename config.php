<?php
/**
 * Configuration File: config.php
 * Database connection settings for MetaTrader 5 EA License Manager
 */

// Database Credentials
$host     = "localhost";
$dbname   = "mt5_license_db";
$username = "root";
$password = "";

/**
 * Returns a active MySQLi connection object or false on failure.
 *
 * @return mysqli|false
 */
function getDBConnection() {
    global $host, $username, $password, $dbname;
    
    // Suppress default connection errors to allow clean error handling
    $conn = @new mysqli($host, $username, $password, $dbname);
    
    if ($conn->connect_error) {
        return false;
    }
    
    $conn->set_charset("utf8mb4");
    return $conn;
}
?>
