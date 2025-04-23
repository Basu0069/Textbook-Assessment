<?php
// Database configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', 'Witch@69');
define('DB_NAME', 'textbook_assessment');

// Google Books API Configuration
define('GOOGLE_BOOKS_API_KEY', 'AIzaSyCUH_j6OUVNfLq15UeXGH_37IQRVFmVKuk');

// Error Reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Session Configuration
if (session_status() == PHP_SESSION_NONE) {
    ini_set('session.cookie_httponly', 1);
    ini_set('session.use_only_cookies', 1);
    ini_set('session.cookie_secure', 1);
}

// Time Zone
date_default_timezone_set('UTC');

try {
    $conn = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?> 