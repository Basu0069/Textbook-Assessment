<?php
require_once 'config.php';
require_once 'includes/header.php';
require_once 'includes/google_books.php';

// Initialize Google Books API
$googleBooks = new GoogleBooksAPI(GOOGLE_BOOKS_API_KEY);

// Get book IDs from URL
$book1Id = isset($_GET['book1']) ? trim($_GET['book1']) : '';
$book2Id = isset($_GET['book2']) ? trim($_GET['book2']) : '';

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
    if (isset($book1['error'])) {
        $error_message = 'Could not load Book 1: ' . $book1['error']['message'];
        $book1 = null;
    }
}
if (!empty($book2Id)) {
    $book2 = $googleBooks->getBookById($book2Id);
    if (isset($book2['error'])) {
        $error_message = 'Could not load Book 2: ' . $book2['error']['message'];
        $book2 = null;
    }
}

// Helper: render stars
function renderStars($rating) {
    $full = floor($rating);
    $half = ($rating - $full) >= 0.5 ? 1 : 0;
    $empty = 5 - $full - $half;
    $html = '';
    for ($i = 0; $i < $full; $i++)  $html .= '<i class="fas fa-star text-yellow-400"></i>';
    if ($half)                        $html .= '<i class="fas fa-star-half-alt text-yellow-400"></i>';
    for ($i = 0; $i < $empty; $i++) $html .= '<i class="far fa-star text-yellow-400"></i>';
    return $html;
}
?>

