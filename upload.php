<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

require '/var/www/html/photo-sharing-app/vendor/autoload.php';

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
        ]);

        $objectUrl = $result['ObjectURL'];

        // Dynamic table name based on username
        $userTableName = "user_files";

        // Use prepared statement to insert file details into database
        $insertQuery = $conn->prepare("INSERT INTO $userTableName (username, file_name, file_url) VALUES (?, ?, ?)");
        $insertQuery->bind_param("sss", $username, $fileName, $objectUrl);

        if ($insertQuery->execute()) {
            echo "File uploaded successfully.";
            header("Location: listfiles.html");
            exit();
        } else {
            echo "Error inserting file: " . $conn->error;
        }

        $insertQuery->close();
    } catch (AwsException $e) {
        echo "Error uploading file to S3: " . $e->getMessage();
    }
} else {
    echo "Invalid request method or user not logged in.";
}

$conn->close();
?>
