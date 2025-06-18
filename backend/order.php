<?php
// order.php - Handles placing a new order

require_once 'db.php';
session_start();

header('Content-Type: application/json');

$response = ['status' => 'error', 'message' => 'An unknown error occurred.'];

// --- Check if user is logged in ---
if (!isset($_SESSION['user_id'])) {
    $response['message'] = 'User not logged in. Please log in to place an order.';
    http_response_code(401); // Unauthorized
    echo json_encode($response);
    exit();
}

// --- Process POST request ---
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $product_id = $_POST['product_id'] ?? null;
    $user_id = $_SESSION['user_id'];

    if (empty($product_id) || !filter_var($product_id, FILTER_VALIDATE_INT)) {
        $response['message'] = 'Invalid product selected.';
        http_response_code(400); // Bad Request
        echo json_encode($response);
        exit();
    }

    // --- Get User and Product Details (using prepared statements) ---
    $user_details = null;
    $product_details = null;

    // Get User Details
    $stmt_user = $conn->prepare("SELECT name, email, phone FROM users WHERE id = ?");
    if ($stmt_user) {
        $stmt_user->bind_param("i", $user_id);
        $stmt_user->execute();
        $result_user = $stmt_user->get_result();
        if ($result_user->num_rows === 1) {
            $user_details = $result_user->fetch_assoc();
        }
        $stmt_user->close();
    } else {
        $response['message'] = 'Database error preparing user query: ' . $conn->error;
        http_response_code(500);
        echo json_encode($response);
        exit();
    }


    // Get Product Details
    $stmt_product = $conn->prepare("SELECT name, price FROM products WHERE id = ?");
    if ($stmt_product) {
        $stmt_product->bind_param("i", $product_id);
        $stmt_product->execute();
        $result_product = $stmt_product->get_result();
        if ($result_product->num_rows === 1) {
            $product_details = $result_product->fetch_assoc();
        }
        $stmt_product->close();
    } else {
        $response['message'] = 'Database error preparing product query: ' . $conn->error;
        http_response_code(500);
        echo json_encode($response);
        exit();
    }

    // --- Check if details were found ---
    if (!$user_details || !$product_details) {
        $response['message'] = 'Could not retrieve user or product details. Order cannot be placed.';
        http_response_code(404); // Not Found (or 500 if it's unexpected)
        echo json_encode($response);
        exit();
    }

    // --- Insert Order into Database ---
    $stmt_order = $conn->prepare("INSERT INTO orders (user_id, user_name, user_email, user_phone, product_id, product_name, product_price) VALUES (?, ?, ?, ?, ?, ?, ?)");
    if (!$stmt_order) {
        $response['message'] = 'Database error preparing order insert: ' . $conn->error;
        http_response_code(500);
        echo json_encode($response);
        exit();
    }

    // Bind parameters: i = integer, s = string, d = double/decimal
    $stmt_order->bind_param(
        "isssisd", // Data types: int, str, str, str, int, str, decimal
        $user_id,
        $user_details['name'],
        $user_details['email'],
        $user_details['phone'],
        $product_id,
        $product_details['name'],
        $product_details['price']
    );

    if ($stmt_order->execute()) {
        $response['status'] = 'success';
        $response['message'] = 'Order placed successfully!';
        $response['order_id'] = $stmt_order->insert_id; // Return the new order ID
        http_response_code(201); // Created
    } else {
        $response['message'] = 'Failed to place order: ' . $stmt_order->error;
        http_response_code(500);
    }

    $stmt_order->close();

} else {
    $response['message'] = 'Invalid request method.';
    http_response_code(405); // Method Not Allowed
}

$conn->close();
echo json_encode($response);
?>