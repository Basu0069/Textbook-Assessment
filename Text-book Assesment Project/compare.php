<?php
require_once 'config.php';
require_once 'includes/header.php';
require_once 'includes/google_books.php';

// Initialize Google Books API
$googleBooks = new GoogleBooksAPI(GOOGLE_BOOKS_API_KEY);

// Get book IDs from URL if they exist
$book1Id = isset($_GET['book1']) ? $_GET['book1'] : '';
$book2Id = isset($_GET['book2']) ? $_GET['book2'] : '';

// Get search query
$query = isset($_GET['q']) ? trim($_GET['q']) : '';

// Search for books if query exists
$searchResults = null;
if (!empty($query)) {
    $searchResults = $googleBooks->searchBooks($query, 20);
}

// Get book details if IDs are provided
$book1 = null;
$book2 = null;
$error_message = '';

if (!empty($book1Id)) {
    $book1 = $googleBooks->getBookById($book1Id);
    if ($book1 === null) {
        $error_message = 'Error! Book not found or error fetching book details.';
    }
}
if (!empty($book2Id)) {
    $book2 = $googleBooks->getBookById($book2Id);
    if ($book2 === null) {
        $error_message = 'Error! Book not found or error fetching book details.';
    }
}
?>

<main class="pt-20">
    <!-- Compare Books Section -->
    <section id="compare" class="section-spacing">
        <div class="container mx-auto px-6">
            <h2 class="text-3xl font-bold text-center text-gray-800 dark:text-white mb-12">Compare Books</h2>
            
            <?php if (!empty($error_message)): ?>
                <div class="max-w-6xl mx-auto bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-6" role="alert">
                    <span class="block sm:inline"><?php echo htmlspecialchars($error_message); ?></span>
                </div>
            <?php endif; ?>

            <?php if (!empty($book1) && !empty($book2)): ?>
                <!-- Comparison Results -->
                <div class="max-w-7xl mx-auto bg-white dark:bg-gray-800 rounded-lg p-8 shadow-lg">
                    <!-- Book Comparison Header -->
                    <div class="text-center mb-8">
                        <h3 class="text-3xl font-bold text-gray-800 dark:text-white">Book Comparison</h3>
                        <p class="text-gray-600 dark:text-gray-300 mt-2 text-lg">Compare the key features of both books</p>
                    </div>

                    <!-- Book Comparison Grid -->
                    <div class="grid md:grid-cols-2 gap-12">
                        <!-- Book 1 -->
                        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-8">
                            <div class="flex flex-col items-center mb-8">
                                <?php if(isset($book1['volumeInfo']['imageLinks']['thumbnail'])): ?>
                                    <img src="<?php echo htmlspecialchars($book1['volumeInfo']['imageLinks']['thumbnail']); ?>" 
                                         alt="<?php echo htmlspecialchars($book1['volumeInfo']['title']); ?>" 
                                         class="w-64 h-80 object-cover rounded-lg shadow-xl mb-6">
                                <?php else: ?>
                                    <div class="w-64 h-80 bg-gray-200 dark:bg-gray-600 rounded-lg shadow-xl mb-6 flex items-center justify-center">
                                        <i class="fas fa-book text-6xl text-gray-400"></i>
                                    </div>
                                <?php endif; ?>
                                
                                <h3 class="text-2xl font-semibold text-gray-800 dark:text-white text-center mb-3">
                                    <?php echo htmlspecialchars($book1['volumeInfo']['title']); ?>
                                </h3>
                                
                                <?php if(isset($book1['volumeInfo']['authors'])): ?>
                                    <p class="text-gray-600 dark:text-gray-300 text-center text-lg mb-4">
                                        By <?php echo htmlspecialchars(implode(', ', $book1['volumeInfo']['authors'])); ?>
                                    </p>
                                <?php endif; ?>
                                
                                <a href="<?php echo htmlspecialchars($book1['volumeInfo']['infoLink']); ?>" 
                                   target="_blank"
                                   class="inline-flex items-center bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition duration-200 text-lg">
                                    <i class="fas fa-external-link-alt mr-2"></i>
                                    View on Google Books
                                </a>
                            </div>

                            <!-- Book 1 Details -->
                            <div class="space-y-6">
                                <div class="bg-white dark:bg-gray-800 rounded-lg p-6 shadow-sm">
                                    <h4 class="font-semibold text-gray-800 dark:text-white mb-4 text-xl">Publication Details</h4>
                                    <ul class="space-y-3 text-base text-gray-600 dark:text-gray-300">
                                        <?php if(isset($book1['volumeInfo']['publishedDate'])): ?>
                                            <li class="flex items-center">
                                                <i class="fas fa-calendar-alt mr-3 text-blue-500 text-xl"></i>
                                                <span class="font-medium">Published:</span> 
                                                <span class="ml-2"><?php echo htmlspecialchars($book1['volumeInfo']['publishedDate']); ?></span>
                                            </li>
                                        <?php endif; ?>
                                        <?php if(isset($book1['volumeInfo']['publisher'])): ?>
                                            <li class="flex items-center">
                                                <i class="fas fa-building mr-3 text-blue-500 text-xl"></i>
                                                <span class="font-medium">Publisher:</span> 
                                                <span class="ml-2"><?php echo htmlspecialchars($book1['volumeInfo']['publisher']); ?></span>
                                            </li>
                                        <?php endif; ?>
                                        <?php if(isset($book1['volumeInfo']['language'])): ?>
                                            <li class="flex items-center">
                                                <i class="fas fa-language mr-3 text-blue-500 text-xl"></i>
                                                <span class="font-medium">Language:</span> 
                                                <span class="ml-2"><?php echo htmlspecialchars($book1['volumeInfo']['language']); ?></span>
                                            </li>
                                        <?php endif; ?>
                                        <?php if(isset($book1['volumeInfo']['pageCount'])): ?>
                                            <li class="flex items-center">
                                                <i class="fas fa-file-alt mr-3 text-blue-500 text-xl"></i>
                                                <span class="font-medium">Pages:</span> 
                                                <span class="ml-2"><?php echo htmlspecialchars($book1['volumeInfo']['pageCount']); ?></span>
                                            </li>
                                        <?php endif; ?>
                                    </ul>
                                </div>

                                <?php if(isset($book1['volumeInfo']['description'])): ?>
                                    <div class="bg-white dark:bg-gray-800 rounded-lg p-6 shadow-sm">
                                        <h4 class="font-semibold text-gray-800 dark:text-white mb-4 text-xl">Description</h4>
                                        <p class="text-base text-gray-600 dark:text-gray-300 leading-relaxed">
                                            <?php echo htmlspecialchars(substr(strip_tags($book1['volumeInfo']['description']), 0, 500)) . '...'; ?>
                                        </p>
                                    </div>
                                <?php endif; ?>

                                <?php if(isset($book1['volumeInfo']['categories'])): ?>
                                    <div class="bg-white dark:bg-gray-800 rounded-lg p-6 shadow-sm">
                                        <h4 class="font-semibold text-gray-800 dark:text-white mb-4 text-xl">Categories</h4>
                                        <div class="flex flex-wrap gap-3">
                                            <?php foreach($book1['volumeInfo']['categories'] as $category): ?>
                                                <span class="bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 text-sm px-4 py-2 rounded-full">
                                                    <?php echo htmlspecialchars($category); ?>
                                                </span>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                <?php endif; ?>

                                <?php if(isset($book1['volumeInfo']['averageRating'])): ?>
                                    <div class="bg-white dark:bg-gray-800 rounded-lg p-6 shadow-sm">
                                        <h4 class="font-semibold text-gray-800 dark:text-white mb-4 text-xl">Ratings</h4>
                                        <div class="flex items-center space-x-4">
                                            <div class="flex items-center">
                                                <i class="fas fa-star text-yellow-500 text-2xl mr-2"></i>
                                                <span class="text-xl font-medium"><?php echo htmlspecialchars($book1['volumeInfo']['averageRating']); ?>/5</span>
                                            </div>
                                            <?php if(isset($book1['volumeInfo']['ratingsCount'])): ?>
                                                <span class="text-gray-600 dark:text-gray-300">
                                                    (<?php echo htmlspecialchars($book1['volumeInfo']['ratingsCount']); ?> ratings)
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Book 2 -->
                        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-8">
                            <div class="flex flex-col items-center mb-8">
                                <?php if(isset($book2['volumeInfo']['imageLinks']['thumbnail'])): ?>
                                    <img src="<?php echo htmlspecialchars($book2['volumeInfo']['imageLinks']['thumbnail']); ?>" 
                                         alt="<?php echo htmlspecialchars($book2['volumeInfo']['title']); ?>" 
                                         class="w-64 h-80 object-cover rounded-lg shadow-xl mb-6">
                                <?php else: ?>
                                    <div class="w-64 h-80 bg-gray-200 dark:bg-gray-600 rounded-lg shadow-xl mb-6 flex items-center justify-center">
                                        <i class="fas fa-book text-6xl text-gray-400"></i>
                                    </div>
                                <?php endif; ?>
                                
                                <h3 class="text-2xl font-semibold text-gray-800 dark:text-white text-center mb-3">
                                    <?php echo htmlspecialchars($book2['volumeInfo']['title']); ?>
                                </h3>
                                
                                <?php if(isset($book2['volumeInfo']['authors'])): ?>
                                    <p class="text-gray-600 dark:text-gray-300 text-center text-lg mb-4">
                                        By <?php echo htmlspecialchars(implode(', ', $book2['volumeInfo']['authors'])); ?>
                                    </p>
                                <?php endif; ?>
                                
                                <a href="<?php echo htmlspecialchars($book2['volumeInfo']['infoLink']); ?>" 
                                   target="_blank"
                                   class="inline-flex items-center bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition duration-200 text-lg">
                                    <i class="fas fa-external-link-alt mr-2"></i>
                                    View on Google Books
                                </a>
                            </div>

                            <!-- Book 2 Details -->
                            <div class="space-y-6">
                                <div class="bg-white dark:bg-gray-800 rounded-lg p-6 shadow-sm">
                                    <h4 class="font-semibold text-gray-800 dark:text-white mb-4 text-xl">Publication Details</h4>
                                    <ul class="space-y-3 text-base text-gray-600 dark:text-gray-300">
                                        <?php if(isset($book2['volumeInfo']['publishedDate'])): ?>
                                            <li class="flex items-center">
                                                <i class="fas fa-calendar-alt mr-3 text-blue-500 text-xl"></i>
                                                <span class="font-medium">Published:</span> 
                                                <span class="ml-2"><?php echo htmlspecialchars($book2['volumeInfo']['publishedDate']); ?></span>
                                            </li>
                                        <?php endif; ?>
                                        <?php if(isset($book2['volumeInfo']['publisher'])): ?>
                                            <li class="flex items-center">
                                                <i class="fas fa-building mr-3 text-blue-500 text-xl"></i>
                                                <span class="font-medium">Publisher:</span> 
                                                <span class="ml-2"><?php echo htmlspecialchars($book2['volumeInfo']['publisher']); ?></span>
                                            </li>
                                        <?php endif; ?>
                                        <?php if(isset($book2['volumeInfo']['language'])): ?>
                                            <li class="flex items-center">
                                                <i class="fas fa-language mr-3 text-blue-500 text-xl"></i>
                                                <span class="font-medium">Language:</span> 
                                                <span class="ml-2"><?php echo htmlspecialchars($book2['volumeInfo']['language']); ?></span>
                                            </li>
                                        <?php endif; ?>
                                        <?php if(isset($book2['volumeInfo']['pageCount'])): ?>
                                            <li class="flex items-center">
                                                <i class="fas fa-file-alt mr-3 text-blue-500 text-xl"></i>
                                                <span class="font-medium">Pages:</span> 
                                                <span class="ml-2"><?php echo htmlspecialchars($book2['volumeInfo']['pageCount']); ?></span>
                                            </li>
                                        <?php endif; ?>
                                    </ul>
                                </div>

                                <?php if(isset($book2['volumeInfo']['description'])): ?>
                                    <div class="bg-white dark:bg-gray-800 rounded-lg p-6 shadow-sm">
                                        <h4 class="font-semibold text-gray-800 dark:text-white mb-4 text-xl">Description</h4>
                                        <p class="text-base text-gray-600 dark:text-gray-300 leading-relaxed">
                                            <?php echo htmlspecialchars(substr(strip_tags($book2['volumeInfo']['description']), 0, 500)) . '...'; ?>
                                        </p>
                                    </div>
                                <?php endif; ?>

                                <?php if(isset($book2['volumeInfo']['categories'])): ?>
                                    <div class="bg-white dark:bg-gray-800 rounded-lg p-6 shadow-sm">
                                        <h4 class="font-semibold text-gray-800 dark:text-white mb-4 text-xl">Categories</h4>
                                        <div class="flex flex-wrap gap-3">
                                            <?php foreach($book2['volumeInfo']['categories'] as $category): ?>
                                                <span class="bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 text-sm px-4 py-2 rounded-full">
                                                    <?php echo htmlspecialchars($category); ?>
                                                </span>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                <?php endif; ?>

                                <?php if(isset($book2['volumeInfo']['averageRating'])): ?>
                                    <div class="bg-white dark:bg-gray-800 rounded-lg p-6 shadow-sm">
                                        <h4 class="font-semibold text-gray-800 dark:text-white mb-4 text-xl">Ratings</h4>
                                        <div class="flex items-center space-x-4">
                                            <div class="flex items-center">
                                                <i class="fas fa-star text-yellow-500 text-2xl mr-2"></i>
                                                <span class="text-xl font-medium"><?php echo htmlspecialchars($book2['volumeInfo']['averageRating']); ?>/5</span>
                                            </div>
                                            <?php if(isset($book2['volumeInfo']['ratingsCount'])): ?>
                                                <span class="text-gray-600 dark:text-gray-300">
                                                    (<?php echo htmlspecialchars($book2['volumeInfo']['ratingsCount']); ?> ratings)
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Comparison Summary -->
                    <div class="mt-12 pt-12 border-t border-gray-200 dark:border-gray-700">
                        <h3 class="text-2xl font-semibold text-gray-800 dark:text-white mb-8 text-center">Comparison Summary</h3>
                        <div class="grid md:grid-cols-2 gap-8">
                            <div class="bg-gray-50 dark:bg-gray-700 p-8 rounded-lg">
                                <h4 class="font-semibold text-gray-800 dark:text-white mb-6 text-xl text-center"><?php echo htmlspecialchars($book1['volumeInfo']['title']); ?></h4>
                                <ul class="space-y-4">
                                    <?php if(isset($book1['volumeInfo']['pageCount'])): ?>
                                        <li class="flex items-center text-gray-600 dark:text-gray-300">
                                            <i class="fas fa-file-alt mr-4 text-blue-500 text-xl"></i>
                                            <span class="font-medium">Pages:</span> 
                                            <span class="ml-2"><?php echo htmlspecialchars($book1['volumeInfo']['pageCount']); ?></span>
                                        </li>
                                    <?php endif; ?>
                                    <?php if(isset($book1['volumeInfo']['averageRating'])): ?>
                                        <li class="flex items-center text-gray-600 dark:text-gray-300">
                                            <i class="fas fa-star mr-4 text-yellow-500 text-xl"></i>
                                            <span class="font-medium">Rating:</span> 
                                            <span class="ml-2"><?php echo htmlspecialchars($book1['volumeInfo']['averageRating']); ?>/5</span>
                                        </li>
                                    <?php endif; ?>
                                    <?php if(isset($book1['volumeInfo']['maturityRating'])): ?>
                                        <li class="flex items-center text-gray-600 dark:text-gray-300">
                                            <i class="fas fa-user-shield mr-4 text-green-500 text-xl"></i>
                                            <span class="font-medium">Maturity Rating:</span> 
                                            <span class="ml-2"><?php echo htmlspecialchars($book1['volumeInfo']['maturityRating']); ?></span>
                                        </li>
                                    <?php endif; ?>
                                </ul>
                            </div>
                            <div class="bg-gray-50 dark:bg-gray-700 p-8 rounded-lg">
                                <h4 class="font-semibold text-gray-800 dark:text-white mb-6 text-xl text-center"><?php echo htmlspecialchars($book2['volumeInfo']['title']); ?></h4>
                                <ul class="space-y-4">
                                    <?php if(isset($book2['volumeInfo']['pageCount'])): ?>
                                        <li class="flex items-center text-gray-600 dark:text-gray-300">
                                            <i class="fas fa-file-alt mr-4 text-blue-500 text-xl"></i>
                                            <span class="font-medium">Pages:</span> 
                                            <span class="ml-2"><?php echo htmlspecialchars($book2['volumeInfo']['pageCount']); ?></span>
                                        </li>
                                    <?php endif; ?>
                                    <?php if(isset($book2['volumeInfo']['averageRating'])): ?>
                                        <li class="flex items-center text-gray-600 dark:text-gray-300">
                                            <i class="fas fa-star mr-4 text-yellow-500 text-xl"></i>
                                            <span class="font-medium">Rating:</span> 
                                            <span class="ml-2"><?php echo htmlspecialchars($book2['volumeInfo']['averageRating']); ?>/5</span>
                                        </li>
                                    <?php endif; ?>
                                    <?php if(isset($book2['volumeInfo']['maturityRating'])): ?>
                                        <li class="flex items-center text-gray-600 dark:text-gray-300">
                                            <i class="fas fa-user-shield mr-4 text-green-500 text-xl"></i>
                                            <span class="font-medium">Maturity Rating:</span> 
                                            <span class="ml-2"><?php echo htmlspecialchars($book2['volumeInfo']['maturityRating']); ?></span>
                                        </li>
                                    <?php endif; ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <!-- Search and Select Form -->
                <div class="max-w-6xl mx-auto bg-white dark:bg-gray-800 rounded-lg p-8 shadow-lg">
                    <form action="compare.php" method="GET" class="space-y-6">
                        <!-- Search Input -->
                        <div>
                            <label class="block text-gray-700 dark:text-gray-300 mb-2">Search Books</label>
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
                        </div>

                        <?php if (!empty($searchResults) && isset($searchResults['items'])): ?>
                            <!-- Book Selection -->
                            <div class="grid md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-gray-700 dark:text-gray-300 mb-2">First Book</label>
                                    <div class="relative">
                                        <select name="book1" 
                                                class="w-full px-4 py-2 rounded-lg bg-gray-50 dark:bg-gray-700 text-gray-800 dark:text-white border border-gray-300 dark:border-gray-600"
                                                onchange="this.form.submit()">
                                            <option value="">Select First Book</option>
                                            <?php foreach($searchResults['items'] as $book): ?>
                                                <option value="<?php echo htmlspecialchars($book['id']); ?>" 
                                                        <?php echo $book1Id === $book['id'] ? 'selected' : ''; ?>>
                                                    <?php echo htmlspecialchars($book['volumeInfo']['title']); ?>
                                                    <?php if(isset($book['volumeInfo']['authors'])): ?>
                                                        - <?php echo htmlspecialchars($book['volumeInfo']['authors'][0]); ?>
                                                    <?php endif; ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                        <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                                            <i class="fas fa-search text-gray-400"></i>
                                        </div>
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-gray-700 dark:text-gray-300 mb-2">Second Book</label>
                                    <div class="relative">
                                        <select name="book2" 
                                                class="w-full px-4 py-2 rounded-lg bg-gray-50 dark:bg-gray-700 text-gray-800 dark:text-white border border-gray-300 dark:border-gray-600"
                                                onchange="this.form.submit()">
                                            <option value="">Select Second Book</option>
                                            <?php foreach($searchResults['items'] as $book): ?>
                                                <option value="<?php echo htmlspecialchars($book['id']); ?>" 
                                                        <?php echo $book2Id === $book['id'] ? 'selected' : ''; ?>>
                                                    <?php echo htmlspecialchars($book['volumeInfo']['title']); ?>
                                                    <?php if(isset($book['volumeInfo']['authors'])): ?>
                                                        - <?php echo htmlspecialchars($book['volumeInfo']['authors'][0]); ?>
                                                    <?php endif; ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                        <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                                            <i class="fas fa-search text-gray-400"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Search Results Preview -->
                            <div class="mt-6">
                                <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Search Results</h3>
                                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                                    <?php foreach($searchResults['items'] as $book): ?>
                                        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow duration-200">
                                            <div class="p-4">
                                                <?php if(isset($book['volumeInfo']['imageLinks']['thumbnail'])): ?>
                                                    <img src="<?php echo htmlspecialchars($book['volumeInfo']['imageLinks']['thumbnail']); ?>" 
                                                         alt="<?php echo htmlspecialchars($book['volumeInfo']['title']); ?>" 
                                                         class="w-full h-48 object-cover rounded-lg mb-3">
                                                <?php else: ?>
                                                    <div class="w-full h-48 bg-gray-200 dark:bg-gray-700 rounded-lg mb-3 flex items-center justify-center">
                                                        <i class="fas fa-book text-4xl text-gray-400"></i>
                                                    </div>
                                                <?php endif; ?>
                                                
                                                <h4 class="text-lg font-semibold text-gray-800 dark:text-white mb-2">
                                                    <?php echo htmlspecialchars($book['volumeInfo']['title']); ?>
                                                </h4>
                                                
                                                <?php if(isset($book['volumeInfo']['authors'])): ?>
                                                    <p class="text-sm text-gray-600 dark:text-gray-300 mb-2">
                                                        By <?php echo htmlspecialchars($book['volumeInfo']['authors'][0]); ?>
                                                    </p>
                                                <?php endif; ?>
                                                
                                                <div class="flex justify-between items-center mt-4">
                                                    <button type="button" 
                                                            onclick="document.querySelector('select[name=book1]').value='<?php echo htmlspecialchars($book['id']); ?>'; document.forms[0].submit();"
                                                            class="text-sm bg-blue-600 text-white px-3 py-1 rounded hover:bg-blue-700 transition duration-200">
                                                        Select as First
                                                    </button>
                                                    <button type="button" 
                                                            onclick="document.querySelector('select[name=book2]').value='<?php echo htmlspecialchars($book['id']); ?>'; document.forms[0].submit();"
                                                            class="text-sm bg-green-600 text-white px-3 py-1 rounded hover:bg-green-700 transition duration-200">
                                                        Select as Second
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                            
                            <button type="submit" class="w-full bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition duration-200 mt-6">Compare Books</button>
                        <?php endif; ?>
                    </form>
                </div>
            <?php endif; ?>
        </div>
    </section>
</main>

<?php require_once 'includes/footer.php'; ?> 