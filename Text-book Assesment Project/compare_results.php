<?php
require_once 'config/database.php';
require_once 'includes/header.php';

if (!isset($_GET['book1']) || !isset($_GET['book2'])) {
    header('Location: compare.php');
    exit();
}

$book1_id = $_GET['book1'];
$book2_id = $_GET['book2'];

try {
    // Get book 1 details
    $stmt = $pdo->prepare("SELECT * FROM books WHERE book_id = ?");
    $stmt->execute([$book1_id]);
    $book1 = $stmt->fetch();

    // Get book 2 details
    $stmt = $pdo->prepare("SELECT * FROM books WHERE book_id = ?");
    $stmt->execute([$book2_id]);
    $book2 = $stmt->fetch();

    if (!$book1 || !$book2) {
        throw new Exception("One or both books not found");
    }

    // Get comparison criteria
    $stmt = $pdo->query("SELECT * FROM criteria ORDER BY category, name");
    $criteria = $stmt->fetchAll();

    // Get assessment scores for both books
    $stmt = $pdo->prepare("
        SELECT a.book_id, c.name, c.category, s.score, s.comments 
        FROM assessment_scores s 
        JOIN assessments a ON s.assessment_id = a.assessment_id 
        JOIN criteria c ON s.criterion_id = c.criterion_id 
        WHERE a.book_id IN (?, ?)
    ");
    $stmt->execute([$book1_id, $book2_id]);
    $scores = $stmt->fetchAll();

    // Organize scores by book and category
    $book1_scores = [];
    $book2_scores = [];
    foreach ($scores as $score) {
        if ($score['book_id'] == $book1_id) {
            $book1_scores[$score['category']][$score['name']] = $score;
        } else {
            $book2_scores[$score['category']][$score['name']] = $score;
        }
    }
} catch (Exception $e) {
    $error = $e->getMessage();
}
?>

<div class="container mx-auto px-6 py-12">
    <?php if (isset($error)): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
            <span class="block sm:inline"><?php echo htmlspecialchars($error); ?></span>
        </div>
    <?php else: ?>
        <h1 class="text-4xl font-bold text-center text-gray-800 dark:text-white mb-12">
            <?php echo htmlspecialchars($book1['title']); ?> vs <?php echo htmlspecialchars($book2['title']); ?>
        </h1>

        <!-- Book Information -->
        <div class="grid md:grid-cols-2 gap-8 mb-12">
            <div class="bg-white dark:bg-gray-800 rounded-lg p-6 shadow-lg">
                <img src="<?php echo htmlspecialchars($book1['cover_image']); ?>" alt="<?php echo htmlspecialchars($book1['title']); ?>" class="w-full h-64 object-cover rounded-lg mb-4">
                <h2 class="text-2xl font-bold text-gray-800 dark:text-white mb-2"><?php echo htmlspecialchars($book1['title']); ?></h2>
                <p class="text-gray-600 dark:text-gray-300 mb-2">Author: <?php echo htmlspecialchars($book1['author']); ?></p>
                <p class="text-gray-600 dark:text-gray-300 mb-2">Publisher: <?php echo htmlspecialchars($book1['publisher']); ?></p>
                <p class="text-gray-600 dark:text-gray-300 mb-4">Year: <?php echo htmlspecialchars($book1['publication_year']); ?></p>
                <p class="text-gray-700 dark:text-gray-200"><?php echo htmlspecialchars($book1['description']); ?></p>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg p-6 shadow-lg">
                <img src="<?php echo htmlspecialchars($book2['cover_image']); ?>" alt="<?php echo htmlspecialchars($book2['title']); ?>" class="w-full h-64 object-cover rounded-lg mb-4">
                <h2 class="text-2xl font-bold text-gray-800 dark:text-white mb-2"><?php echo htmlspecialchars($book2['title']); ?></h2>
                <p class="text-gray-600 dark:text-gray-300 mb-2">Author: <?php echo htmlspecialchars($book2['author']); ?></p>
                <p class="text-gray-600 dark:text-gray-300 mb-2">Publisher: <?php echo htmlspecialchars($book2['publisher']); ?></p>
                <p class="text-gray-600 dark:text-gray-300 mb-4">Year: <?php echo htmlspecialchars($book2['publication_year']); ?></p>
                <p class="text-gray-700 dark:text-gray-200"><?php echo htmlspecialchars($book2['description']); ?></p>
            </div>
        </div>

        <!-- Comparison Results -->
        <div class="bg-white dark:bg-gray-800 rounded-lg p-6 shadow-lg">
            <h2 class="text-2xl font-bold text-gray-800 dark:text-white mb-6">Detailed Comparison</h2>
            
            <?php foreach ($criteria as $criterion): ?>
                <div class="mb-8">
                    <h3 class="text-xl font-semibold text-gray-700 dark:text-gray-300 mb-4"><?php echo htmlspecialchars($criterion['category']); ?></h3>
                    <div class="grid md:grid-cols-3 gap-4">
                        <div class="text-center">
                            <h4 class="font-medium text-gray-600 dark:text-gray-400 mb-2"><?php echo htmlspecialchars($criterion['name']); ?></h4>
                            <p class="text-2xl font-bold text-blue-600 dark:text-blue-400">
                                <?php echo isset($book1_scores[$criterion['category']][$criterion['name']]) ? $book1_scores[$criterion['category']][$criterion['name']]['score'] : 'N/A'; ?>
                            </p>
                        </div>
                        <div class="text-center">
                            <h4 class="font-medium text-gray-600 dark:text-gray-400 mb-2">Comparison</h4>
                            <p class="text-2xl font-bold">
                                <?php
                                $score1 = isset($book1_scores[$criterion['category']][$criterion['name']]) ? $book1_scores[$criterion['category']][$criterion['name']]['score'] : 0;
                                $score2 = isset($book2_scores[$criterion['category']][$criterion['name']]) ? $book2_scores[$criterion['category']][$criterion['name']]['score'] : 0;
                                if ($score1 > $score2) {
                                    echo '<span class="text-green-600 dark:text-green-400">↑</span>';
                                } elseif ($score1 < $score2) {
                                    echo '<span class="text-red-600 dark:text-red-400">↓</span>';
                                } else {
                                    echo '<span class="text-gray-600 dark:text-gray-400">=</span>';
                                }
                                ?>
                            </p>
                        </div>
                        <div class="text-center">
                            <h4 class="font-medium text-gray-600 dark:text-gray-400 mb-2"><?php echo htmlspecialchars($criterion['name']); ?></h4>
                            <p class="text-2xl font-bold text-blue-600 dark:text-blue-400">
                                <?php echo isset($book2_scores[$criterion['category']][$criterion['name']]) ? $book2_scores[$criterion['category']][$criterion['name']]['score'] : 'N/A'; ?>
                            </p>
                        </div>
                    </div>
                    <?php if (isset($book1_scores[$criterion['category']][$criterion['name']]['comments']) || isset($book2_scores[$criterion['category']][$criterion['name']]['comments'])): ?>
                        <div class="mt-4 grid md:grid-cols-2 gap-4">
                            <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                                <p class="text-gray-600 dark:text-gray-300"><?php echo htmlspecialchars($book1_scores[$criterion['category']][$criterion['name']]['comments'] ?? 'No comments'); ?></p>
                            </div>
                            <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                                <p class="text-gray-600 dark:text-gray-300"><?php echo htmlspecialchars($book2_scores[$criterion['category']][$criterion['name']]['comments'] ?? 'No comments'); ?></p>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php require_once 'includes/footer.php'; ?> 