<main class="pt-20">
    <section class="py-12">
        <div class="container mx-auto px-6">
            <h2 class="text-3xl font-bold text-center text-gray-800 dark:text-white mb-3">Compare Books</h2>
            <p class="text-center text-gray-500 dark:text-gray-400 mb-10">Search for books, select two to compare side by side</p>

            <?php if (!empty($error_message)): ?>
                <div class="max-w-5xl mx-auto bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                    <?php echo htmlspecialchars($error_message); ?>
                </div>
            <?php endif; ?>

            <!-- === SEARCH FORM === -->
            <div class="max-w-5xl mx-auto bg-white dark:bg-gray-800 rounded-xl shadow-md p-6 mb-8">
                <form action="compare.php" method="GET" id="compareForm">
                    <!-- Hidden fields to preserve current selections -->
                    <input type="hidden" name="book1" id="book1Hidden" value="<?php echo htmlspecialchars($book1Id); ?>">
                    <input type="hidden" name="book2" id="book2Hidden" value="<?php echo htmlspecialchars($book2Id); ?>">

                    <div class="flex gap-3 mb-4">
                        <input type="text" name="q" value="<?php echo htmlspecialchars($query); ?>"
                               placeholder="Search for books to compare..."
                               class="flex-1 px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <button type="submit"
                                class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition duration-200 flex items-center gap-2">
                            <i class="fas fa-search"></i> Search
                        </button>
                    </div>

                    <!-- Selection Status -->
                    <div class="grid md:grid-cols-2 gap-4">
                        <div class="flex items-center gap-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg px-4 py-3">
                            <div class="w-7 h-7 rounded-full bg-blue-600 text-white flex items-center justify-center font-bold text-sm flex-shrink-0">1</div>
                            <div class="flex-1 min-w-0">
                                <p class="text-xs text-gray-500 dark:text-gray-400 mb-0.5">Book 1</p>
                                <p id="book1Label" class="text-sm font-medium text-gray-800 dark:text-white truncate">
                                    <?php echo !empty($book1) ? htmlspecialchars($book1['volumeInfo']['title']) : 'Not selected'; ?>
                                </p>
                            </div>
                            <?php if (!empty($book1Id)): ?>
                                <button type="button" onclick="clearBook(1)" class="text-red-500 hover:text-red-700 text-xs flex-shrink-0">
                                    <i class="fas fa-times"></i>
                                </button>
                            <?php endif; ?>
                        </div>
                        <div class="flex items-center gap-3 bg-green-50 dark:bg-green-900/20 rounded-lg px-4 py-3">
                            <div class="w-7 h-7 rounded-full bg-green-600 text-white flex items-center justify-center font-bold text-sm flex-shrink-0">2</div>
                            <div class="flex-1 min-w-0">
                                <p class="text-xs text-gray-500 dark:text-gray-400 mb-0.5">Book 2</p>
                                <p id="book2Label" class="text-sm font-medium text-gray-800 dark:text-white truncate">
                                    <?php echo !empty($book2) ? htmlspecialchars($book2['volumeInfo']['title']) : 'Not selected'; ?>
                                </p>
                            </div>
                            <?php if (!empty($book2Id)): ?>
                                <button type="button" onclick="clearBook(2)" class="text-red-500 hover:text-red-700 text-xs flex-shrink-0">
                                    <i class="fas fa-times"></i>
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                </form>
            </div>

            <?php if (!empty($searchResults) && isset($searchResults['items'])): ?>
                <!-- === BOOK GRID FOR SELECTION === -->
                <div class="max-w-5xl mx-auto mb-10">
                    <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-200 mb-4">
                        Search Results — click to add to comparison
                    </h3>
                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4">
                        <?php foreach($searchResults['items'] as $book): 
                            $isBook1 = $book['id'] === $book1Id;
                            $isBook2 = $book['id'] === $book2Id;
                            $thumb = isset($book['volumeInfo']['imageLinks']['thumbnail'])
                                ? str_replace('http://', 'https://', $book['volumeInfo']['imageLinks']['thumbnail'])
                                : 'https://placehold.co/200x280/e2e8f0/1e293b?text=No+Cover';
                        ?>
                            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden border-2 transition-all duration-200
                                        <?php echo $isBook1 ? 'border-blue-500' : ($isBook2 ? 'border-green-500' : 'border-transparent hover:border-gray-300 dark:hover:border-gray-500'); ?>"
                                 id="card-<?php echo htmlspecialchars($book['id']); ?>">
                                <img src="<?php echo htmlspecialchars($thumb); ?>"
                                     alt="<?php echo htmlspecialchars($book['volumeInfo']['title']); ?>"
                                     class="w-full h-44 object-cover">
                                <div class="p-3">
                                    <h4 class="text-xs font-semibold text-gray-800 dark:text-white line-clamp-2 mb-1">
                                        <?php echo htmlspecialchars($book['volumeInfo']['title']); ?>
                                    </h4>
                                    <?php if(isset($book['volumeInfo']['authors'])): ?>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 line-clamp-1 mb-2">
                                            <?php echo htmlspecialchars($book['volumeInfo']['authors'][0]); ?>
                                        </p>
                                    <?php endif; ?>

                                    <?php if ($isBook1): ?>
                                        <span class="block text-center text-xs bg-blue-100 text-blue-700 rounded px-2 py-1 font-medium">&#10003; Book 1</span>
                                    <?php elseif ($isBook2): ?>
                                        <span class="block text-center text-xs bg-green-100 text-green-700 rounded px-2 py-1 font-medium">&#10003; Book 2</span>
                                    <?php else: ?>
                                        <div class="flex gap-1">
                                            <button type="button"
                                                    onclick="selectBook(1, '<?php echo htmlspecialchars($book['id']); ?>', <?php echo htmlspecialchars(json_encode($book['volumeInfo']['title'])); ?>)"
                                                    class="flex-1 text-xs bg-blue-600 text-white rounded px-1 py-1 hover:bg-blue-700 transition">
                                                + Book 1
                                            </button>
                                            <button type="button"
                                                    onclick="selectBook(2, '<?php echo htmlspecialchars($book['id']); ?>', <?php echo htmlspecialchars(json_encode($book['volumeInfo']['title'])); ?>)"
                                                    class="flex-1 text-xs bg-green-600 text-white rounded px-1 py-1 hover:bg-green-700 transition">
                                                + Book 2
                                            </button>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <!-- Compare Button -->
                    <?php if (!empty($book1Id) && !empty($book2Id)): ?>
                        <div class="mt-6 text-center">
                            <a href="compare.php?book1=<?php echo urlencode($book1Id); ?>&book2=<?php echo urlencode($book2Id); ?>&q=<?php echo urlencode($query); ?>"
                               class="inline-flex items-center gap-2 bg-blue-600 text-white px-8 py-3 rounded-xl text-lg font-semibold hover:bg-blue-700 transition shadow-md">
                                <i class="fas fa-balance-scale"></i> Compare These Books
                            </a>
                        </div>
                    <?php elseif (!empty($book1Id) || !empty($book2Id)): ?>
                        <div class="mt-4 text-center text-gray-500 dark:text-gray-400 text-sm">
                            <i class="fas fa-info-circle mr-1"></i>
                            Select <?php echo empty($book1Id) ? 'Book 1' : 'Book 2'; ?> to enable comparison
                        </div>
                    <?php endif; ?>
                </div>
            <?php elseif (empty($query)): ?>
                <div class="max-w-5xl mx-auto text-center py-10 text-gray-400">
                    <i class="fas fa-search text-5xl mb-4 block"></i>
                    <p class="text-lg">Search for books above to start comparing</p>
                </div>
            <?php else: ?>
                <div class="max-w-5xl mx-auto text-center py-10 text-gray-400">
                    <i class="fas fa-book-open text-5xl mb-4 block"></i>
                    <p class="text-lg">No books found for "<?php echo htmlspecialchars($query); ?>"</p>
                </div>
            <?php endif; ?>

            <?php if (!empty($book1) && !empty($book2)): ?>
                <!-- === COMPARISON RESULTS === -->
                <div class="max-w-5xl mx-auto bg-white dark:bg-gray-800 rounded-xl shadow-lg p-8">
                    <h3 class="text-2xl font-bold text-center text-gray-800 dark:text-white mb-8">Side-by-Side Comparison</h3>

                    <div class="grid md:grid-cols-2 gap-8">
                        <?php foreach ([['book' => $book1, 'label' => 'Book 1', 'color' => 'blue'], ['book' => $book2, 'label' => 'Book 2', 'color' => 'green']] as $slot): 
                            $b = $slot['book'];
                            $c = $slot['color'];
                            $thumb = isset($b['volumeInfo']['imageLinks']['thumbnail'])
                                ? str_replace('http://', 'https://', $b['volumeInfo']['imageLinks']['thumbnail'])
                                : 'https://placehold.co/300x420/e2e8f0/1e293b?text=No+Cover';
                        ?>
                            <div class="border-2 border-<?php echo $c; ?>-200 dark:border-<?php echo $c; ?>-800 rounded-xl p-6">
                                <!-- Book Header -->
                                <div class="text-center mb-6">
                                    <span class="inline-block bg-<?php echo $c; ?>-100 dark:bg-<?php echo $c; ?>-900 text-<?php echo $c; ?>-700 dark:text-<?php echo $c; ?>-300 text-xs font-semibold px-3 py-1 rounded-full mb-3"><?php echo $slot['label']; ?></span>
                                    <img src="<?php echo htmlspecialchars($thumb); ?>"
                                         alt="<?php echo htmlspecialchars($b['volumeInfo']['title']); ?>"
                                         class="w-40 h-56 object-cover rounded-lg shadow-lg mx-auto mb-4">
                                    <h3 class="text-xl font-bold text-gray-800 dark:text-white mb-1">
                                        <?php echo htmlspecialchars($b['volumeInfo']['title']); ?>
                                    </h3>
                                    <?php if(isset($b['volumeInfo']['authors'])): ?>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">
                                            By <?php echo htmlspecialchars(implode(', ', $b['volumeInfo']['authors'])); ?>
                                        </p>
                                    <?php endif; ?>
                                </div>

                                <!-- Stats Table -->
                                <div class="space-y-3 text-sm">
                                    <?php if(isset($b['volumeInfo']['publishedDate'])): ?>
                                        <div class="flex justify-between py-2 border-b border-gray-100 dark:border-gray-700">
                                            <span class="text-gray-500 dark:text-gray-400 flex items-center gap-2"><i class="fas fa-calendar-alt text-<?php echo $c; ?>-500"></i> Published</span>
                                            <span class="font-medium text-gray-800 dark:text-white"><?php echo htmlspecialchars($b['volumeInfo']['publishedDate']); ?></span>
                                        </div>
                                    <?php endif; ?>
                                    <?php if(isset($b['volumeInfo']['publisher'])): ?>
                                        <div class="flex justify-between py-2 border-b border-gray-100 dark:border-gray-700">
                                            <span class="text-gray-500 dark:text-gray-400 flex items-center gap-2"><i class="fas fa-building text-<?php echo $c; ?>-500"></i> Publisher</span>
                                            <span class="font-medium text-gray-800 dark:text-white text-right max-w-[60%]"><?php echo htmlspecialchars($b['volumeInfo']['publisher']); ?></span>
                                        </div>
                                    <?php endif; ?>
                                    <?php if(isset($b['volumeInfo']['pageCount'])): ?>
                                        <div class="flex justify-between py-2 border-b border-gray-100 dark:border-gray-700">
                                            <span class="text-gray-500 dark:text-gray-400 flex items-center gap-2"><i class="fas fa-file-alt text-<?php echo $c; ?>-500"></i> Pages</span>
                                            <span class="font-medium text-gray-800 dark:text-white"><?php echo htmlspecialchars($b['volumeInfo']['pageCount']); ?></span>
                                        </div>
                                    <?php endif; ?>
                                    <?php if(isset($b['volumeInfo']['language'])): ?>
                                        <div class="flex justify-between py-2 border-b border-gray-100 dark:border-gray-700">
                                            <span class="text-gray-500 dark:text-gray-400 flex items-center gap-2"><i class="fas fa-language text-<?php echo $c; ?>-500"></i> Language</span>
                                            <span class="font-medium text-gray-800 dark:text-white uppercase"><?php echo htmlspecialchars($b['volumeInfo']['language']); ?></span>
                                        </div>
                                    <?php endif; ?>
                                    <?php if(isset($b['volumeInfo']['averageRating'])): ?>
                                        <div class="flex justify-between py-2 border-b border-gray-100 dark:border-gray-700">
                                            <span class="text-gray-500 dark:text-gray-400 flex items-center gap-2"><i class="fas fa-star text-<?php echo $c; ?>-500"></i> Rating</span>
                                            <span class="font-medium text-gray-800 dark:text-white flex items-center gap-1">
                                                <?php echo renderStars($b['volumeInfo']['averageRating']); ?>
                                                <span class="ml-1 text-xs text-gray-500">(<?php echo $b['volumeInfo']['averageRating']; ?>/5)</span>
                                            </span>
                                        </div>
                                    <?php endif; ?>
                                    <?php if(isset($b['volumeInfo']['categories'])): ?>
                                        <div class="py-2 border-b border-gray-100 dark:border-gray-700">
                                            <span class="text-gray-500 dark:text-gray-400 flex items-center gap-2 mb-2"><i class="fas fa-tag text-<?php echo $c; ?>-500"></i> Categories</span>
                                            <div class="flex flex-wrap gap-1">
                                                <?php foreach(array_slice($b['volumeInfo']['categories'], 0, 3) as $cat): ?>
                                                    <span class="bg-<?php echo $c; ?>-50 dark:bg-<?php echo $c; ?>-900/30 text-<?php echo $c; ?>-700 dark:text-<?php echo $c; ?>-300 text-xs px-2 py-0.5 rounded-full">
                                                        <?php echo htmlspecialchars($cat); ?>
                                                    </span>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                    <?php if(isset($b['volumeInfo']['description'])): ?>
                                        <div class="py-2">
                                            <p class="text-gray-500 dark:text-gray-400 text-xs mb-1">Description</p>
                                            <p class="text-gray-700 dark:text-gray-300 text-xs leading-relaxed line-clamp-4">
                                                <?php echo htmlspecialchars(strip_tags($b['volumeInfo']['description'])); ?>
                                            </p>
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <!-- View button -->
                                <?php if(isset($b['volumeInfo']['infoLink'])): ?>
                                    <div class="mt-4 text-center">
                                        <a href="<?php echo htmlspecialchars($b['volumeInfo']['infoLink']); ?>"
                                           target="_blank"
                                           class="inline-flex items-center gap-2 bg-<?php echo $c; ?>-600 text-white px-4 py-2 rounded-lg hover:bg-<?php echo $c; ?>-700 transition text-sm">
                                            <i class="fas fa-external-link-alt"></i> View on Google Books
                                        </a>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <!-- Start Over link -->
                    <div class="text-center mt-8">
                        <a href="compare.php" class="text-blue-600 hover:text-blue-800 text-sm underline">
                            <i class="fas fa-redo mr-1"></i> Start a new comparison
                        </a>
                    </div>
                </div>
            <?php elseif (!empty($book1Id) && empty($book2)): ?>
                <div class="max-w-5xl mx-auto text-center py-6 text-amber-600 dark:text-amber-400 bg-amber-50 dark:bg-amber-900/20 rounded-xl">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    Book data could not be loaded. The Google Books API may be temporarily rate-limited. Please try again.
                </div>
            <?php endif; ?>
        </div>
    </section>
</main>

<script>
function selectBook(slot, bookId, bookTitle) {
    document.getElementById('book' + slot + 'Hidden').value = bookId;
    document.getElementById('book' + slot + 'Label').textContent = bookTitle;

    const q = document.querySelector('input[name="q"]').value;
    const book1 = document.getElementById('book1Hidden').value;
    const book2 = document.getElementById('book2Hidden').value;

    // Redirect with all params preserved
    const params = new URLSearchParams();
    if (q) params.set('q', q);
    if (book1) params.set('book1', book1);
    if (book2) params.set('book2', book2);
    window.location.href = 'compare.php?' + params.toString();
}

function clearBook(slot) {
    const q = document.querySelector('input[name="q"]').value;
    const book1 = slot === 1 ? '' : document.getElementById('book1Hidden').value;
    const book2 = slot === 2 ? '' : document.getElementById('book2Hidden').value;

    const params = new URLSearchParams();
    if (q) params.set('q', q);
    if (book1) params.set('book1', book1);
    if (book2) params.set('book2', book2);
    window.location.href = 'compare.php?' + params.toString();
}
</script>

<?php require_once 'includes/footer.php'; ?>