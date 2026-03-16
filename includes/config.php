<?php
// Prevent multiple inclusions
if (!defined('CONFIG_LOADED')) {
    define('CONFIG_LOADED', true);

    // Database configuration for SQLite
    define('DB_FILE', __DIR__ . '/../database.sqlite');
    // For PDO connection string
    define('DB_DSN', 'sqlite:' . DB_FILE);

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
