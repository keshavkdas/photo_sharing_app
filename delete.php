<?php
require 'vendor/autoload.php';

use Aws\S3\S3Client;
use Aws\Exception\AwsException;
use Aws\SecretsManager\SecretsManagerClient;

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

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    if (isset($data['fileName'])) {
        $bucketName = 'keshavshare';
        if (isset($_SESSION['username'])) {
                $username = $_SESSION['username'];
        } else {
                throw new Exception("User is not logged in.");
        }
        $keyName = "user_$username/" . $data['fileName'];
 

        $s3 = new S3Client([
            'version' => 'latest',
            'region'  => 'ap-south-1',
            'credentials' => [
                'key' => $credentials['key'],
                'secret' => $credentials['secret'],
            ],

        ]);

        try {
            $result = $s3->deleteObject([
                'Bucket' => $bucketName,
                'Key'    => $keyName,
            ]);

            echo json_encode(['message' => 'File deleted successfully']);
        } catch (AwsException $e) {
            echo json_encode(['message' => 'Error deleting file: ' . $e->getMessage()]);
        }
    } else {
        echo json_encode(['message' => 'Invalid request']);
    }
} else {
    echo json_encode(['message' => 'Invalid request method']);
}
?>
