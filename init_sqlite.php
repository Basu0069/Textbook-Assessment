<?php
require_once __DIR__ . '/includes/config.php';

echo "Initializing SQLite Database...<br>\n";

try {
    $pdo = new PDO(DB_DSN);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Read and execute database.sql
    $sql1 = file_get_contents(__DIR__ . '/database.sql');
    $pdo->exec($sql1);
    echo "Successfully executed database.sql<br>\n";
    
    // Read and execute create_reviews_table.sql
    $sql2 = file_get_contents(__DIR__ . '/create_reviews_table.sql');
    $pdo->exec($sql2);
    echo "Successfully executed create_reviews_table.sql<br>\n";
    
    echo "Database setup complete! SQLite database file created at " . DB_FILE . "<br>\n";
} catch(PDOException $e) {
    echo "Error setting up database: " . $e->getMessage() . "<br>\n";
}
