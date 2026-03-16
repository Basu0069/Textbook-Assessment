<?php
// Database configuration
define('DB_HOST', 'localhost');
if (!defined('DB_DSN')) {
    define('DB_FILE', __DIR__ . '/../database.sqlite');
    define('DB_DSN', 'sqlite:' . DB_FILE);
}
try {
    $pdo = new PDO(DB_DSN);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?> 