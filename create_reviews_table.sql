CREATE TABLE IF NOT EXISTS reviews (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    book_id INTEGER NOT NULL,
    user_id INTEGER NOT NULL,
    content_quality INTEGER NOT NULL CHECK (content_quality BETWEEN 1 AND 5),
    explanation_clarity INTEGER NOT NULL CHECK (explanation_clarity BETWEEN 1 AND 5),
    examples_quality INTEGER NOT NULL CHECK (examples_quality BETWEEN 1 AND 5),
    exercises_quality INTEGER NOT NULL CHECK (exercises_quality BETWEEN 1 AND 5),
    language_clarity INTEGER NOT NULL CHECK (language_clarity BETWEEN 1 AND 5),
    average_rating REAL NOT NULL,
    comments TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (book_id) REFERENCES books(book_id),
    FOREIGN KEY (user_id) REFERENCES users(id)
);

CREATE INDEX IF NOT EXISTS idx_book_id ON reviews(book_id);