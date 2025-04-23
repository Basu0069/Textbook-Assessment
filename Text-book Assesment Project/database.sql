-- Create database
CREATE DATABASE IF NOT EXISTS textbook_assessment;
USE textbook_assessment;

-- Users table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Books table
CREATE TABLE books (
    book_id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(255) NOT NULL,
    author VARCHAR(100) NOT NULL,
    publisher VARCHAR(100),
    publication_year INT,
    isbn VARCHAR(20),
    description TEXT,
    cover_image_url VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Assessments table
CREATE TABLE assessments (
    assessment_id INT PRIMARY KEY AUTO_INCREMENT,
    book_id INT NOT NULL,
    user_id INT NOT NULL,
    overall_score DECIMAL(5,2),
    comments TEXT,
    status ENUM('draft', 'completed', 'reviewed') DEFAULT 'draft',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (book_id) REFERENCES books(book_id),
    FOREIGN KEY (user_id) REFERENCES users(id) -- ✅ FIXED THIS LINE
);


-- Criteria table
CREATE TABLE criteria (
    criterion_id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    category ENUM('content', 'design', 'pedagogy', 'technical') NOT NULL,
    weight DECIMAL(5,2) DEFAULT 1.00
);

-- Assessment scores table
CREATE TABLE assessment_scores (
    score_id INT PRIMARY KEY AUTO_INCREMENT,
    assessment_id INT NOT NULL,
    criterion_id INT NOT NULL,
    score DECIMAL(5,2) NOT NULL,
    comments TEXT,
    FOREIGN KEY (assessment_id) REFERENCES assessments(assessment_id),
    FOREIGN KEY (criterion_id) REFERENCES criteria(criterion_id)
); 