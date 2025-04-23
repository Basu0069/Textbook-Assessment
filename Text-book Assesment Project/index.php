<?php
require_once 'config.php';
require_once 'includes/header.php';
require_once 'includes/google_books.php';

// Initialize Google Books API
$googleBooks = new GoogleBooksAPI(GOOGLE_BOOKS_API_KEY);

// Get featured books
$featuredBooks = $googleBooks->getFeaturedBooks(3);
?>

<style>
    /* Animation Classes */
    .fade-in {
        opacity: 0;
        transform: translateY(20px);
        transition: opacity 0.6s ease-out, transform 0.6s ease-out;
    }

    .fade-in.visible {
        opacity: 1;
        transform: translateY(0);
    }

    .slide-in-left {
        opacity: 0;
        transform: translateX(-30px);
        transition: opacity 0.6s ease-out, transform 0.6s ease-out;
    }

    .slide-in-left.visible {
        opacity: 1;
        transform: translateX(0);
    }

    .slide-in-right {
        opacity: 0;
        transform: translateX(30px);
        transition: opacity 0.6s ease-out, transform 0.6s ease-out;
    }

    .slide-in-right.visible {
        opacity: 1;
        transform: translateX(0);
    }

    .scale-up {
        opacity: 0;
        transform: scale(0.95);
        transition: opacity 0.6s ease-out, transform 0.6s ease-out;
    }

    .scale-up.visible {
        opacity: 1;
        transform: scale(1);
    }

    /* Hover Effects */
    .hover-lift {
        transition: transform 0.3s ease-out;
    }

    .hover-lift:hover {
        transform: translateY(-5px);
    }

    .hover-scale {
        transition: transform 0.3s ease-out;
    }

    .hover-scale:hover {
        transform: scale(1.02);
    }

    /* Button Animations */
    .btn-pulse {
        position: relative;
        overflow: hidden;
    }

    .btn-pulse::after {
        content: '';
        position: absolute;
        top: 50%;
        left: 50%;
        width: 5px;
        height: 5px;
        background: rgba(255, 255, 255, 0.5);
        opacity: 0;
        border-radius: 100%;
        transform: scale(1, 1) translate(-50%);
        transform-origin: 50% 50%;
    }

    .btn-pulse:focus:not(:active)::after {
        animation: ripple 1s ease-out;
    }

    @keyframes ripple {
        0% {
            transform: scale(0, 0);
            opacity: 0.5;
        }
        100% {
            transform: scale(20, 20);
            opacity: 0;
        }
    }
</style>

