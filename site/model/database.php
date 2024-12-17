<?php

// Include the AWS SDK for PHP
require 'vendor/autoload.php';

use Aws\SecretsManager\SecretsManagerClient;
use Aws\Exception\AwsException;

// Retrieve the secret name from your environment or configuration
$secret_name = "secret3"; // Replace with your actual secret name

// Create a Secrets Manager client
$client = new SecretsManagerClient([
    'region' => 'ap-southeast-2',  // Adjust the region if needed
    'version' => 'latest'
]);

try {
    // Retrieve the secret value from Secrets Manager
    $result = $client->getSecretValue([
        'SecretId' => $secret_name,
        'VersionStage' => 'AWSCURRENT' // Defaults to AWSCURRENT if unspecified
    ]);

    // Decode the secret
    if (isset($result['SecretString'])) {
        $secret = json_decode($result['SecretString'], true);  // Decode the secret into an associative array
    } else {
        // If binary data is returned (rare for most cases)
        $secret = json_decode(base64_decode($result['SecretBinary']), true);
    }

    // Extract the database credentials from the secret
    $host = $secret['host'];
    $user = $secret['username'];
    $password = $secret['password'];
    $database = $secret['dbname'];

    // Connect to the database using PDO
    try {
        $conn = new PDO("mysql:host=$host;dbname=$database", $user, $password);
        echo "Connected to the database successfully!";
    } catch (PDOException $e) {
        $error_message = $e->getMessage();
        include('../view/database_error.php');
        exit();
    }

} catch (AwsException $e) {
    // Output error message if failed to retrieve the secret
    echo "Error retrieving secret: " . $e->getMessage() . PHP_EOL;
    include('../view/database_error.php');
    exit();
}

?>
