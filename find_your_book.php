<?php
require_once 'config.php';
require_once 'includes/header.php';
require_once 'includes/google_books.php';

// Initialize Google Books API
$googleBooks = new GoogleBooksAPI(GOOGLE_BOOKS_API_KEY);

// Define education levels and their subjects
$educationLevels = [
    'school' => [
        '1-5' => ['mathematics', 'english', 'hindi', 'evs', 'computer basics'],
        '6-8' => ['mathematics', 'science', 'social science', 'english', 'hindi', 'sanskrit', 'computer science'],
        '9-10' => ['mathematics', 'science', 'social science', 'english', 'hindi', 'sanskrit', 'computer science'],
        '11-12' => [
            'science' => ['physics', 'chemistry', 'mathematics', 'biology', 'computer science', 'english'],
            'commerce' => ['accountancy', 'business studies', 'economics', 'mathematics', 'english', 'computer science'],
            'humanities' => ['history', 'geography', 'political science', 'economics', 'psychology', 'sociology', 'english']
        ]
    ],
    'engineering' => [
        'btech' => [
            'Computer Science' => ['Data Structures', 'Algorithms', 'Operating Systems', 'Database Management', 'Computer Networks', 'Artificial Intelligence'],
            'Mechanical' => ['Thermodynamics', 'Fluid Mechanics', 'Machine Design', 'Manufacturing Technology', 'Heat Transfer'],
            'Electrical' => ['Circuit Theory', 'Power Systems', 'Control Systems', 'Electronics', 'Digital Systems'],
            'Civil' => ['Structural Analysis', 'Construction Technology', 'Environmental Engineering', 'Geotechnical Engineering', 'Transportation Engineering'],
            'Electronics' => ['Digital Electronics', 'Microprocessors', 'Communication Systems', 'VLSI Design', 'Embedded Systems']
        ],
        'mtech' => [
            'Computer Science' => ['Advanced Algorithms', 'Machine Learning', 'Cloud Computing', 'Big Data Analytics', 'Cyber Security'],
            'Mechanical' => ['Advanced Thermodynamics', 'Robotics', 'Automotive Engineering', 'Mechatronics', 'Advanced Manufacturing'],
            'Electrical' => ['Power Electronics', 'Renewable Energy Systems', 'Smart Grid', 'Advanced Control Systems'],
            'Civil' => ['Advanced Structural Design', 'Earthquake Engineering', 'Environmental Management', 'Transportation Planning'],
            'Electronics' => ['VLSI Design', 'Wireless Communication', 'Signal Processing', 'IoT Systems']
        ]
    ],
    'medical' => [
        'mbbs' => ['Anatomy', 'Physiology', 'Biochemistry', 'Pathology', 'Pharmacology', 'Microbiology', 'Forensic Medicine', 'Community Medicine'],
        'bds' => ['Dental Anatomy', 'Oral Pathology', 'Periodontics', 'Orthodontics', 'Prosthodontics', 'Oral Surgery'],
        'bams' => ['Ayurvedic Anatomy', 'Ayurvedic Physiology', 'Ayurvedic Pathology', 'Ayurvedic Pharmacology', 'Panchakarma']
    ],
    'management' => [
        'bba' => ['Principles of Management', 'Business Economics', 'Financial Accounting', 'Marketing Management', 'Human Resource Management', 'Business Law'],
        'mba' => ['Strategic Management', 'Financial Management', 'Marketing Management', 'Operations Management', 'Human Resource Management', 'Business Analytics']
    ]
];

