<?php
require_once 'config.php';

class GoogleBooksAPI {
    private $apiKey;
    private $baseUrl = 'https://www.googleapis.com/books/v1/volumes';

    public function __construct($apiKey = null) {
        $this->apiKey = $apiKey ?: GOOGLE_BOOKS_API_KEY;
    }

    // Registry of all mock books (keyed by ID) - returned when API is rate-limited
    private $mockBooks = [
        // Real book covers via Open Library (ISBN-based, free & public domain)
        'mock-featured-1' => ['title' => 'Fundamentals of Education', 'authors' => ['Dr. Sarah Johnson'], 'publishedDate' => '2022', 'averageRating' => 4.5, 'pageCount' => 320, 'language' => 'en', 'publisher' => 'EduPress', 'description' => 'A comprehensive guide to education principles and classroom management for modern educators.', 'infoLink' => '#', 'imageLinks' => ['thumbnail' => 'https://covers.openlibrary.org/b/isbn/9780062316097-L.jpg'], 'categories' => ['Education']],
        'mock-featured-2' => ['title' => 'Modern Teaching Methods', 'authors' => ['Prof. Alan Wright'], 'publishedDate' => '2023', 'averageRating' => 4.8, 'pageCount' => 280, 'language' => 'en', 'publisher' => 'TeachWell', 'description' => 'Latest evidence-based practices in teaching that enhance student engagement and learning outcomes.', 'infoLink' => '#', 'imageLinks' => ['thumbnail' => 'https://covers.openlibrary.org/b/isbn/9780374533557-L.jpg'], 'categories' => ['Education', 'Pedagogy']],
        'mock-featured-3' => ['title' => 'Introduction to Mathematics', 'authors' => ['Dr. Maria Chen'], 'publishedDate' => '2021', 'averageRating' => 4.3, 'pageCount' => 512, 'language' => 'en', 'publisher' => 'MathWorld', 'description' => 'A thorough introduction covering algebra, geometry, calculus and applied mathematics.', 'infoLink' => '#', 'imageLinks' => ['thumbnail' => 'https://covers.openlibrary.org/b/isbn/9780486404523-L.jpg'], 'categories' => ['Mathematics']],
        'mock-featured-4' => ['title' => 'Science for the Modern Age', 'authors' => ['Prof. James Reed'], 'publishedDate' => '2023', 'averageRating' => 4.6, 'pageCount' => 440, 'language' => 'en', 'publisher' => 'ScienceNow', 'description' => 'An engaging exploration of physics, chemistry and biology with real-world applications.', 'infoLink' => '#', 'imageLinks' => ['thumbnail' => 'https://covers.openlibrary.org/b/isbn/9780385737951-L.jpg'], 'categories' => ['Science']],
        'mock-featured-5' => ['title' => 'World History: A Complete Guide', 'authors' => ['Dr. Emily Watson'], 'publishedDate' => '2020', 'averageRating' => 4.4, 'pageCount' => 650, 'language' => 'en', 'publisher' => 'HistoryCo', 'description' => 'From ancient civilizations to the modern era, a comprehensive overview of world history.', 'infoLink' => '#', 'imageLinks' => ['thumbnail' => 'https://covers.openlibrary.org/b/isbn/9780142437230-L.jpg'], 'categories' => ['History']],
        'mock-featured-6' => ['title' => 'Computer Science Essentials', 'authors' => ['Prof. David Kumar'], 'publishedDate' => '2024', 'averageRating' => 4.9, 'pageCount' => 720, 'language' => 'en', 'publisher' => 'TechPub', 'description' => 'Algorithms, data structures, programming paradigms and software engineering best practices.', 'infoLink' => '#', 'imageLinks' => ['thumbnail' => 'https://covers.openlibrary.org/b/isbn/9780743273565-L.jpg'], 'categories' => ['Computer Science']],
        'mock-book-1' => ['title' => 'Introduction to the Topic', 'authors' => ['Jane Doe', 'John Smith'], 'publishedDate' => '2024-01-01', 'averageRating' => 4.2, 'pageCount' => 350, 'language' => 'en', 'publisher' => 'Academic Press', 'description' => 'A comprehensive introductory text covering all the core concepts.', 'infoLink' => '#', 'imageLinks' => ['thumbnail' => 'https://covers.openlibrary.org/b/isbn/9780062316097-L.jpg'], 'categories' => ['Education']],
        'mock-book-2' => ['title' => 'Advanced Study Guide', 'authors' => ['Dr. Alice Chen'], 'publishedDate' => '2023-05-15', 'averageRating' => 4.6, 'pageCount' => 480, 'language' => 'en', 'publisher' => 'Scholar Books', 'description' => 'An advanced deep-dive with real-world applications and case studies.', 'infoLink' => '#', 'imageLinks' => ['thumbnail' => 'https://covers.openlibrary.org/b/isbn/9780374533557-L.jpg'], 'categories' => ['Education', 'Advanced Studies']],
        'mock-book-3' => ['title' => 'A Practical Guide', 'authors' => ['Prof. Robert Brown'], 'publishedDate' => '2022-08-20', 'averageRating' => 4.1, 'pageCount' => 290, 'language' => 'en', 'publisher' => 'PracticePress', 'description' => 'A hands-on practical guide for professional settings.', 'infoLink' => '#', 'imageLinks' => ['thumbnail' => 'https://covers.openlibrary.org/b/isbn/9780486404523-L.jpg'], 'categories' => ['Professional']],
        'mock-book-4' => ['title' => 'For Beginners', 'authors' => ['Emily Watson'], 'publishedDate' => '2023-11-01', 'averageRating' => 4.5, 'pageCount' => 200, 'language' => 'en', 'publisher' => 'StartEasy', 'description' => 'Perfect for absolute beginners, walking step-by-step through the fundamentals.', 'infoLink' => '#', 'imageLinks' => ['thumbnail' => 'https://covers.openlibrary.org/b/isbn/9780385737951-L.jpg'], 'categories' => ['Beginner']],
        'mock-book-5' => ['title' => 'Mastering the Subject', 'authors' => ['Dr. Sam Lee'], 'publishedDate' => '2024-03-10', 'averageRating' => 4.7, 'pageCount' => 560, 'language' => 'en', 'publisher' => 'MasterClass', 'description' => 'Master the nuances with expert insights and worked examples.', 'infoLink' => '#', 'imageLinks' => ['thumbnail' => 'https://covers.openlibrary.org/b/isbn/9780142437230-L.jpg'], 'categories' => ['Expert']],
        'mock-book-6' => ['title' => 'Research & Applications', 'authors' => ['Prof. Maria García'], 'publishedDate' => '2021-06-15', 'averageRating' => 4.3, 'pageCount' => 420, 'language' => 'en', 'publisher' => 'ResearchCo', 'description' => 'An academic exploration of research trends and modern applications.', 'infoLink' => '#', 'imageLinks' => ['thumbnail' => 'https://covers.openlibrary.org/b/isbn/9780743273565-L.jpg'], 'categories' => ['Research']],
    ];

