<?php
session_start();

header("Content-Security-Policy: default-src 'self'; script-src 'self' https://cdnjs.cloudflare.com; style-src 'self' https://fonts.googleapis.com; font-src 'self' https://fonts.gstatic.com;");

$servername = "localhost";
$username = "root"; // Database username
$password = "Keshav@123"; // Database password
$dbname = "photo_sharing_app"; // Database name

$conn = new mysqli($servername, $username, $password, $dbname);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user = $_POST['username'];
    $pass = $_POST['password'];

    // Validate user input
    $user = htmlspecialchars($user);
    $pass = htmlspecialchars($pass);

    // Prepare and bind
    $stmt = $conn->prepare("SELECT id, username, password FROM users WHERE username = ?");
    $stmt->bind_param("s", $user);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();

        if ($pass == $row['password']) {
            // Password is correct, set session variables
            $_SESSION['id'] = $row['id'];
            $_SESSION['username'] = $row['username'];
            $_SESSION['loggedIn'] = 'true';
            // Redirect to the upload page or any other page after successful login
            header("Location: upload.html");
            exit();
        } else {
            $_SESSION['error'] = "Invalid username or password.";
            header("Location: login.html");
            exit();
        }
    } else {
        $_SESSION['error'] = "Invalid username or password.";
        header("Location: login.html");
        exit();
    }

    // Close the statement and connection
    $stmt->close();
    $conn->close();
}
?>
