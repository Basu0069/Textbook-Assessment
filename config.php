<?php
// This file is for direct access (e.g., test-db.php, init scripts)
// For pages, use includes/db.php which includes includes/config.php
require_once __DIR__ . '/includes/config.php';

// Time Zone
date_default_timezone_set('UTC');

try {
    $conn = new PDO(DB_DSN);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Also set $pdo for compatibility with db.php consumers
    $pdo = $conn;
} catch(PDOException $e) {
    error_log("Connection failed: " . $e->getMessage());
    die("Connection failed. Please check server logs.");
}