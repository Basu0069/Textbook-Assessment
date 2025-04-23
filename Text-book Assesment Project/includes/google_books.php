<?php
require_once 'config.php';

class GoogleBooksAPI {
    private $apiKey;
    private $baseUrl = 'https://www.googleapis.com/books/v1/volumes';

    public function __construct($apiKey = null) {
        $this->apiKey = $apiKey ?: GOOGLE_BOOKS_API_KEY;
    }

    public function getBookById($bookId) {
        if (empty($bookId)) {
            error_log("Book ID is empty in getBookById");
            return ['error' => ['message' => 'Book ID is required']];
        }

        if (empty($this->apiKey)) {
            error_log("API key is not set in getBookById");
            return ['error' => ['message' => 'Google Books API key is not configured']];
        }

        // Validate and sanitize the book ID
        $bookId = trim($bookId);
        if (!preg_match('/^[a-zA-Z0-9\-_]+$/', $bookId)) {
            error_log("Invalid book ID format: " . $bookId);
            return ['error' => ['message' => 'Invalid book ID format']];
        }

        $url = $this->baseUrl . '/' . urlencode($bookId) . '?key=' . $this->apiKey;
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10); // Set timeout to 10 seconds
        
        $response = curl_exec($ch);
        
        if (curl_errno($ch)) {
            $error = curl_error($ch);
            error_log("cURL Error in getBookById: " . $error);
            curl_close($ch);
            return ['error' => ['message' => 'Failed to connect to Google Books API: ' . $error]];
        }
        
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($httpCode !== 200) {
            error_log("HTTP Error in getBookById: " . $httpCode . " for book ID: " . $bookId);
            if ($httpCode === 400) {
                return ['error' => ['message' => 'Invalid book ID or book not found in Google Books database']];
            }
            return ['error' => ['message' => 'Google Books API returned error code: ' . $httpCode]];
        }
        
        $data = json_decode($response, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            error_log("JSON Error in getBookById: " . json_last_error_msg());
            return ['error' => ['message' => 'Failed to parse API response']];
        }
        
        if (isset($data['error'])) {
            error_log("API Error in getBookById: " . json_encode($data['error']));
            return $data;
        }
        
        return $data;
    }

    public function searchBooks($query, $maxResults = 10) {
        $url = $this->baseUrl . '?q=' . urlencode($query) . '&maxResults=' . $maxResults . '&key=' . $this->apiKey;
        $response = file_get_contents($url);
        return json_decode($response, true);
    }

    public function getBookDetails($bookId) {
        if (empty($bookId)) {
            error_log("Book ID is empty in getBookDetails");
            return null;
        }

        $url = $this->baseUrl . '/' . $bookId . '?key=' . $this->apiKey;
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        
        $response = curl_exec($ch);
        
        if (curl_errno($ch)) {
            error_log("cURL Error in getBookDetails: " . curl_error($ch));
            curl_close($ch);
            return null;
        }
        
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($httpCode !== 200) {
            error_log("HTTP Error in getBookDetails: " . $httpCode);
            return null;
        }
        
        $data = json_decode($response, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            error_log("JSON Error in getBookDetails: " . json_last_error_msg());
            return null;
        }
        
        return $data;
    }

    public function getFeaturedBooks($maxResults = 3) {
        $url = $this->baseUrl . '?q=subject:education&maxResults=' . $maxResults . '&key=' . $this->apiKey;
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        
        $response = curl_exec($ch);
        curl_close($ch);
        
        return json_decode($response, true);
    }
}
?> 