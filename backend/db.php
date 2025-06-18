<?php
// db.php - Database connection setup

// --- Database Configuration ---
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');
define('DB_NAME', 'grocery_store');

// --- Establish Connection ---
$conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

// Check connection
if ($conn->connect_error) {

    header('Content-Type: application/json');
    http_response_code(500); // Internal Server Error
    echo json_encode(['status' => 'error', 'message' => 'Database connection failed: ' . $conn->connect_error]);
    exit(); // Stop script execution
}

// Set character set to UTF8mb4 for proper encoding support
$conn->set_charset("utf8mb4");
?>