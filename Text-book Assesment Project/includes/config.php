<?php
// Prevent multiple inclusions
if (!defined('CONFIG_LOADED')) {
    define('CONFIG_LOADED', true);

    // Database configuration
    if (!defined('DB_HOST')) define('DB_HOST', 'localhost');
    if (!defined('DB_NAME')) define('DB_NAME', 'textbook_assessment');
    if (!defined('DB_USER')) define('DB_USER', 'root');
    if (!defined('DB_PASS')) define('DB_PASS', '');

    // Google Books API configuration
    if (!defined('GOOGLE_BOOKS_API_KEY')) define('GOOGLE_BOOKS_API_KEY', 'YOUR_GOOGLE_BOOKS_API_KEY');

    // Error reporting
    error_reporting(E_ALL);
    ini_set('display_errors', 1);

    // Session start only if not already started
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
} 