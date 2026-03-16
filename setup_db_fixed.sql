-- Create database if not exists
CREATE DATABASE IF NOT EXISTS textbook_assessment;

-- Create dedicated user
CREATE USER IF NOT EXISTS 'textbook_user'@'localhost' IDENTIFIED BY 'Witch@69';

-- Grant privileges
GRANT ALL PRIVILEGES ON textbook_assessment.* TO 'textbook_user'@'localhost';

-- Apply changes
FLUSH PRIVILEGES;

-- Use the database
USE textbook_assessment;

-- Import tables (as a fallback in case setup_db.sh failed)
SOURCE database.sql;
SOURCE create_reviews_table.sql;
