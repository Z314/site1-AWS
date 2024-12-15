<?php

// Retrieve the database credentials from environment variables
$host = getenv('DB_HOST');
$user = getenv('DB_USER');
$password = getenv('DB_PASSWORD');
$database = getenv('DB_NAME');

// Connect to the database using PDO
try {
	$conn = new PDO("mysql:host=$host;dbname=$database", $user, $password);
	echo "Connected to the database successfully!";
} catch (PDOException $e) {
	$error_message = $e->getMessage();
	include('../view/database_error.php');
	exit();
}
?>
