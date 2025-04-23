CREATE TABLE IF NOT EXISTS reviews (
    id INT AUTO_INCREMENT PRIMARY KEY,
    book_id VARCHAR(255) NOT NULL,
    username VARCHAR(255) NOT NULL,
    content_quality INT NOT NULL CHECK (content_quality BETWEEN 1 AND 5),
    explanation_clarity INT NOT NULL CHECK (explanation_clarity BETWEEN 1 AND 5),
    examples_quality INT NOT NULL CHECK (examples_quality BETWEEN 1 AND 5),
    exercises_quality INT NOT NULL CHECK (exercises_quality BETWEEN 1 AND 5),
    language_clarity INT NOT NULL CHECK (language_clarity BETWEEN 1 AND 5),
    average_rating DECIMAL(3,2) NOT NULL,
    comments TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_book_id (book_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci; 




ALTER TABLE reviews 
MODIFY COLUMN book_id INT NOT NULL,
ADD COLUMN user_id INT NOT NULL AFTER id,
ADD FOREIGN KEY (book_id) REFERENCES books(book_id),
ADD FOREIGN KEY (user_id) REFERENCES users(id),
DROP COLUMN username;