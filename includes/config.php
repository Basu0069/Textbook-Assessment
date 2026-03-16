<?php
// Prevent multiple inclusions
if (!defined('CONFIG_LOADED')) {
    define('CONFIG_LOADED', true);

    // Database configuration
    // On Render: set these as Environment Variables in the dashboard
    // Locally: falls back to your local credentials
    if (!defined('DB_HOST')) define('DB_HOST', getenv('DB_HOST') ?: 'localhost');
    if (!defined('DB_NAME')) define('DB_NAME', getenv('DB_NAME') ?: 'textbook_assessment');
    if (!defined('DB_USER')) define('DB_USER', getenv('DB_USER') ?: 'textbook_user');
    if (!defined('DB_PASS')) define('DB_PASS', getenv('DB_PASS') ?: 'Witch@69');

    // Google Books API configuration
    if (!defined('GOOGLE_BOOKS_API_KEY')) define('GOOGLE_BOOKS_API_KEY', getenv('GOOGLE_BOOKS_API_KEY') ?: 'YOUR_GOOGLE_BOOKS_API_KEY');

    // Error reporting - disable display in production
    error_reporting(E_ALL);
    ini_set('display_errors', getenv('APP_ENV') === 'production' ? '0' : '1');

    // Session start only if not already started
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
}
 