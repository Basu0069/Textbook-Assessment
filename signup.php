<?php
require_once 'includes/header.php';
require_once 'includes/db.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

    // Debug: Print received form data
    error_log("Signup attempt - Name: $name, Email: $email");

    if (empty($name) || empty($email) || empty($password) || empty($confirmPassword)) {
        $error = 'Please fill in all fields';
    } elseif ($password !== $confirmPassword) {
        $error = 'Passwords do not match';
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters long';
    } else {
        try {
            // Verify database connection
            if (!$pdo) {
                throw new Exception("Database connection failed");
            }

            // Check if email already exists
            $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->execute([$email]);
            if ($stmt->fetch()) {
                $error = 'Email already registered';
            } else {
                // Create new user
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                
                // Debug: Print SQL and parameters
                error_log("Attempting to insert user - Name: $name, Email: $email");
                
                $stmt = $pdo->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
                $result = $stmt->execute([$name, $email, $hashedPassword]);
                
                if (!$result) {
                    throw new Exception("Failed to insert user data");
                }
                
                // Get the newly created user's ID
                $userId = $pdo->lastInsertId();
                error_log("User created successfully with ID: $userId");
                
                // Verify the user was created
                $verifyStmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
                $verifyStmt->execute([$userId]);
                $user = $verifyStmt->fetch();
                
                if (!$user) {
                    throw new Exception("User creation verification failed");
                }
                
                // Set session variables
                $_SESSION['user_id'] = $userId;
                $_SESSION['user_name'] = $name;
                
                error_log("Session variables set - User ID: $userId, Name: $name");
                
                // Redirect to home page
                header('Location: index.php');
                exit;
            }
        } catch (PDOException $e) {
            error_log("Database error during signup: " . $e->getMessage());
            $error = 'Database error: ' . $e->getMessage();
        } catch (Exception $e) {
            error_log("Error during signup: " . $e->getMessage());
            $error = 'Error: ' . $e->getMessage();
        }
    }
}

// Display any errors
if ($error) {
    echo '<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
            <strong class="font-bold">Error!</strong>
            <span class="block sm:inline">' . htmlspecialchars($error) . '</span>
          </div>';
}
?>

<main class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
        <div>
            <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900 dark:text-white">
                Create your account
            </h2>
        </div>
        
        <?php if ($error): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                <span class="block sm:inline"><?php echo htmlspecialchars($error); ?></span>
            </div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                <span class="block sm:inline"><?php echo htmlspecialchars($success); ?></span>
            </div>
        <?php endif; ?>

        <form class="mt-8 space-y-6" method="POST">
            <div class="rounded-md shadow-sm -space-y-px">
                <div>
                    <label for="name" class="sr-only">Full Name</label>
                    <input id="name" name="name" type="text" required 
                           class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 
                                  placeholder-gray-500 dark:placeholder-gray-400 text-gray-900 dark:text-white rounded-t-md 
                                  focus:outline-none focus:ring-blue-500 focus:border-blue-500 focus:z-10 sm:text-sm 
                                  bg-white dark:bg-gray-700"
                           placeholder="Full Name">
                </div>
                <div>
                    <label for="email" class="sr-only">Email address</label>
                    <input id="email" name="email" type="email" required 
                           class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 
                                  placeholder-gray-500 dark:placeholder-gray-400 text-gray-900 dark:text-white 
                                  focus:outline-none focus:ring-blue-500 focus:border-blue-500 focus:z-10 sm:text-sm 
                                  bg-white dark:bg-gray-700"
                           placeholder="Email address">
                </div>
                <div>
                    <label for="password" class="sr-only">Password</label>
                    <input id="password" name="password" type="password" required 
                           class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 
                                  placeholder-gray-500 dark:placeholder-gray-400 text-gray-900 dark:text-white 
                                  focus:outline-none focus:ring-blue-500 focus:border-blue-500 focus:z-10 sm:text-sm 
                                  bg-white dark:bg-gray-700"
                           placeholder="Password">
                </div>
                <div>
                    <label for="confirm_password" class="sr-only">Confirm Password</label>
                    <input id="confirm_password" name="confirm_password" type="password" required 
                           class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 
                                  placeholder-gray-500 dark:placeholder-gray-400 text-gray-900 dark:text-white rounded-b-md 
                                  focus:outline-none focus:ring-blue-500 focus:border-blue-500 focus:z-10 sm:text-sm 
                                  bg-white dark:bg-gray-700"
                           placeholder="Confirm Password">
                </div>
            </div>

            <div>
                <button type="submit" 
                        class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium 
                               rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 
                               focus:ring-offset-2 focus:ring-blue-500">
                    Sign up
                </button>
            </div>
            
            <div class="text-center">
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    Already have an account? 
                    <a href="login.php" class="font-medium text-blue-600 hover:text-blue-500 dark:text-blue-400">
                        Sign in
                    </a>
                </p>
            </div>
        </form>
    </div>
</main>

<?php require_once 'includes/footer.php'; ?> 