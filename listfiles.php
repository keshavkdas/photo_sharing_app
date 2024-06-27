<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

require 'vendor/autoload.php';

use Aws\S3\S3Client;
use Aws\Exception\AwsException;
use Aws\SecretsManager\SecretsManagerClient;

$bucketName = 'keshavshare';
function getAWSCredentials()
{
    $secretName = "s3bucket-cred"; // Replace with your secret name in AWS Secr>
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
        // Output error message
        error_log('Error retrieving AWS credentials from Secrets Manager: ' . $>
        return null;
    }
}

// Initialize S3 client with retrieved credentials
$credentials = getAWSCredentials();

// Initialize S3 client
$s3 = new S3Client([
    'credentials' => [
        'key'    => $credentials['key'],
        'secret' => $credentials['secret'],
    ],
    'version' => 'latest',
    'region' => 'ap-south-1',
]);

$username = $_SESSION['username'];
$folderName = "user_$username/";

try {
    // List objects in the specified S3 bucket and user folder
    $result = $s3->listObjectsV2([
        'Bucket' => $bucketName,
        'Prefix' => $folderName,
    ]);

    $files = [];
    if (isset($result['Contents'])) {
        foreach ($result['Contents'] as $object) {
            $fileKey = $object['Key'];
            $presignedUrl = (string) $s3->createPresignedRequest(
                $s3->getCommand('GetObject', [
                    'Bucket' => $bucketName,
                    'Key' => $fileKey,
                ]),
                '+15 minutes' // Expiration time for the pre-signed URL (e.g., 15 minutes)
            )->getUri();

            $files[] = [
                'name' => str_replace($folderName, '', $fileKey),
                'presignedUrl' => $presignedUrl,
            ];
        }
    }

    // Set response content type to JSON
    header('Content-Type: application/json');
    // Send the files array as JSON response
    echo json_encode($files);
} catch (AwsException $e) {
    // Handle AWS exceptions and return an error response
    error_log('Error listing files: ' . $e->getMessage());
    header('HTTP/1.1 500 Internal Server Error');
    echo json_encode(['error' => 'Error listing files: ' . $e->getMessage()]);
}
?>
