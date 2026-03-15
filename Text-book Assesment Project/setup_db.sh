#!/bash/bin

# Textbook Assessment Project Setup Script
# This script helps install dependencies and setup the database on Debian-based systems.

echo "--- Textbook Assessment Project Setup ---"

# 1. Update package list
echo "Updating package list..."
sudo apt update

# 2. Install MySQL Server and PHP MySQL driver
echo "Installing MySQL Server and PHP MySQL driver..."
sudo apt install -y mysql-server php-mysql

# 3. Start MySQL service
echo "Starting MySQL service..."
sudo systemctl start mysql
sudo systemctl enable mysql

# 4. Check MySQL status
echo "Checking MySQL status..."
sudo systemctl status mysql --no-pager

# 5. Create database and tables
echo "Setting up database structure..."
# Note: This assumes 'root' user with 'Witch@69' password as per config.php
# If your root user has a different setup, you might need to adjust this.
mysql -u root -p'Witch@69' -e "CREATE DATABASE IF NOT EXISTS textbook_assessment;" 2>/dev/null || echo "Could not create database automatically. Please ensure MySQL is accessible."

if [ -f "database.sql" ]; then
    echo "Importing database.sql..."
    mysql -u root -p'Witch@69' textbook_assessment < database.sql 2>/dev/null
fi

if [ -f "create_reviews_table.sql" ]; then
    echo "Importing create_reviews_table.sql..."
    mysql -u root -p'Witch@69' textbook_assessment < create_reviews_table.sql 2>/dev/null
fi

echo "--- Setup Completed ---"
echo "You can now run 'php -S localhost:8000' and visit http://localhost:8000/test-db.php to verify."
