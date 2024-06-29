<?php
session_start();

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
    // Sanitize user input
    $username = htmlspecialchars($_POST['username']);
    $password = htmlspecialchars($_POST['password']);

    // Check if username already exists
    $checkQuery = $conn->prepare("SELECT * FROM users WHERE username = ?");
    $checkQuery->bind_param("s", $username);
    $checkQuery->execute();
    $checkResult = $checkQuery->get_result();

    if ($checkResult->num_rows > 0) {
        echo "<script>alert('Username already exists.'); window.location.href='register.html';</script>";
    } else {
        // Insert user into 'users' table with password hashing
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $insertQuery = $conn->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
        $insertQuery->bind_param("ss", $username, $hashedPassword);

        if ($insertQuery->execute() === TRUE) {
            // Create a table for the user to store files
            $createTableQuery = "CREATE TABLE IF NOT EXISTS user_$username (
                id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                file_name VARCHAR(255) NOT NULL,
                file_url VARCHAR(255) NOT NULL
            )";
            if ($conn->query($createTableQuery) === TRUE) {
                echo "<script>alert('Registration successful.'); window.location.href='login.html';</script>";
            } else {
                echo "Error creating user table: " . $conn->error;
            }
        } else {
            echo "Error inserting user: " . $conn->error;
        }
    }

    $checkQuery->close();
    $insertQuery->close();
} else {
    echo "Invalid request method.";
}

$conn->close();
?>
