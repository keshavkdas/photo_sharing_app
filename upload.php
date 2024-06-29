<?php
session_start();
$_SESSION['loggedIn'] = true;

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

require '/var/www/html/vendor/autoload.php';

use Aws\S3\S3Client;
use Aws\Exception\AwsException;
use Aws\SecretsManager\SecretsManagerClient;

$bucketName = 'keshavshare';
$region = 'ap-south-1';

function getAWSCredentials()
{
    $secretName = "s3bucket-cred"; // Replace with your secret name in AWS Secrets Manager
    $region = "ap-south-1"; // Replace with your preferred AWS region

    // Create a Secrets Manager client with default credentials
    $secretsManagerClient = new SecretsManagerClient([
        'version' => 'latest',
        'region' => $region,
    ]);

    try {
        // Retrieve secret value from Secrets Manager
        $result = $secretsManagerClient->getSecretValue([
            'SecretId' => $secretName,
        ]);

        // Parse and extract the secret values
        $secret = json_decode($result['SecretString'], true);
        return [
            'key' => $secret['AccessKeyId'],
            'secret' => $secret['SecretAccessKey'],
        ];

    } catch (AwsException $e) {
        // Output error message to error log
        error_log('Error retrieving AWS credentials from Secrets Manager: ' . $e->getMessage());
        return null;
    }
}

// Initialize S3 client with retrieved credentials
$credentials = getAWSCredentials();

$conn = new mysqli("localhost", "root", "Keshav@123", "photo_sharing_app");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_SESSION['username'])) {
    $username = $_SESSION['username'];
    $fileName = $_FILES['file']['name'];
    $fileTmpName = $_FILES['file']['tmp_name'];
    
    // Validate and sanitize user inputs if necessary
    $fileName = htmlspecialchars($fileName);
    
    // File type validation
    $allowedTypes = ['image/jpeg', 'image/png', 'application/pdf'];
    $fileType = mime_content_type($fileTmpName);
    if (!in_array($fileType, $allowedTypes)) {
        die("File type not allowed.");
    }

    // File size validation
    $maxFileSize = 2 * 1024 * 1024; // 2MB
    if ($_FILES['file']['size'] > $maxFileSize) {
        die("File is too large.");
    }

    // Create S3 client
    $s3 = new S3Client([
        'credentials' => [
            'key'    => $credentials['key'],
            'secret' => $credentials['secret'],
        ],
        'version' => 'latest',
        'region' => $region,
    ]);

    try {
        // Create folder in S3 bucket with user's username
        $folderName = "user_$username/";
        $fileKey = $folderName . $fileName;

        // Upload file to S3 bucket
        $result = $s3->putObject([
            'Bucket' => $bucketName,
            'Key' => $fileKey,
            'Body' => fopen($fileTmpName, 'rb'),
            'ServerSideEncryption' => 'AES256', // Server-side encryption using S3-managed keys
        ]);

        $objectUrl = $result['ObjectURL'];

        // Dynamic table name based on username
        $userTableName = "user_$username";

        // Use prepared statement to insert file details into database
        $stmt = $conn->prepare("INSERT INTO $userTableName (file_name, file_url) VALUES (?, ?)");
        $stmt->bind_param("ss", $fileName, $objectUrl);

        if ($stmt->execute()) {
            echo "File uploaded successfully.";
            header("Location: listfiles.html");
            exit();
        } else {
            echo "Error inserting file: " . $stmt->error;
        }

        $stmt->close();
    } catch (AwsException $e) {
        echo "Error uploading file to S3: " . $e->getMessage();
    }
} else {
    echo "Invalid request method or user not logged in.";
}

$conn->close();
?>
