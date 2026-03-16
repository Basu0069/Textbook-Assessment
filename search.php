<?php
require_once 'config.php';
require_once 'includes/header.php';
require_once 'includes/google_books.php';

// Initialize Google Books API
$googleBooks = new GoogleBooksAPI(GOOGLE_BOOKS_API_KEY);

// Get search query and filters
$query = isset($_GET['q']) ? trim($_GET['q']) : '';
$category = isset($_GET['category']) ? $_GET['category'] : '';
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$perPage = 12;
$startIndex = ($page - 1) * $perPage;

// Build search query with category filter
$searchQuery = $query;
if (!empty($category)) {
    switch($category) {
        case 'academic':
            $searchQuery .= ' subject:academic';
            break;
        case 'textbook':
            $searchQuery .= ' subject:textbook';
            break;
        case 'reference':
            $searchQuery .= ' subject:reference';
            break;
    }
}

// Search books if query exists
$searchResults = null;
if (!empty($query)) {
    $searchResults = $googleBooks->searchBooks($searchQuery, $perPage);
}
?>

<main class="pt-20">
    <div class="container mx-auto px-6 py-12">
        <!-- Search Form -->
        <div class="max-w-2xl mx-auto mb-12">
            <form action="search.php" method="GET" class="space-y-4">
                
            <h2 class="text-2xl font-bold text-center text-gray-800 dark:text-white mb-8">Search & Review Books</h2>
                <div class="flex gap-4">
                    
                    <input type="text" name="q" value="<?php echo htmlspecialchars($query); ?>" 
                           placeholder="Search for books..." 
                           class="flex-1 px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 
                                  bg-white dark:bg-gray-700 text-gray-800 dark:text-white">
                    <button type="submit" 
                            class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition duration-200">
                        Search
                    </button>
                </div>
                

            </form>
        </div>

        <!-- Search Results -->
        <?php if (!empty($query)): ?>
            <h2 class="text-2xl font-bold text-gray-800 dark:text-white mb-8">
                Search Results for "<?php echo htmlspecialchars($query); ?>"
            </h2>

            <?php if (isset($searchResults['items']) && !empty($searchResults['items'])): ?>
                <div class="grid md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8">
                    <?php foreach($searchResults['items'] as $book): ?>
                        <div class="card bg-white dark:bg-gray-800 p-6 shadow-lg">
                            <?php if(isset($book['volumeInfo']['imageLinks']['thumbnail'])): ?>
                                <img src="<?php echo htmlspecialchars($book['volumeInfo']['imageLinks']['thumbnail']); ?>" 
                                     alt="<?php echo htmlspecialchars($book['volumeInfo']['title']); ?>" 
                                     class="w-full h-64 object-cover rounded-lg mb-4">
                            <?php else: ?>
                                <div class="w-full h-64 bg-gray-200 dark:bg-gray-700 rounded-lg mb-4 flex items-center justify-center">
                                    <i class="fas fa-book text-4xl text-gray-400"></i>
                                </div>
                            <?php endif; ?>
                            
                            <h3 class="text-xl font-semibold text-gray-800 dark:text-white mb-2">
                                <?php echo htmlspecialchars($book['volumeInfo']['title']); ?>
                            </h3>
                            
                            <?php if(isset($book['volumeInfo']['authors'])): ?>
                                <p class="text-gray-600 dark:text-gray-300 mb-4">
                                    By <?php echo htmlspecialchars(implode(', ', $book['volumeInfo']['authors'])); ?>
                                </p>
                            <?php endif; ?>
                            
                            <?php if(isset($book['volumeInfo']['publishedDate'])): ?>
                                <p class="text-gray-500 dark:text-gray-400 text-sm mb-4">
                                    Published: <?php echo htmlspecialchars($book['volumeInfo']['publishedDate']); ?>
                                </p>
                            <?php endif; ?>
                            
                            <div class="flex gap-2">
                                <a href="compare.php?book1=<?php echo urlencode($book['id']); ?>" 
                                   class="flex-1 text-center bg-blue-600 text-white py-2 rounded-lg hover:bg-blue-700 transition duration-200">
                                    Compare
                                </a>
                                <a href="<?php echo htmlspecialchars($book['volumeInfo']['infoLink']); ?>" 
                                   target="_blank"
                                   class="flex-1 text-center bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-white py-2 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 transition duration-200">
                                    View
                                </a>
                                <a href="review_form.php?book_id=<?php echo urlencode($book['id']); ?>&title=<?php echo urlencode($book['volumeInfo']['title']); ?>&authors=<?php echo urlencode(implode(',', $book['volumeInfo']['authors'] ?? [])); ?>&cover=<?php echo urlencode($book['volumeInfo']['imageLinks']['thumbnail'] ?? ''); ?>" 
                                   class="flex-1 text-center bg-green-600 text-white py-2 rounded-lg hover:bg-green-700 transition duration-200">
                                    Review
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Pagination -->
                <?php if(isset($searchResults['totalItems']) && $searchResults['totalItems'] > $perPage): ?>
                    <div class="flex justify-center mt-12">
                        <div class="flex gap-2">
                            <?php if($page > 1): ?>
                                <a href="?q=<?php echo urlencode($query); ?>&page=<?php echo $page - 1; ?>" 
                                   class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-white rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 transition duration-200">
                                    Previous
                                </a>
                            <?php endif; ?>
                            
                            <?php if(isset($searchResults['items']) && count($searchResults['items']) == $perPage): ?>
                                <a href="?q=<?php echo urlencode($query); ?>&page=<?php echo $page + 1; ?>" 
                                   class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-white rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 transition duration-200">
                                    Next
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?>
            <?php else: ?>
                <p class="text-center text-gray-600 dark:text-gray-400">No books found matching your search.</p>
            <?php endif; ?>
        <?php else: ?>
            <p class="text-center text-gray-600 dark:text-gray-400">Enter a search term to find books.</p>
        <?php endif; ?>
    </div>
</main>

<?php require_once 'includes/footer.php'; ?> 