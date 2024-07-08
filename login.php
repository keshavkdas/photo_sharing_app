<?php
require 'vendor/autoload.php';

use Aws\SecretsManager\SecretsManagerClient; 
use Aws\Exception\AwsException;

session_start();

//header("Content-Security-Policy: default-src 'self'; script-src 'self' https://cdnjs.cloudflare.com; style-src 'self' https://fonts.googleapis.com; font-src 'self' https://fonts.gstatic.com;");

$servername = "localhost";
$username = "root"; // Database username
$password = "Keshav@123"; // Database password
$dbname = "photo_sharing_app"; // Database name

$conn = new mysqli($servername, $username, $password, $dbname);

// Function to get secret from AWS Secrets Manager
function getSecret($secretName) {
    $client = new SecretsManagerClient([
        'version' => 'latest',
        'region' => 'ap-south-1' 
    ]);

    try {
        $result = $client->getSecretValue([
            'SecretId' => $secretName,
        ]);

        if (isset($result['SecretString'])) {
            return json_decode($result['SecretString'], true);
        }
    } catch (AwsException $e) {
        error_log($e->getMessage());
    }

    return null;
}

// Retrieve reCAPTCHA keys from AWS Secrets Manager
$secret = getSecret('captcha'); 
$siteKey = $secret['site_key'];
$secretKey = $secret['secret_key'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user = $_POST['username'];
    $pass = $_POST['password'];
    $recaptchaResponse = $_POST['g-recaptcha-response'];

    // Validate user input
    $user = htmlspecialchars($user);
    $pass = htmlspecialchars($pass);

    // Verify reCAPTCHA
    $response = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=$secretKey&response=$recaptchaResponse");
    $responseKeys = json_decode($response, true);

    if (intval($responseKeys["success"]) !== 1) {
        $error = "Please complete the CAPTCHA.";
        header("Location: login.html");
        exit();
    }

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
            $error = "Incorrect password.";
            header("Location: login.html");
            exit();
        }
    } else {
        $error = "Username does not exist.";
        header("Location: login.html");
        exit();
    }

    // Close the statement and connection
    $stmt->close();
    $conn->close();
}
?>
