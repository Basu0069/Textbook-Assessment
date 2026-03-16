<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TextBook Quality Assessment Platform</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Outfit', sans-serif;
            padding-top: 5rem; /* Add padding to prevent content overlap with fixed header */
        }
        .section-spacing {
            padding: 6rem 0;
        }
        .card {
            transition: all 0.3s ease;
            border-radius: 1rem;
            overflow: hidden;
        }
        .card:hover {
            transform: translateY(-5px);
        }
        .gradient-text {
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
    </style>
</head>

<body class="bg-gray-50 dark:bg-gray-900 transition-colors duration-200">
    <!-- Navigation Bar -->
    <nav class="glass-effect shadow-lg fixed w-full top-0 z-40 bg-white dark:bg-gray-800">
        <div class="container mx-auto px-6">
            <div class="flex justify-between items-center h-20">
                <div class="flex items-center space-x-3">
                    <i class="fas fa-book-reader text-blue-600 dark:text-blue-400 text-2xl"></i>
                    <span class="text-xl font-semibold text-gray-800 dark:text-white">TextBook Assessment</span>
                </div>
                <div class="hidden md:flex space-x-8">
                    <a href="index.php" class="text-gray-600 dark:text-gray-300 hover:text-blue-600 transition-colors duration-200">Home</a>
                    <a href="find_your_book.php" class="text-gray-600 dark:text-gray-300 hover:text-blue-600 transition-colors duration-200">Find Best Books for You</a>
                    <a href="search.php" class="text-gray-600 dark:text-gray-300 hover:text-blue-600 transition-colors duration-200">Search & Review Books</a>
                    <a href="compare.php" class="text-gray-600 dark:text-gray-300 hover:text-blue-600 transition-colors duration-200">Compare Books</a>
                </div>
                <div class="flex items-center space-x-4">
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <div class="flex items-center space-x-4">
                            <span class="text-gray-600 dark:text-gray-300 hidden md:block">Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?></span>
                            <a href="logout.php" class="bg-red-600 hover:bg-red-700 text-white px-3 py-1.5 rounded-md transition-colors duration-200 flex items-center text-sm">
                                <i class="fas fa-sign-out-alt mr-1"></i>
                                <span>Logout</span>
                            </a>
                        </div>
                    <?php else: ?>
                        <a href="login.php" class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1.5 rounded-md transition-colors duration-200 flex items-center text-sm">
                            <i class="fas fa-sign-in-alt mr-1"></i>
                            <span>Login</span>
                        </a>
                        <a href="signup.php" class="bg-green-600 hover:bg-green-700 text-white px-3 py-1.5 rounded-md transition-colors duration-200 flex items-center text-sm">
                            <i class="fas fa-user-plus mr-1"></i>
                            <span>Sign Up</span>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>
    <!-- Main Content Wrapper -->
    <div class="main-content">
</body>
</html> 