    public function getBookById($bookId) {
        if (empty($bookId)) {
            error_log("Book ID is empty in getBookById");
            return ['error' => ['message' => 'Book ID is required']];
        }

        $bookId = trim($bookId);

        // Return mock data immediately for mock IDs (no API call needed)
        if (str_starts_with($bookId, 'mock-')) {
            if (isset($this->mockBooks[$bookId])) {
                return ['id' => $bookId, 'volumeInfo' => $this->mockBooks[$bookId]];
            }
            return ['error' => ['message' => 'Mock book not found']];
        }

        if (empty($this->apiKey)) {
            error_log("API key is not set in getBookById");
            return ['error' => ['message' => 'Google Books API key is not configured']];
        }

        // Validate and sanitize the book ID
        if (!preg_match('/^[a-zA-Z0-9\-_]+$/', $bookId)) {
            error_log("Invalid book ID format: " . $bookId);
            return ['error' => ['message' => 'Invalid book ID format']];
        }

        $url = $this->baseUrl . '/' . urlencode($bookId);
        if (!empty($this->apiKey) && $this->apiKey !== 'YOUR_GOOGLE_BOOKS_API_KEY') {
            $url .= '?key=' . $this->apiKey;
        }
        
        $context = stream_context_create(['http' => ['ignore_errors' => true, 'timeout' => 10]]);
        $response = @file_get_contents($url, false, $context);
        
        if ($response === false) {
            error_log("file_get_contents Error in getBookById");
            return ['error' => ['message' => 'Failed to connect to Google Books API']];
        }
        
        $httpCode = 200;
        if (isset($http_response_header) && preg_match('/HTTP\/\d\.\d (\d{3})/', $http_response_header[0], $matches)) {
            $httpCode = intval($matches[1]);
        }
        
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
        $url = $this->baseUrl . '?q=' . urlencode($query) . '&maxResults=' . $maxResults;
        if (!empty($this->apiKey) && $this->apiKey !== 'YOUR_GOOGLE_BOOKS_API_KEY') {
            $url .= '&key=' . $this->apiKey;
        }
        $context = stream_context_create(['http' => ['ignore_errors' => true]]);
        $response = @file_get_contents($url, false, $context);
        $result = $response !== false ? json_decode($response, true) : null;
        
        if (empty($result) || isset($result['error'])) {
            // Mock data fallback - shown when API is rate-limited
            return ['items' => [
                [
                    'id' => 'mock-book-1',
                    'volumeInfo' => [
                        'title' => 'Introduction to ' . $query,
                        'authors' => ['Jane Doe', 'John Smith'],
                        'publishedDate' => '2024-01-01',
                        'description' => 'A comprehensive introductory text covering all the core concepts of ' . $query . '.',
                        'infoLink' => '#',
                        'imageLinks' => ['thumbnail' => 'https://placehold.co/400x600/dbeafe/1e3a8a?text=' . urlencode($query)]
                    ]
                ],
                [
                    'id' => 'mock-book-2',
                    'volumeInfo' => [
                        'title' => 'Advanced ' . $query,
                        'authors' => ['Dr. Alice Chen'],
                        'publishedDate' => '2023-05-15',
                        'description' => 'An advanced deep-dive into ' . $query . ' with real-world applications and case studies.',
                        'infoLink' => '#',
                        'imageLinks' => ['thumbnail' => 'https://placehold.co/400x600/dcfce7/14532d?text=Advanced']
                    ]
                ],
                [
                    'id' => 'mock-book-3',
                    'volumeInfo' => [
                        'title' => $query . ': A Practical Guide',
                        'authors' => ['Prof. Robert Brown'],
                        'publishedDate' => '2022-08-20',
                        'description' => 'A hands-on practical guide to applying ' . $query . ' in professional settings.',
                        'infoLink' => '#',
                        'imageLinks' => ['thumbnail' => 'https://placehold.co/400x600/fef9c3/713f12?text=Practical']
                    ]
                ],
                [
                    'id' => 'mock-book-4',
                    'volumeInfo' => [
                        'title' => $query . ' for Beginners',
                        'authors' => ['Emily Watson'],
                        'publishedDate' => '2023-11-01',
                        'description' => 'Perfect for absolute beginners, this guide walks you step-by-step through ' . $query . '.',
                        'infoLink' => '#',
                        'imageLinks' => ['thumbnail' => 'https://placehold.co/400x600/fce7f3/831843?text=Beginners']
                    ]
                ],
                [
                    'id' => 'mock-book-5',
                    'volumeInfo' => [
                        'title' => 'Mastering ' . $query,
                        'authors' => ['Dr. Sam Lee'],
                        'publishedDate' => '2024-03-10',
                        'description' => 'Master the nuances of ' . $query . ' with expert insights and worked examples.',
                        'infoLink' => '#',
                        'imageLinks' => ['thumbnail' => 'https://placehold.co/400x600/ede9fe/4c1d95?text=Mastering']
                    ]
                ],
                [
                    'id' => 'mock-book-6',
                    'volumeInfo' => [
                        'title' => $query . ': Research & Applications',
                        'authors' => ['Prof. Maria García'],
                        'publishedDate' => '2021-06-15',
                        'description' => 'An academic exploration of research trends and modern applications in ' . $query . '.',
                        'infoLink' => '#',
                        'imageLinks' => ['thumbnail' => 'https://placehold.co/400x600/ccfbf1/134e4a?text=Research']
                    ]
                ]
            ]];
        }
        return $result;
    }