// Get search results if form is submitted
$searchResults = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $educationType = $_POST['education_type'] ?? '';
    $level = $_POST['level'] ?? '';
    $stream = $_POST['stream'] ?? '';
    $subject = $_POST['subject'] ?? '';
    
    // Build search query based on selections
    $query = '';
    if (!empty($educationType) && !empty($level) && !empty($subject)) {
        // Add Indian education system specific keywords
        $query = "{$subject} textbook {$level}";
        
        if ($educationType === 'school') {
            $query .= " CBSE NCERT";
        } elseif ($educationType === 'engineering') {
            $query .= " engineering";
        } elseif ($educationType === 'medical') {
            $query .= " medical";
        } elseif ($educationType === 'management') {
            $query .= " management";
        }
        
        // Add rating filter to get top-rated books
        $query .= " rating:4+";
        
        // Search for books
        $searchResults = $googleBooks->searchBooks($query, 12);
    }
}
?>

<main class="pt-20">
    <!-- Find Your Book Section -->
    <section class="py-8 bg-gray-50 dark:bg-gray-800">
        <div class="container mx-auto px-4">
            <h2 class="text-2xl font-bold text-center text-gray-800 dark:text-white mb-8">Find Best Books for Your Course</h2>
            
            <!-- Book Search Form -->
            <div class="max-w-2xl mx-auto bg-white dark:bg-gray-700 rounded-lg shadow-md p-6 mb-8">
                <form method="POST" class="space-y-6">
                    <!-- Education Type Selection -->
                    <div>
                        <label class="block text-gray-700 dark:text-gray-300 mb-2">Select Education Type</label>
                        <select name="education_type" id="educationType" class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-800 dark:text-white" required>
                            <option value="">Select education type</option>
                            <option value="school">School Education</option>
                            <option value="engineering">Engineering</option>
                            <option value="medical">Medical</option>
                            <option value="management">Management</option>
                        </select>
                    </div>

                    <!-- Level Selection -->
                    <div>
                        <label class="block text-gray-700 dark:text-gray-300 mb-2">Select Level</label>
                        <select name="level" id="level" class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-800 dark:text-white" required>
                            <option value="">Select level</option>
                        </select>
                    </div>

                    <!-- Stream Selection (for 11-12 and Engineering) -->
                    <div id="streamContainer" class="hidden">
                        <label class="block text-gray-700 dark:text-gray-300 mb-2">Select Stream</label>
                        <select name="stream" id="stream" class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-800 dark:text-white">
                            <option value="">Select stream</option>
                        </select>
                    </div>

                    <!-- Subject Selection -->
                    <div>
                        <label class="block text-gray-700 dark:text-gray-300 mb-2">Select Subject</label>
                        <select name="subject" id="subject" class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-800 dark:text-white" required>
                            <option value="">Select a subject</option>
                        </select>
                    </div>

                    <button type="submit" class="w-full bg-blue-600 text-white py-3 rounded-lg hover:bg-blue-700 transition duration-200">
                        Find Best Books
                    </button>
                </form>
            </div>

            <!-- Search Results -->
            <?php if ($searchResults && isset($searchResults['items'])): ?>
                <div class="max-w-4xl mx-auto">
                    <h3 class="text-xl font-bold text-gray-800 dark:text-white mb-6">
                        Recommended Books for <?php echo htmlspecialchars(ucfirst($level)); ?> <?php echo htmlspecialchars(ucfirst($subject)); ?>
                    </h3>
                    
                    <div class="grid md:grid-cols-2 gap-6">
                        <?php foreach($searchResults['items'] as $book): ?>
                            <div class="bg-white dark:bg-gray-700 rounded-lg shadow-md overflow-hidden">
                                <div class="p-4">
                                    <?php if(isset($book['volumeInfo']['imageLinks']['thumbnail'])): ?>
                                        <?php 
                                        $imageUrl = str_replace(['http://', '&edge=curl'], ['https://', ''], $book['volumeInfo']['imageLinks']['thumbnail']);
                                        ?>
                                        <div class="aspect-[2/3] bg-white p-2 w-40 mx-auto">
                                            <img src="<?php echo htmlspecialchars($imageUrl); ?>" 
                                                 alt="<?php echo htmlspecialchars($book['volumeInfo']['title']); ?>" 
                                                 class="w-full h-full object-contain rounded-lg">
                                        </div>
                                    <?php else: ?>
                                        <div class="aspect-[2/3] bg-gray-200 dark:bg-gray-600 w-40 mx-auto flex items-center justify-center">
                                            <i class="fas fa-book text-4xl text-gray-400"></i>
                                        </div>
                                    <?php endif; ?>

                                    <div class="mt-4">
                                        <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-2">
                                            <?php echo htmlspecialchars($book['volumeInfo']['title']); ?>
                                        </h3>
                                        
                                        <?php if(isset($book['volumeInfo']['authors'])): ?>
                                            <p class="text-sm text-gray-600 dark:text-gray-300 mb-2">
                                                By <?php echo htmlspecialchars(implode(', ', $book['volumeInfo']['authors'])); ?>
                                            </p>
                                        <?php endif; ?>

                                        <?php if(isset($book['volumeInfo']['averageRating'])): ?>
                                            <div class="flex items-center mb-2">
                                                <div class="flex text-yellow-400">
                                                    <?php for($i = 0; $i < 5; $i++): ?>
                                                        <i class="fas fa-star <?php echo $i < $book['volumeInfo']['averageRating'] ? 'text-yellow-400' : 'text-gray-300'; ?>"></i>
                                                    <?php endfor; ?>
                                                </div>
                                                <span class="ml-2 text-sm text-gray-600 dark:text-gray-300">
                                                    <?php echo number_format($book['volumeInfo']['averageRating'], 1); ?>
                                                </span>
                                            </div>
                                        <?php endif; ?>

                                        <?php if(isset($book['volumeInfo']['description'])): ?>
                                            <p class="text-sm text-gray-600 dark:text-gray-300 mb-4 line-clamp-3">
                                                <?php echo htmlspecialchars(substr(strip_tags($book['volumeInfo']['description']), 0, 150)) . '...'; ?>
                                            </p>
                                        <?php endif; ?>

                                        <div class="flex justify-between mt-4">
                                            <a href="ratings.php?id=<?php echo htmlspecialchars($book['id']); ?>" 
                                               class="text-blue-600 dark:text-blue-400 hover:underline text-sm">
                                                View Details
                                            </a>
                                            <a href="compare.php?book1=<?php echo htmlspecialchars($book['id']); ?>" 
                                               class="text-green-600 dark:text-green-400 hover:underline text-sm">
                                                Compare
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php elseif ($_SERVER['REQUEST_METHOD'] === 'POST'): ?>
                <div class="text-center py-8">
                    <i class="fas fa-search text-4xl text-gray-400 mb-4"></i>
                    <p class="text-gray-600 dark:text-gray-300">No books found for the selected course and subject. Try adjusting your search.</p>
                </div>
            <?php endif; ?>
        </div>
    </section>
