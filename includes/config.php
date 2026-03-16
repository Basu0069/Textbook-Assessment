<?php
// Prevent multiple inclusions
if (!defined('CONFIG_LOADED')) {
    define('CONFIG_LOADED', true);

    // Database configuration for SQLite - use fixed path
    define('DB_FILE', '/var/www/html/database.sqlite');
    define('DB_DSN', 'sqlite:' . DB_FILE);

    // Google Books API configuration
    if (!defined('GOOGLE_BOOKS_API_KEY')) {
        define('GOOGLE_BOOKS_API_KEY', getenv('GOOGLE_BOOKS_API_KEY') ?: '');
    }

    // CRITICAL: Turn off display_errors so PHP errors don't output as HTML
    // before headers/redirects are sent. Errors will still go to error_log.
    error_reporting(E_ALL);
    ini_set('display_errors', '0');
    ini_set('log_errors', '1');
}