<main class="pt-0">
    <!-- Navigation Bar -->
    <!-- <nav class="bg-white dark:bg-gray-800 shadow-lg fixed w-full top-0 z-40">
        <div class="container mx-auto px-4">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center space-x-2">
                    <i class="fas fa-book-reader text-blue-600 dark:text-blue-400 text-2xl"></i>
                    <span class="text-xl font-semibold text-gray-800 dark:text-white">TextBook Assessment</span>
                </div>
                <div class="hidden md:flex space-x-6">
                    <a href="index.php" class="text-gray-600 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 transition-colors duration-200">Home</a>
                    <a href="compare.php" class="text-gray-600 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 transition-colors duration-200">Compare Books</a>
                    <a href="login.php" class="text-gray-600 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 transition-colors duration-200">Login</a>
                    <a href="#featured" class="text-gray-600 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 transition-colors duration-200">Featured Books</a>
                </div>
                <button class="md:hidden text-gray-600 dark:text-gray-300 mobile-menu-btn">
                    <i class="fas fa-bars text-2xl"></i>
                </button>
            </div>
        </div>
    </nav> -->

    <!-- Mobile Menu -->
    <!-- <div class="md:hidden hidden mobile-menu fixed w-full bg-white dark:bg-gray-800 shadow-lg z-30 mt-16">
        <div class="px-4 py-2 space-y-3">
            <a href="index.php" class="block text-gray-600 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 py-2 transition-colors duration-200">Home</a>
            <a href="compare.php" class="block text-gray-600 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 py-2 transition-colors duration-200">Compare Books</a>
            <a href="#featured" class="block text-gray-600 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 py-2 transition-colors duration-200">Featured Books</a>
            
        </div>
    </div> -->

    <!-- Hero Section -->
    <section class="bg-gradient-to-r from-blue-600 to-blue-800 text-white section-spacing">
        <div class="container mx-auto px-6">
            <div class="max-w-3xl mx-auto text-center">
                <h1 class="text-4xl md:text-5xl font-bold mb-6 fade-in">TextBook Quality Assessment Platform</h1>
                <p class="text-xl mb-8 opacity-90 fade-in" style="transition-delay: 0.2s">Rate, review, and compare textbooks with our comprehensive assessment tools</p>
                <div class="flex flex-col md:flex-row gap-4 justify-center">
                    <a href="#search" class="bg-white text-blue-600 px-8 py-3 rounded-lg font-semibold hover:bg-opacity-90 transition duration-200 hover-lift btn-pulse fade-in" style="transition-delay: 0.4s">Get Started</a>
                    <!-- <a href="#featured" class="border-2 border-white text-white px-8 py-3 rounded-lg font-semibold hover:bg-white hover:text-blue-600 transition duration-200">Featured Books</a> -->
                </div>
            </div>
        </div>
    </section>

   
    <!-- Features Section -->
    <section id="features" class="section-spacing">
        <div class="container mx-auto px-6">
            <h2 class="text-3xl font-bold text-center text-gray-800 dark:text-white mb-12 fade-in">Key Features</h2>
            <div class="grid md:grid-cols-3 gap-8">
                <div class="card bg-white dark:bg-gray-800 p-8 shadow-lg hover-scale slide-in-left">
                    <div class="text-blue-600 dark:text-blue-400 text-4xl mb-6">
                        <i class="fas fa-star"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-800 dark:text-white mb-4">Rating & Reviews</h3>
                    <p class="text-gray-600 dark:text-gray-300">Rate books on a 5-star scale and share detailed reviews with the community</p>
                </div>
                <div class="card bg-white dark:bg-gray-800 p-8 shadow-lg hover-scale fade-in" style="transition-delay: 0.2s">
                    <div class="text-blue-600 dark:text-blue-400 text-4xl mb-6">
                        <i class="fas fa-balance-scale"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-800 dark:text-white mb-4">Book Comparison</h3>
                    <p class="text-gray-600 dark:text-gray-300">Compare books side by side based on ratings, reviews, and key features</p>
                </div>
                <div class="card bg-white dark:bg-gray-800 p-8 shadow-lg hover-scale slide-in-right">
                    <div class="text-blue-600 dark:text-blue-400 text-4xl mb-6">
                        <i class="fas fa-book"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-800 dark:text-white mb-4">Book Search</h3>
                    <p class="text-gray-600 dark:text-gray-300">Search and discover books with our comprehensive search functionality</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Search Books Section -->
    <section id="search" class="py-8 bg-gray-100 dark:bg-gray-800">
        <h2 class="text-2xl font-bold text-center text-gray-800 dark:text-white mb-8 fade-in">Search & Review Books</h2>
        <div class="container mx-auto px-4">
            <div class="max-w-xl mx-auto scale-up">
                <form action="search.php" method="GET" class="flex gap-4">
                    <input type="text" name="q" placeholder="Search for books..." 
                           class="flex-1 px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 
                                  bg-white dark:bg-gray-700 text-gray-800 dark:text-white text-sm
                                  focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200">
                    <button type="submit" 
                            class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 
                                   transition duration-200 text-sm hover-lift btn-pulse">
                        <i class="fas fa-search mr-2"></i>
                        Search
                    </button>
                </form>
            </div>
        </div>
    </section>
     <!-- Featured Books Section -->
    <section class="py-8 bg-gray-50 dark:bg-gray-800">
        <div class="container mx-auto px-4">
            <h2 class="text-2xl font-bold text-center text-gray-800 dark:text-white mb-8 fade-in">Featured Books</h2>
            
            <?php if (!empty($featuredBooks) && isset($featuredBooks['items'])): ?>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-10 mx-32">
                    <?php foreach($featuredBooks['items'] as $index => $book): ?>
                        <div class="bg-white dark:bg-gray-700 rounded-lg shadow-md overflow-hidden 
                                  hover-scale hover-lift fade-in" 
                             style="transition-delay: <?php echo $index * 0.1; ?>s">
                            <div class="relative">
                                <?php 
                                // Process image URL for better quality
                                $imageUrl = isset($book['volumeInfo']['imageLinks']['thumbnail']) 
                                    ? str_replace(['http://', '&edge=curl'], ['https://', ''], $book['volumeInfo']['imageLinks']['thumbnail'])
                                    : null;
                                ?>
                                
                                <?php if($imageUrl): ?>
                                    <div class="aspect-[2/3] bg-white p-2 w-40 mt-6 mx-auto">
                                        <img src="<?php echo htmlspecialchars($imageUrl); ?>" 
                                             alt="<?php echo htmlspecialchars($book['volumeInfo']['title']); ?>" 
                                             class="w-full h-full object-contain rounded-lg transition duration-300 hover:scale-105">
                                    </div>
                                <?php else: ?>
                                    <div class="aspect-[2/3] bg-gray-200 dark:bg-gray-600 w-40 mx-auto flex items-center justify-center">
                                        <i class="fas fa-book text-4xl text-gray-400"></i>
                                    </div>
                                <?php endif; ?>
                                
                                <div class="absolute top-2 right-2">
                                    <?php if(isset($book['volumeInfo']['averageRating'])): ?>
                                        <div class="bg-yellow-500 text-white px-2 py-1 rounded-full flex items-center text-sm hover-scale">
                                            <i class="fas fa-star mr-1"></i>
                                            <span class="font-semibold"><?php echo htmlspecialchars($book['volumeInfo']['averageRating']); ?></span>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            
                            <div class="p-4">
                                <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-1 line-clamp-2">
                                    <?php echo htmlspecialchars($book['volumeInfo']['title']); ?>
                                </h3>
                                
                                <?php if(isset($book['volumeInfo']['authors'])): ?>
                                    <p class="text-sm text-gray-600 dark:text-gray-300 mb-2">
                                        By <?php echo htmlspecialchars(implode(', ', $book['volumeInfo']['authors'])); ?>
                                    </p>
                                <?php endif; ?>
                                
                                <?php if(isset($book['volumeInfo']['publishedDate'])): ?>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-2">
                                        Published: <?php echo htmlspecialchars($book['volumeInfo']['publishedDate']); ?>
                                    </p>
                                <?php endif; ?>
                                
                                <?php if(isset($book['volumeInfo']['description'])): ?>
                                    <p class="text-xs text-gray-600 dark:text-gray-300 mb-4 line-clamp-2">
                                        <?php echo htmlspecialchars(substr(strip_tags($book['volumeInfo']['description']), 0, 100)) . '...'; ?>
                                    </p>
                                <?php endif; ?>
                                
                                <div class="flex justify-between items-center">
                                    <a href="<?php echo htmlspecialchars($book['volumeInfo']['infoLink']); ?>" 
                                       target="_blank"
                                       class="inline-flex items-center bg-blue-600 text-white px-3 py-1 rounded-lg 
                                              hover:bg-blue-700 transition duration-200 text-sm hover-lift btn-pulse">
                                        <i class="fas fa-external-link-alt mr-1"></i>
                                        View Details
                                    </a>
                                    
                                    <a href="compare.php?book1=<?php echo htmlspecialchars($book['id']); ?>" 
                                       class="inline-flex items-center bg-green-600 text-white px-3 py-1 rounded-lg 
                                              hover:bg-green-700 transition duration-200 text-sm hover-lift btn-pulse">
                                        <i class="fas fa-balance-scale mr-1"></i>
                                        Compare
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="text-center py-8 fade-in">
                    <i class="fas fa-book text-4xl text-gray-400 mb-2"></i>
                    <p class="text-gray-600 dark:text-gray-300 text-base">No featured books available at the moment.</p>
                </div>
            <?php endif; ?>
        </div>
    </section>

</main>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Smooth scrolling for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });

    // Intersection Observer for animations
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('visible');
            }
        });
    }, {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    });

    // Observe all animated elements
    document.querySelectorAll('.fade-in, .slide-in-left, .slide-in-right, .scale-up').forEach((el) => {
        observer.observe(el);
    });

    // Add hover effect to cards
    document.querySelectorAll('.card').forEach(card => {
        card.addEventListener('mouseenter', () => {
            card.style.transform = 'translateY(-5px)';
        });
        card.addEventListener('mouseleave', () => {
            card.style.transform = 'translateY(0)';
        });
    });
});
</script>

<?php require_once 'includes/footer.php'; ?> 