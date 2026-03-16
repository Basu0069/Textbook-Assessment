<?php
require_once 'includes/config.php';
require_once 'includes/db.php';
require_once 'includes/google_books.php';

$api = new GoogleBooksAPI();
$searchResults = [];
$error = '';
$message = '';

// Handle search
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['query'])) {
    $query = trim($_GET['query']);
    if (!empty($query)) {
        try {
            $searchResults = $api->searchBooks($query, 10);
            if (isset($searchResults['error'])) {
                $error = 'Error searching books: ' . $searchResults['error']['message'];
            }
        } catch (Exception $e) {
            $error = 'Error searching books: ' . $e->getMessage();
        }
    }
}

// Handle review submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $bookId = $_POST['book_id'] ?? '';
    $email = filter_var($_POST['email'] ?? '', FILTER_VALIDATE_EMAIL);
    $rating = intval($_POST['rating'] ?? 0);
    $reviewText = trim($_POST['review_text'] ?? '');

    if (empty($bookId)) {
        $error = 'Book ID is required';
    } elseif (!$email) {
        $error = 'Please enter a valid email address';
    } elseif ($rating < 1 || $rating > 5) {
        $error = 'Please select a valid rating (1-5 stars)';
    } elseif (empty($reviewText)) {
        $error = 'Please write your review';
    } else {
        try {
            $stmt = $pdo->prepare("
                INSERT INTO reviews (book_id, email, rating, review_text)
                VALUES (?, ?, ?, ?)
            ");
            $stmt->execute([$bookId, $email, $rating, $reviewText]);
            $message = 'Your review has been submitted successfully!';
        } catch (PDOException $e) {
            $error = 'Error submitting review: ' . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search and Review Books</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-50">
    <?php include 'includes/header.php'; ?>

    <main class="container mx-auto px-4 py-8">
        <!-- Search Form -->
        <div class="max-w-4xl mx-auto mb-8">
            <form method="GET" class="flex gap-4">
                <input type="text" name="query" placeholder="Search for books..." 
                       value="<?php echo htmlspecialchars($_GET['query'] ?? ''); ?>"
                       class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                <button type="submit" 
                        class="bg-blue-600 text-white py-2 px-6 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                    Search
                </button>
            </form>
        </div>

        <?php if ($error): ?>
            <div class="max-w-4xl mx-auto mb-8">
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                    <strong class="font-bold">Error!</strong>
                    <span class="block sm:inline"><?php echo htmlspecialchars($error); ?></span>
                </div>
            </div>
        <?php endif; ?>

        <?php if ($message): ?>
            <div class="max-w-4xl mx-auto mb-8">
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                    <strong class="font-bold">Success!</strong>
                    <span class="block sm:inline"><?php echo htmlspecialchars($message); ?></span>
                </div>
            </div>
        <?php endif; ?>

        <!-- Search Results -->
        <?php if (isset($searchResults['items']) && count($searchResults['items']) > 0): ?>
            <div class="max-w-4xl mx-auto space-y-6">
                <?php foreach ($searchResults['items'] as $book): ?>
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <div class="flex flex-col md:flex-row gap-6">
                            <?php if (isset($book['volumeInfo']['imageLinks']['thumbnail'])): ?>
                                <img src="<?php echo htmlspecialchars($book['volumeInfo']['imageLinks']['thumbnail']); ?>" 
                                     alt="<?php echo htmlspecialchars($book['volumeInfo']['title']); ?>"
                                     class="w-32 h-48 object-cover rounded-lg shadow-md">
                            <?php endif; ?>
                            
                            <div class="flex-1">
                                <h2 class="text-xl font-bold text-gray-900 mb-2">
                                    <?php echo htmlspecialchars($book['volumeInfo']['title']); ?>
                                </h2>
                                <p class="text-gray-600 mb-2">
                                    by <?php echo htmlspecialchars($book['volumeInfo']['authors'][0] ?? 'Unknown Author'); ?>
                                </p>
                                
                                <?php if (isset($book['volumeInfo']['description'])): ?>
                                    <p class="text-gray-700 mb-4 line-clamp-3">
                                        <?php echo htmlspecialchars($book['volumeInfo']['description']); ?>
                                    </p>
                                <?php endif; ?>

                                <!-- Review Button -->
                                <div class="mt-4">
                                    <a href="ratings.php?id=<?php echo $book['id']; ?>" 
                                       class="inline-block bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                                        Review This Book
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php elseif (isset($_GET['query'])): ?>
            <div class="max-w-4xl mx-auto text-center py-12">
                <p class="text-gray-600">No books found. Try a different search term.</p>
            </div>
        <?php endif; ?>
    </main>

    <?php include 'includes/footer.php'; ?>

    <script>
        function toggleReviewForm(bookId) {
            const form = document.getElementById(`review-form-${bookId}`);
            form.classList.toggle('hidden');
        }

        // Add star rating interaction
        document.querySelectorAll('input[type="radio"]').forEach(radio => {
            radio.addEventListener('change', function() {
                const bookId = this.id.split('-')[1];
                const stars = document.querySelectorAll(`label[for^="rating-${bookId}"] i`);
                const rating = parseInt(this.value);
                
                stars.forEach((star, index) => {
                    if (index < rating) {
                        star.classList.add('text-yellow-400');
                        star.classList.remove('text-gray-300');
                    } else {
                        star.classList.remove('text-yellow-400');
                        star.classList.add('text-gray-300');
                    }
                });
            });
        });
    </script>
</body>
</html> 