<?php
session_start();

$servername = "localhost";
$username = "root"; // Database username
$password = "Keshav@123"; // Database password
$dbname = "photo_sharing_app"; // Database name

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} else {
    // Debugging
    echo "Database connected successfully<br>";
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user = $_POST['username'];
    $pass = $_POST['password'];

    // Validate user input
    $user = htmlspecialchars($user);
    $pass = htmlspecialchars($pass);

    // Debugging
    echo "Username: " . $user . "<br>";
    echo "Password: " . $pass . "<br>";

    // Prepare and bind
    $stmt = $conn->prepare("SELECT id, username, password FROM users WHERE username = ?");
    $stmt->bind_param("s", $user);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        // Debugging
        echo "Fetched Username: " . $row['username'] . "<br>";
        echo "Fetched Password Hash: " . $row['password'] . "<br>";

        if (($pass==$row['password'])) {
            // Password is correct, set session variables
            $_SESSION['id'] = $row['id'];
            $_SESSION['username'] = $row['username'];
            $_SESSION['loggedIn'] = 'true';
            print_r($_SESSION['loggedIn']);
            // Redirect to the upload page or any other page after successful login
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

    // Close the statement and connection
    $stmt->close();
    $conn->close();
}
?>
