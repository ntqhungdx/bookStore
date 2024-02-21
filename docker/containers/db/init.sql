CREATE DATABASE IF NOT EXISTS testing CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci;

-- Create a testing user with privileges on the testing database
CREATE USER IF NOT EXISTS 'testuser'@'%' IDENTIFIED BY 'testpassword';
GRANT ALL PRIVILEGES ON testing.* TO 'testuser'@'%';
FLUSH PRIVILEGES;
