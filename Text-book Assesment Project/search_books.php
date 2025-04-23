<?php
require_once 'config.php';
require_once 'includes/google_books.php';

// Initialize Google Books API
$googleBooks = new GoogleBooksAPI(GOOGLE_BOOKS_API_KEY);

// Get search query from GET parameter
$query = isset($_GET['query']) ? trim($_GET['query']) : '';

// Set headers for JSON response
header('Content-Type: application/json');

// If no query provided, return empty result
if (empty($query)) {
    echo json_encode(['items' => []]);
    exit;
}

try {
    // Search for books
    $results = $googleBooks->searchBooks($query, 5);
    
    // Return results as JSON
    echo json_encode($results);
} catch (Exception $e) {
    // Return error message
    http_response_code(500);
    echo json_encode(['error' => 'Failed to search books: ' . $e->getMessage()]);
} 