</main>

<script>
    const educationLevels = <?php echo json_encode($educationLevels); ?>;
    
    // Function to update level options based on education type
    document.getElementById('educationType').addEventListener('change', function() {
        const levelSelect = document.getElementById('level');
        const streamContainer = document.getElementById('streamContainer');
        const streamSelect = document.getElementById('stream');
        const subjectSelect = document.getElementById('subject');
        
        // Clear existing options
        levelSelect.innerHTML = '<option value="">Select level</option>';
        streamSelect.innerHTML = '<option value="">Select stream</option>';
        subjectSelect.innerHTML = '<option value="">Select subject</option>';
        
        const selectedType = this.value;
        
        if (selectedType === 'school') {
            // Add school levels
            ['1-5', '6-8', '9-10', '11-12'].forEach(level => {
                const option = document.createElement('option');
                option.value = level;
                option.textContent = level === '1-5' ? 'Primary (Classes 1-5)' :
                                   level === '6-8' ? 'Middle (Classes 6-8)' :
                                   level === '9-10' ? 'Secondary (Classes 9-10)' :
                                   'Senior Secondary (Classes 11-12)';
                levelSelect.appendChild(option);
            });
        } else if (selectedType === 'engineering') {
            // Add engineering levels
            ['btech', 'mtech'].forEach(level => {
                const option = document.createElement('option');
                option.value = level;
                option.textContent = level === 'btech' ? 'B.Tech' : 'M.Tech';
                levelSelect.appendChild(option);
            });
        } else if (selectedType === 'medical') {
            // Add medical levels
            ['mbbs', 'bds', 'bams'].forEach(level => {
                const option = document.createElement('option');
                option.value = level;
                option.textContent = level.toUpperCase();
                levelSelect.appendChild(option);
            });
        } else if (selectedType === 'management') {
            // Add management levels
            ['bba', 'mba'].forEach(level => {
                const option = document.createElement('option');
                option.value = level;
                option.textContent = level.toUpperCase();
                levelSelect.appendChild(option);
            });
        }
    });
    
    // Function to update stream options based on level
    document.getElementById('level').addEventListener('change', function() {
        const educationType = document.getElementById('educationType').value;
        const level = this.value;
        const streamContainer = document.getElementById('streamContainer');
        const streamSelect = document.getElementById('stream');
        const subjectSelect = document.getElementById('subject');
        
        // Clear existing options
        streamSelect.innerHTML = '<option value="">Select stream</option>';
        subjectSelect.innerHTML = '<option value="">Select subject</option>';
        
        if (educationType === 'school' && level === '11-12') {
            streamContainer.classList.remove('hidden');
            ['science', 'commerce', 'humanities'].forEach(stream => {
                const option = document.createElement('option');
                option.value = stream;
                option.textContent = stream.charAt(0).toUpperCase() + stream.slice(1);
                streamSelect.appendChild(option);
            });
        } else if (educationType === 'engineering') {
            streamContainer.classList.remove('hidden');
            Object.keys(educationLevels.engineering[level]).forEach(stream => {
                const option = document.createElement('option');
                option.value = stream;
                option.textContent = stream;
                streamSelect.appendChild(option);
            });
        } else {
            streamContainer.classList.add('hidden');
            updateSubjects();
        }
    });
    
    // Function to update subject options based on stream
    document.getElementById('stream').addEventListener('change', function() {
        updateSubjects();
    });
    
    function updateSubjects() {
        const educationType = document.getElementById('educationType').value;
        const level = document.getElementById('level').value;
        const stream = document.getElementById('stream').value;
        const subjectSelect = document.getElementById('subject');
        
        // Clear existing options
        subjectSelect.innerHTML = '<option value="">Select subject</option>';
        
        if (educationType === 'school') {
            if (level === '11-12') {
                educationLevels.school['11-12'][stream].forEach(subject => {
                    const option = document.createElement('option');
                    option.value = subject;
                    option.textContent = subject.charAt(0).toUpperCase() + subject.slice(1);
                    subjectSelect.appendChild(option);
                });
            } else {
                educationLevels.school[level].forEach(subject => {
                    const option = document.createElement('option');
                    option.value = subject;
                    option.textContent = subject.charAt(0).toUpperCase() + subject.slice(1);
                    subjectSelect.appendChild(option);
                });
            }
        } else if (educationType === 'engineering') {
            educationLevels.engineering[level][stream].forEach(subject => {
                const option = document.createElement('option');
                option.value = subject;
                option.textContent = subject;
                subjectSelect.appendChild(option);
            });
        } else if (educationType === 'medical') {
            educationLevels.medical[level].forEach(subject => {
                const option = document.createElement('option');
                option.value = subject;
                option.textContent = subject;
                subjectSelect.appendChild(option);
            });
        } else if (educationType === 'management') {
            educationLevels.management[level].forEach(subject => {
                const option = document.createElement('option');
                option.value = subject;
                option.textContent = subject;
                subjectSelect.appendChild(option);
            });
        }
    }
</script>

<?php require_once 'includes/footer.php'; ?> 