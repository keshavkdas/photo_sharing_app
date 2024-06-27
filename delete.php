<?php
require 'vendor/autoload.php';

use Aws\S3\S3Client;
use Aws\Exception\AwsException;

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    if (isset($data['fileName'])) {
        $bucketName = 'keshavshare';
        $keyName = 'user_$usename/' . $data['fileName']; 

        $s3 = new S3Client([
            'version' => 'latest',
            'region'  => 'ap-south-1',
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
