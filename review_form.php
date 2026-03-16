<?php
require_once 'includes/header.php';
require_once 'includes/db.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Initialize variables
$bookId = isset($_GET['book_id']) ? $_GET['book_id'] : null;
$title = isset($_GET['title']) ? urldecode($_GET['title']) : 'Unknown Title';
$authors = isset($_GET['authors']) ? urldecode($_GET['authors']) : 'Unknown Author';
$cover = isset($_GET['cover']) ? urldecode($_GET['cover']) : 'https://via.placeholder.com/150x200?text=No+Cover';
$error = '';
$success = '';
$reviews = [];

// Redirect if no book ID
if (!$bookId) {
    header('Location: index.php');
    exit;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Validate input
        $contentQuality = intval($_POST['content_quality'] ?? 0);
        $explanationClarity = intval($_POST['explanation_clarity'] ?? 0);
        $examplesQuality = intval($_POST['examples_quality'] ?? 0);
        $exercisesQuality = intval($_POST['exercises_quality'] ?? 0);
        $languageClarity = intval($_POST['language_clarity'] ?? 0);
        $comments = trim($_POST['comments'] ?? '');

        // Validate ratings
        $ratings = [$contentQuality, $explanationClarity, $examplesQuality, $exercisesQuality, $languageClarity];
        foreach ($ratings as $rating) {
            if ($rating < 1 || $rating > 5) {
                throw new Exception('All ratings must be between 1 and 5');
            }
        }

        // Calculate average rating
        $averageRating = array_sum($ratings) / count($ratings);

        // Insert review into database
        $stmt = $pdo->prepare("INSERT INTO reviews (book_id, user_id, content_quality, explanation_clarity, 
                              examples_quality, exercises_quality, language_clarity, average_rating, comments) 
                              VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        
        $stmt->execute([
            $bookId,
            $_SESSION['user_id'],
            $contentQuality,
            $explanationClarity,
            $examplesQuality,
            $exercisesQuality,
            $languageClarity,
            $averageRating,
            $comments
        ]);

        $success = 'Thank you for your review!';
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}

