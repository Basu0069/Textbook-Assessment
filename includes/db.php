<?php
require_once 'config.php';

try {
    $pdo = new PDO(DB_DSN);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // First, check if users table exists
    $usersTableExists = $pdo->query("SELECT name FROM sqlite_master WHERE type='table' AND name='users'")->rowCount() > 0;
    
    // If users table doesn't exist, create it
    if (!$usersTableExists) {
        $sql = "CREATE TABLE IF NOT EXISTS users (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT NOT NULL,
            email TEXT NOT NULL UNIQUE,
            password TEXT NOT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )";
        $pdo->exec($sql);
    }
    
    // Check if reviews table exists
    $reviewsTableExists = $pdo->query("SELECT name FROM sqlite_master WHERE type='table' AND name='reviews'")->rowCount() > 0;
    
    // If reviews table doesn't exist, create it
    if (!$reviewsTableExists) {
        $sql = "CREATE TABLE IF NOT EXISTS reviews (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            book_id INTEGER NOT NULL,
            user_id INTEGER NOT NULL,
            content_quality INTEGER NOT NULL CHECK (content_quality BETWEEN 1 AND 5),
            explanation_clarity INTEGER NOT NULL CHECK (explanation_clarity BETWEEN 1 AND 5),
            examples_quality INTEGER NOT NULL CHECK (examples_quality BETWEEN 1 AND 5),
            exercises_quality INTEGER NOT NULL CHECK (exercises_quality BETWEEN 1 AND 5),
            language_clarity INTEGER NOT NULL CHECK (language_clarity BETWEEN 1 AND 5),
            average_rating REAL NOT NULL,
            comments TEXT,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (book_id) REFERENCES books(book_id),
            FOREIGN KEY (user_id) REFERENCES users(id)
        );
        CREATE INDEX IF NOT EXISTS idx_book_id ON reviews(book_id);";
        $pdo->exec($sql);
    }

    // Verify tables exist
    $tables = $pdo->query("SELECT name FROM sqlite_master WHERE type='table' AND name NOT LIKE 'sqlite_%'")->fetchAll(PDO::FETCH_COLUMN);
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