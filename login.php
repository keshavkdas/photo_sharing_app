<?php
session_start();

$servername = "localhost";
$username = "root"; // Database username
$password = "Keshav@123"; // Database password
$dbname = "photo_sharing_app"; // Database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Debugging - Connection successful
echo "Database connected successfully<br>";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize user input
    $user = htmlspecialchars($_POST['username']);
    $pass = htmlspecialchars($_POST['password']);

    // Prepare and bind SQL statement
    $stmt = $conn->prepare("SELECT id, username, password FROM users WHERE username = ?");
    $stmt->bind_param("s", $user);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        
        // Debugging - Fetched data
        echo "Fetched Username: " . $row['username'] . "<br>";
        echo "Fetched Password Hash: " . $row['password'] . "<br>";

        // Verify password
        if (password_verify($pass, $row['password'])) {
            // Password is correct, set session variables
            $_SESSION['id'] = $row['id'];
            $_SESSION['username'] = $row['username'];
            $_SESSION['loggedIn'] = true; // Use boolean true for session variable

            // Debugging - Session variable
            echo "Logged in: " . $_SESSION['loggedIn'] . "<br>";

            // Redirect to the upload page after successful login
            header("Location: upload.html");
            exit();
        } else {
            // Invalid password
            echo "Invalid username or password.";
        }
    } else {
        // Invalid username
        echo "Invalid username or password.";
    }

    // Close statement
    $stmt->close();
}

// Close connection
$conn->close();
?>