// Get existing reviews with user names
$stmt = $pdo->prepare("
    SELECT r.*, u.name as username 
    FROM reviews r 
    JOIN users u ON r.user_id = u.id 
    WHERE r.book_id = ? 
    ORDER BY r.created_at DESC
");
$stmt->execute([$bookId]);
$reviews = $stmt->fetchAll();

// Calculate average ratings
$avgRatings = [
    'content_quality' => 0,
    'explanation_clarity' => 0,
    'examples_quality' => 0,
    'exercises_quality' => 0,
    'language_clarity' => 0,
    'overall' => 0
];

if (count($reviews) > 0) {
    foreach ($reviews as $review) {
        $avgRatings['content_quality'] += $review['content_quality'];
        $avgRatings['explanation_clarity'] += $review['explanation_clarity'];
        $avgRatings['examples_quality'] += $review['examples_quality'];
        $avgRatings['exercises_quality'] += $review['exercises_quality'];
        $avgRatings['language_clarity'] += $review['language_clarity'];
        $avgRatings['overall'] += $review['average_rating'];
    }

    foreach ($avgRatings as $key => $value) {
        $avgRatings[$key] = round($value / count($reviews), 1);
    }
}
?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

<main class="pt-20">
    <div class="container mx-auto px-4 py-8">
        <?php if ($error): ?>
            <div class="max-w-4xl mx-auto mb-8">
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                    <strong class="font-bold">Error!</strong>
                    <span class="block sm:inline"><?php echo htmlspecialchars($error); ?></span>
                </div>
            </div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="max-w-4xl mx-auto mb-8">
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                    <strong class="font-bold">Success!</strong>
                    <span class="block sm:inline"><?php echo htmlspecialchars($success); ?></span>
                </div>
            </div>
        <?php endif; ?>

        <div class="max-w-4xl mx-auto bg-white dark:bg-gray-800 rounded-lg shadow-lg p-8 mb-8">
            <div class="flex flex-col md:flex-row gap-6 items-center">
                <img src="<?php echo htmlspecialchars($cover); ?>" alt="Book Cover" class="w-32 h-48 object-cover rounded-lg shadow-md">
                <div class="flex-1">
                    <h1 class="text-2xl font-bold text-gray-800 dark:text-white mb-2"><?php echo htmlspecialchars($title); ?></h1>
                    <p class="text-gray-600 dark:text-gray-300 mb-4">by <?php echo htmlspecialchars($authors); ?></p>
                    
                    <?php if (count($reviews) > 0): ?>
                        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                            <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-2">Average Ratings</h3>
                            <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                                <div class="text-sm text-gray-600 dark:text-gray-300">Content Quality: <?php echo $avgRatings['content_quality']; ?>/5</div>
                                <div class="text-sm text-gray-600 dark:text-gray-300">Explanation Clarity: <?php echo $avgRatings['explanation_clarity']; ?>/5</div>
                                <div class="text-sm text-gray-600 dark:text-gray-300">Examples Quality: <?php echo $avgRatings['examples_quality']; ?>/5</div>
                                <div class="text-sm text-gray-600 dark:text-gray-300">Exercises Quality: <?php echo $avgRatings['exercises_quality']; ?>/5</div>
                                <div class="text-sm text-gray-600 dark:text-gray-300">Language Clarity: <?php echo $avgRatings['language_clarity']; ?>/5</div>
                                <div class="text-sm text-gray-600 dark:text-gray-300">Overall: <?php echo $avgRatings['overall']; ?>/5</div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="max-w-4xl mx-auto bg-white dark:bg-gray-800 rounded-lg shadow-lg p-8 mb-8">
            <h2 class="text-2xl font-bold text-gray-800 dark:text-white mb-6">Rate This Book</h2>
            <form method="POST" action="" class="space-y-6">
                <div>
                    <label for="username" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Name</label>
                    <input type="text" id="username" name="username" required
                           class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 
                                  bg-white dark:bg-gray-700 text-gray-800 dark:text-white focus:ring-2 focus:ring-blue-500">
                </div>

                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Content Quality</label>
                        <div class="rating flex items-center gap-2">
                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                <input type="radio" name="content_quality" id="content_<?php echo $i; ?>" value="<?php echo $i; ?>" required
                                       class="hidden">
                                <label for="content_<?php echo $i; ?>" 
                                       class="cursor-pointer text-2xl text-gray-300 hover:text-yellow-400 transition-colors">
                                    <i class="fas fa-star"></i>
                                </label>
                            <?php endfor; ?>
                            <span class="rating-value text-sm text-gray-600 dark:text-gray-300">0/5</span>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Explanation Clarity</label>
                        <div class="rating flex items-center gap-2">
                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                <input type="radio" name="explanation_clarity" id="explanation_<?php echo $i; ?>" value="<?php echo $i; ?>" required
                                       class="hidden">
                                <label for="explanation_<?php echo $i; ?>" 
                                       class="cursor-pointer text-2xl text-gray-300 hover:text-yellow-400 transition-colors">
                                    <i class="fas fa-star"></i>
                                </label>
                            <?php endfor; ?>
                            <span class="rating-value text-sm text-gray-600 dark:text-gray-300">0/5</span>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Examples Quality</label>
                        <div class="rating flex items-center gap-2">
                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                <input type="radio" name="examples_quality" id="examples_<?php echo $i; ?>" value="<?php echo $i; ?>" required
                                       class="hidden">
                                <label for="examples_<?php echo $i; ?>" 
                                       class="cursor-pointer text-2xl text-gray-300 hover:text-yellow-400 transition-colors">
                                    <i class="fas fa-star"></i>
                                </label>
                            <?php endfor; ?>
                            <span class="rating-value text-sm text-gray-600 dark:text-gray-300">0/5</span>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Exercises Quality</label>
                        <div class="rating flex items-center gap-2">
                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                <input type="radio" name="exercises_quality" id="exercises_<?php echo $i; ?>" value="<?php echo $i; ?>" required
                                       class="hidden">
                                <label for="exercises_<?php echo $i; ?>" 
                                       class="cursor-pointer text-2xl text-gray-300 hover:text-yellow-400 transition-colors">
                                    <i class="fas fa-star"></i>
                                </label>
                            <?php endfor; ?>
                            <span class="rating-value text-sm text-gray-600 dark:text-gray-300">0/5</span>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Language Clarity</label>
                        <div class="rating flex items-center gap-2">
                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                <input type="radio" name="language_clarity" id="language_<?php echo $i; ?>" value="<?php echo $i; ?>" required
                                       class="hidden">
                                <label for="language_<?php echo $i; ?>" 
                                       class="cursor-pointer text-2xl text-gray-300 hover:text-yellow-400 transition-colors">
                                    <i class="fas fa-star"></i>
                                </label>
                            <?php endfor; ?>
                            <span class="rating-value text-sm text-gray-600 dark:text-gray-300">0/5</span>
                        </div>
                    </div>
                </div>

                <div>
                    <label for="comments" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Additional Comments</label>
                    <textarea id="comments" name="comments" rows="4"
                              class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 
                                     bg-white dark:bg-gray-700 text-gray-800 dark:text-white focus:ring-2 focus:ring-blue-500"></textarea>
                </div>

                <button type="submit" 
                        class="w-full bg-blue-600 text-white py-3 px-6 rounded-lg font-semibold hover:bg-blue-700 
                               focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition duration-200">
                    Submit Review
                </button>
            </form>
        </div>

        <?php if (count($reviews) > 0): ?>
            <div class="max-w-4xl mx-auto bg-white dark:bg-gray-800 rounded-lg shadow-lg p-8">
                <h2 class="text-2xl font-bold text-gray-800 dark:text-white mb-6">Recent Reviews</h2>
                <div class="space-y-6">
                    <?php foreach ($reviews as $review): ?>
                        <div class="border-b border-gray-200 dark:border-gray-700 pb-6 last:border-b-0">
                            <div class="flex justify-between items-center mb-4">
                                <span class="font-semibold text-gray-800 dark:text-white"><?php echo htmlspecialchars($review['username']); ?></span>
                                <span class="text-sm text-gray-500 dark:text-gray-400">
                                    <?php echo date('M j, Y', strtotime($review['created_at'])); ?>
                                </span>
                            </div>
                            <div class="grid grid-cols-2 md:grid-cols-3 gap-4 mb-4">
                                <div class="text-sm text-gray-600 dark:text-gray-300">Content Quality: <?php echo $review['content_quality']; ?>/5</div>
                                <div class="text-sm text-gray-600 dark:text-gray-300">Explanation Clarity: <?php echo $review['explanation_clarity']; ?>/5</div>
                                <div class="text-sm text-gray-600 dark:text-gray-300">Examples Quality: <?php echo $review['examples_quality']; ?>/5</div>
                                <div class="text-sm text-gray-600 dark:text-gray-300">Exercises Quality: <?php echo $review['exercises_quality']; ?>/5</div>
                                <div class="text-sm text-gray-600 dark:text-gray-300">Language Clarity: <?php echo $review['language_clarity']; ?>/5</div>
                                <div class="text-sm text-gray-600 dark:text-gray-300">Overall: <?php echo $review['average_rating']; ?>/5</div>
                            </div>
                            <?php if (!empty($review['comments'])): ?>
                                <p class="text-gray-600 dark:text-gray-300 text-sm">
                                    <?php echo nl2br(htmlspecialchars($review['comments'])); ?>
                                </p>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</main>

<script>
// Star Rating Functionality
document.querySelectorAll('.rating').forEach(rating => {
    const inputs = rating.querySelectorAll('input');
    const valueDisplay = rating.querySelector('.rating-value');
    const labels = rating.querySelectorAll('label');
    
    // Add hover effect
    labels.forEach((label, index) => {
        label.addEventListener('mouseover', () => {
            labels.forEach((l, i) => {
                if (i <= index) {
                    l.querySelector('i').classList.add('text-yellow-400');
                    l.querySelector('i').classList.remove('text-gray-300');
                }
            });
        });

        label.addEventListener('mouseout', () => {
            const selectedInput = rating.querySelector('input:checked');
            if (!selectedInput) {
                labels.forEach(l => {
                    l.querySelector('i').classList.add('text-gray-300');
                    l.querySelector('i').classList.remove('text-yellow-400');
                });
            } else {
                const selectedValue = parseInt(selectedInput.value);
                labels.forEach((l, i) => {
                    if (i < selectedValue) {
                        l.querySelector('i').classList.add('text-yellow-400');
                        l.querySelector('i').classList.remove('text-gray-300');
                    } else {
                        l.querySelector('i').classList.add('text-gray-300');
                        l.querySelector('i').classList.remove('text-yellow-400');
                    }
                });
            }
        });
    });
    
    inputs.forEach(input => {
        input.addEventListener('change', () => {
            const selectedValue = input.value;
            valueDisplay.textContent = `${selectedValue}/5`;
            
            // Update star colors
            labels.forEach((label, index) => {
                if (index < selectedValue) {
                    label.querySelector('i').classList.add('text-yellow-400');
                    label.querySelector('i').classList.remove('text-gray-300');
                } else {
                    label.querySelector('i').classList.add('text-gray-300');
                    label.querySelector('i').classList.remove('text-yellow-400');
                }
            });
        });
    });
});
</script>

<?php require_once 'includes/footer.php'; ?> 