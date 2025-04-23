<?php
require_once 'config.php';

try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // First, check if users table exists
    $usersTableExists = $pdo->query("SHOW TABLES LIKE 'users'")->rowCount() > 0;
    
    // If users table doesn't exist, create it
    if (!$usersTableExists) {
        $pdo->exec("CREATE TABLE users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(100) NOT NULL,
            email VARCHAR(100) NOT NULL UNIQUE,
            password VARCHAR(255) NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )");
    }
    
    // Check if reviews table exists
    $reviewsTableExists = $pdo->query("SHOW TABLES LIKE 'reviews'")->rowCount() > 0;
    
    // If reviews table doesn't exist, create it
    if (!$reviewsTableExists) {
        $pdo->exec("CREATE TABLE reviews (
            id INT AUTO_INCREMENT PRIMARY KEY,
            book_id VARCHAR(255) NOT NULL,
            user_id INT NOT NULL,
            content_quality INT NOT NULL CHECK (content_quality BETWEEN 1 AND 5),
            explanation_clarity INT NOT NULL CHECK (explanation_clarity BETWEEN 1 AND 5),
            examples_quality INT NOT NULL CHECK (examples_quality BETWEEN 1 AND 5),
            exercises_quality INT NOT NULL CHECK (exercises_quality BETWEEN 1 AND 5),
            language_clarity INT NOT NULL CHECK (language_clarity BETWEEN 1 AND 5),
            average_rating DECIMAL(3,1) NOT NULL,
            comments TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_book_id (book_id),
            INDEX idx_user_id (user_id),
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
        )");
    }

    // Verify tables exist
    $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
    if (!in_array('users', $tables)) {
        throw new Exception("Users table was not created successfully");
    }
    if (!in_array('reviews', $tables)) {
        throw new Exception("Reviews table was not created successfully");
    }
} catch(PDOException $e) {
    die("Database error: " . $e->getMessage());
} catch(Exception $e) {
    die("Error: " . $e->getMessage());
} 