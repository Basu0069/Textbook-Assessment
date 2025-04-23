<footer class="bg-gray-800 text-white py-8">
    <div class="container mx-auto px-6">
        <div class="grid md:grid-cols-3 gap-8">
            <div>
                <h3 class="text-lg font-semibold mb-4">About Us</h3>
                <p class="text-gray-400">A comprehensive platform for book assessment, comparison, and reviews.</p>
            </div>
            <div>
                <h3 class="text-lg font-semibold mb-4">Quick Links</h3>
                <ul class="space-y-2">
                    <li><a href="index.php" class="text-gray-400 hover:text-white transition duration-200">Home</a></li>
                    <li><a href="search.php" class="text-gray-400 hover:text-white transition duration-200">Search Books</a></li>
                    <li><a href="compare.php" class="text-gray-400 hover:text-white transition duration-200">Compare Books</a></li>
                </ul>
            </div>
            <div>
                <h3 class="text-lg font-semibold mb-4">Contact</h3>
                <ul class="space-y-2">
                    <li class="text-gray-400">
                        <i class="fas fa-phone mr-2"></i>
                        <a href="tel:9346297136" class="hover:text-white transition duration-200">9346297136</a>
                    </li>
                    <li class="text-gray-400">
                        <i class="fas fa-envelope mr-2"></i>
                        <a href="mailto:divyansht233@gmail.com" class="hover:text-white transition duration-200">divyansht233@gmail.com</a>
                    </li>
                </ul>
            </div>
        </div>
        <div class="border-t border-gray-700 mt-8 pt-8 text-center text-gray-400">
            <p>&copy; <?php echo date('Y'); ?> Book Assessment Platform. All rights reserved.</p>
        </div>
    </div>
</footer>

<!-- Scripts -->
<script src="https://cdn.tailwindcss.com"></script>
<script>
    // Mobile menu toggle
    document.querySelector('.mobile-menu-btn').addEventListener('click', function() {
        document.querySelector('.mobile-menu').classList.toggle('hidden');
    });
</script>
</body>
</html> 