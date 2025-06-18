<?php
// register.php - Handles user registration

// Include database connection
require_once 'db.php';

// Set header to return JSON response
header('Content-Type: application/json');

// Start session to potentially store messages
session_start();

// --- Input Validation and Processing ---
$response = ['status' => 'error', 'message' => 'An unknown error occurred.'];

// Only process POST requests
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get data from request body (assuming JSON is sent by React)
    $data = json_decode(file_get_contents('php://input'), true);

    $name = $data['name'] ?? null;
    $phone = $data['phone'] ?? null;
    $email = $data['email'] ?? null;
    $password = $data['password'] ?? null;

    // Basic Server-Side Validation
    $errors = [];
    if (empty($name) || !preg_match("/^[a-zA-Z\s]+$/", $name)) {
        $errors[] = "Invalid Name: Only letters and spaces allowed.";
    }
    if (empty($phone) || !preg_match("/^\d{10}$/", $phone)) {
        $errors[] = "Invalid Phone Number: Must be 10 digits.";
    }
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid Email Address.";
    }
    if (empty($password) || strlen($password) < 6) { // Example: Minimum password length
        $errors[] = "Password must be at least 6 characters long.";
    }

    if (!empty($errors)) {
        $response['message'] = implode(" ", $errors);
        http_response_code(400); // Bad Request
        echo json_encode($response);
        exit();
    }

    // --- Check if Email Already Exists (Security: Prevent duplicate emails) ---
    $stmt_check = $conn->prepare("SELECT id FROM users WHERE email = ?");
    if (!$stmt_check) {
        $response['message'] = "Database error (prepare check): " . $conn->error;
        http_response_code(500);
        echo json_encode($response);
        exit();
    }
    $stmt_check->bind_param("s", $email);
    $stmt_check->execute();
    $stmt_check->store_result();

    if ($stmt_check->num_rows > 0) {
        $response['message'] = "Email address already registered.";
        http_response_code(409); // Conflict
        $stmt_check->close();
        echo json_encode($response);
        exit();
    }
    $stmt_check->close();

    // --- Hash Password (Security: Never store plain text passwords) ---
    $password_hash = password_hash($password, PASSWORD_DEFAULT);

    // --- Insert User into Database (Security: Use Prepared Statements) ---
    $stmt_insert = $conn->prepare("INSERT INTO users (name, phone, email, password_hash) VALUES (?, ?, ?, ?)");
    if (!$stmt_insert) {
        $response['message'] = "Database error (prepare insert): " . $conn->error;
        http_response_code(500);
        echo json_encode($response);
        exit();
    }
    // 'ssss' indicates four string parameters
    $stmt_insert->bind_param("ssss", $name, $phone, $email, $password_hash);

    if ($stmt_insert->execute()) {
        // Registration successful
        // Optionally log the user in automatically by setting session variables
        $_SESSION['user_id'] = $stmt_insert->insert_id; // Get the ID of the new user
        $_SESSION['user_name'] = $name; // Store name for personalisation
        $_SESSION['user_email'] = $email; // Store email

        $response['status'] = 'success';
        $response['message'] = 'Registration successful! Redirecting...';
        // Include redirect URL for the frontend
        $response['redirectUrl'] = '../public/index.html'; // Adjust path as needed
        http_response_code(201); // Created
    } else {
        $response['message'] = "Registration failed: " . $stmt_insert->error;
        http_response_code(500);
    }

    $stmt_insert->close();

} else {
    // Not a POST request
    $response['message'] = "Invalid request method.";
    http_response_code(405); // Method Not Allowed
}

$conn->close();
echo json_encode($response);
?>