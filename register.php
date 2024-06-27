<?php
session_start();

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "share";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Check if username already exists
    $checkQuery = "SELECT * FROM users WHERE username = '$username'";
    $checkResult = $conn->query($checkQuery);
    if ($checkResult->num_rows > 0) {
        echo "Username already exists.";
    } else {
        // Insert user into 'users' table
        $insertQuery = "INSERT INTO users (username, password) VALUES ('$username', '$password')";
        if ($conn->query($insertQuery) === TRUE) {
            // Create a table for the user to store files
            $createTableQuery = "CREATE TABLE IF NOT EXISTS user_$username (
                id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                file_name VARCHAR(255) NOT NULL,
                file_url VARCHAR(255) NOT NULL
            )";
            if ($conn->query($createTableQuery) === TRUE) {
                echo "Registration successful.";
            } else {
                echo "Error creating user table: " . $conn->error;
            }
        } else {
            echo "Error inserting user: " . $conn->error;
        }
    }
} else {
    echo "Invalid request method.";
}

$conn->close();
?>
