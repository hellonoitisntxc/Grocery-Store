<?php
// login.php - Handles user login and authentication

require_once 'db.php';
session_start(); // Start session to access stored CAPTCHA code

header('Content-Type: application/json');

$response = ['status' => 'error', 'message' => 'An unknown error occurred.'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'] ?? null;
    $password = $_POST['password'] ?? null;
    $captcha_input = $_POST['captcha'] ?? null; // Get CAPTCHA input from form

    // Basic Server-Side Validation
    $errors = [];
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid Email Address.";
    }
    if (empty($password)) {
        $errors[] = "Password is required.";
    }

    // --- CAPTCHA Validation --- //
    $isCaptchaValid = false;
    // Check if the user submitted something AND if the session variable exists
    if (!empty($captcha_input) && isset($_SESSION['captcha_code'])) {
        // Compare the user's input (converted to lowercase)
        // with the code stored in the session (already lowercase)
        if (strtolower(trim($captcha_input)) === $_SESSION['captcha_code']) {
            $isCaptchaValid = true;
        }

        unset($_SESSION['captcha_code']);

    } else {
        // If session code isn't set (e.g., session expired, direct POST attempt),
        // it's also invalid. Ensure it's unset if it somehow existed but input was empty.
        if (isset($_SESSION['captcha_code'])) {
            unset($_SESSION['captcha_code']);
        }
    }

    // Add error message if CAPTCHA was not valid
    if (!$isCaptchaValid) {
        $errors[] = "Incorrect or missing CAPTCHA code.";
    }
    // --- End CAPTCHA Validation --- //


    // If any validation errors occurred (including CAPTCHA)
    if (!empty($errors)) {
        $response['message'] = implode(" ", $errors);
        http_response_code(400); // Bad Request
        echo json_encode($response);
        $conn->close(); // Close DB connection before exiting
        exit();
    }

    // --- Authenticate User ---
    $stmt = $conn->prepare("SELECT id, name, email, password_hash FROM users WHERE email = ?");
    if (!$stmt) {
        $response['message'] = "Database error (prepare): " . $conn->error;
        http_response_code(500);
        echo json_encode($response);
        $conn->close();
        exit();
    }
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();

        // Verify password
        if (password_verify($password, $user['password_hash'])) {
            // Password is correct, login successful

            // Regenerate session ID upon login for security
            session_regenerate_id(true);

            // Store user information in session variables
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_email'] = $user['email'];

            $response['status'] = 'success';
            $response['message'] = 'Login successful! Redirecting...';
            $response['redirectUrl'] = '../public/index.html'; // Redirect to main page
            http_response_code(200);

        } else {
            // Invalid password
            $response['message'] = "Invalid email or password."; // generic message
            http_response_code(401); // Unauthorised
        }
    } else {
        // User not found
        $response['message'] = "Invalid email or password."; // generic message
        http_response_code(401); // Unauthorised
    }

    $stmt->close();

} else {
    // Not a POST request
    $response['message'] = "Invalid request method.";
    http_response_code(405); // Method Not Allowed
}

$conn->close();
echo json_encode($response);
?>