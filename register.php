<?php
session_start();

header("Content-Security-Policy: default-src 'self'; script-src 'self' https://cdnjs.cloudflare.com; style-src 'self' https://fonts.googleapis.com; font-src 'self' https://fonts.gstatic.com;");

$servername = "localhost";
$username = "root";
$password = "Keshav@123";
$dbname = "photo_sharing_app";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Validate and sanitize user input
    $username = htmlspecialchars($username);
    $password = htmlspecialchars($password);

    // Check if username already exists
    $checkQuery = $conn->prepare("SELECT username FROM users WHERE username = ?");
    $checkQuery->bind_param("s", $username);
    $checkQuery->execute();
    $checkResult = $checkQuery->get_result();

    if ($checkResult->num_rows > 0) {
        // Username already exists
       echo "<script src='redirect.js'></script>";
        exit(); // Stop further execution
    } else {
        // Insert user into 'users' table
        $insertQuery = $conn->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
        $insertQuery->bind_param("ss", $username, $password);
        if ($insertQuery->execute() === TRUE) {
            // Create a table for the user to store files
            $createTableQuery = $conn->prepare("CREATE TABLE IF NOT EXISTS user_$username (
                id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                file_name VARCHAR(255) NOT NULL,
                file_url VARCHAR(255) NOT NULL
            )");

            if ($createTableQuery->execute() === TRUE) {
                echo "<script src='loginre.js'></script>";
                exit(); // Stop further execution
            } else {
                echo "Error creating user table: " . $conn->error;
            }
        } else {
            echo "Error inserting user: " . $conn->error;
        }
    }

    $checkQuery->close();
    $insertQuery->close();
    $createTableQuery->close();
} else {
    echo "Invalid request method.";
}

$conn->close();
?>