    public function getBookDetails($bookId) {
        if (empty($bookId)) {
            error_log("Book ID is empty in getBookDetails");
            return null;
        }

        $url = $this->baseUrl . '/' . $bookId;
        if (!empty($this->apiKey) && $this->apiKey !== 'YOUR_GOOGLE_BOOKS_API_KEY') {
            $url .= '?key=' . $this->apiKey;
        }
        
        $context = stream_context_create(['http' => ['ignore_errors' => true]]);
        $response = @file_get_contents($url, false, $context);
        
        if ($response === false) {
            error_log("file_get_contents Error in getBookDetails");
            return null;
        }
        
        $httpCode = 200;
        if (isset($http_response_header) && preg_match('/HTTP\/\d\.\d (\d{3})/', $http_response_header[0], $matches)) {
            $httpCode = intval($matches[1]);
        }
        
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
        $url = $this->baseUrl . '?q=subject:education&maxResults=' . $maxResults;
        if (!empty($this->apiKey) && $this->apiKey !== 'YOUR_GOOGLE_BOOKS_API_KEY') {
            $url .= '&key=' . $this->apiKey;
        }
        
        $context = stream_context_create(['http' => ['ignore_errors' => true]]);
        $response = @file_get_contents($url, false, $context);
        
        $result = $response !== false ? json_decode($response, true) : null;
        if (empty($result) || isset($result['error'])) {
            // Mock data fallback - shown when API is rate-limited
            return ['items' => [
                [
                    'id' => 'mock-featured-1',
                    'volumeInfo' => [
                        'title' => 'Fundamentals of Education',
                        'authors' => ['Dr. Sarah Johnson'],
                        'publishedDate' => '2022',
                        'averageRating' => 4.5,
                        'description' => 'A comprehensive guide to education principles and classroom management for modern educators.',
                        'infoLink' => '#',
                        'imageLinks' => ['thumbnail' => 'https://covers.openlibrary.org/b/isbn/9780062316097-L.jpg']
                    ]
                ],
                [
                    'id' => 'mock-featured-2',
                    'volumeInfo' => [
                        'title' => 'Modern Teaching Methods',
                        'authors' => ['Prof. Alan Wright'],
                        'publishedDate' => '2023',
                        'averageRating' => 4.8,
                        'description' => 'Latest evidence-based practices in teaching that enhance student engagement and learning outcomes.',
                        'infoLink' => '#',
                        'imageLinks' => ['thumbnail' => 'https://covers.openlibrary.org/b/isbn/9780374533557-L.jpg']
                    ]
                ],
                [
                    'id' => 'mock-featured-3',
                    'volumeInfo' => [
                        'title' => 'Introduction to Mathematics',
                        'authors' => ['Dr. Maria Chen'],
                        'publishedDate' => '2021',
                        'averageRating' => 4.3,
                        'description' => 'A thorough introduction covering algebra, geometry, calculus and applied mathematics.',
                        'infoLink' => '#',
                        'imageLinks' => ['thumbnail' => 'https://covers.openlibrary.org/b/isbn/9780486404523-L.jpg']
                    ]
                ],
                [
                    'id' => 'mock-featured-4',
                    'volumeInfo' => [
                        'title' => 'Science for the Modern Age',
                        'authors' => ['Prof. James Reed'],
                        'publishedDate' => '2023',
                        'averageRating' => 4.6,
                        'description' => 'An engaging exploration of physics, chemistry and biology with real-world applications.',
                        'infoLink' => '#',
                        'imageLinks' => ['thumbnail' => 'https://covers.openlibrary.org/b/isbn/9780385737951-L.jpg']
                    ]
                ],
                [
                    'id' => 'mock-featured-5',
                    'volumeInfo' => [
                        'title' => 'World History: A Complete Guide',
                        'authors' => ['Dr. Emily Watson'],
                        'publishedDate' => '2020',
                        'averageRating' => 4.4,
                        'description' => 'From ancient civilizations to the modern era, a comprehensive overview of world history.',
                        'infoLink' => '#',
                        'imageLinks' => ['thumbnail' => 'https://covers.openlibrary.org/b/isbn/9780142437230-L.jpg']
                    ]
                ],
                [
                    'id' => 'mock-featured-6',
                    'volumeInfo' => [
                        'title' => 'Computer Science Essentials',
                        'authors' => ['Prof. David Kumar'],
                        'publishedDate' => '2024',
                        'averageRating' => 4.9,
                        'description' => 'Algorithms, data structures, programming paradigms and software engineering best practices.',
                        'infoLink' => '#',
                        'imageLinks' => ['thumbnail' => 'https://covers.openlibrary.org/b/isbn/9780743273565-L.jpg']
                    ]
                ]
            ]];
        }
        return $result;
    }
}
?> 