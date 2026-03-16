<?php
include 'config.php';

if ($conn) {
    echo "✅ Database connection successful!";
    // Optional: Show all table names to confirm DB content
    $stmt = $conn->query("SHOW TABLES");
    echo "<br><strong>Tables in DB:</strong><br>";
    while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
        echo $row[0] . "<br>";
    }
} else {
    echo "❌ Database connection failed.";
}
?>



to check  
http://localhost:8000/test-db.php
 