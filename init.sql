-- Create a database
CREATE DATABASE IF NOT EXISTS photo_sharing_app;

-- Use the database
USE photo_sharing_app;

-- Create a table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL,
    password VARCHAR(100) NOT NULL
);

-- Insert some initial data
INSERT INTO users (username, email) VALUES
('keshav', '1234');
