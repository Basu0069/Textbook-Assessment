-- SQLite does not use databases (it's one file), so no CREATE/USE DATABASE

-- Users table
CREATE TABLE IF NOT EXISTS users (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL,
    email TEXT NOT NULL UNIQUE,
    password TEXT NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Books table
CREATE TABLE IF NOT EXISTS books (
    book_id INTEGER PRIMARY KEY AUTOINCREMENT,
    title TEXT NOT NULL,
    author TEXT NOT NULL,
    publisher TEXT,
    publication_year INTEGER,
    isbn TEXT,
    description TEXT,
    cover_image_url TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Assessments table
CREATE TABLE IF NOT EXISTS assessments (
    assessment_id INTEGER PRIMARY KEY AUTOINCREMENT,
    book_id INTEGER NOT NULL,
    user_id INTEGER NOT NULL,
    overall_score REAL,
    comments TEXT,
    status TEXT DEFAULT 'draft',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (book_id) REFERENCES books(book_id),
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Criteria table
CREATE TABLE IF NOT EXISTS criteria (
    criterion_id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL,
    description TEXT,
    category TEXT NOT NULL CHECK(category IN ('content', 'design', 'pedagogy', 'technical')),
    weight REAL DEFAULT 1.00
);

-- Assessment scores table
CREATE TABLE IF NOT EXISTS assessment_scores (
    score_id INTEGER PRIMARY KEY AUTOINCREMENT,
    assessment_id INTEGER NOT NULL,
    criterion_id INTEGER NOT NULL,
    score REAL NOT NULL,
    comments TEXT,
    FOREIGN KEY (assessment_id) REFERENCES assessments(assessment_id),
    FOREIGN KEY (criterion_id) REFERENCES criteria(criterion_id)
);