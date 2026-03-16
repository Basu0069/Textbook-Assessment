<?php
// Include the master configuration
require_once __DIR__ . '/includes/config.php';

// Error Reporting (Already in includes/config.php, but keeping for direct access if needed)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Time Zone
date_default_timezone_set('UTC');

if (!defined('DB_DSN')) {
    define('DB_FILE', __DIR__ . '/database.sqlite');
    define('DB_DSN', 'sqlite:' . DB_FILE);
}
try {
    $conn = new PDO(DB_DSN);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?> 