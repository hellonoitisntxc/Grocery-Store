<?php
// api/getOrder.php - RESTful endpoint to get order details by ID

require_once '../db.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *'); // Allow requests
header('Access-Control-Allow-Methods: GET'); // Allow only GET requests
header('Access-Control-Allow-Headers: Content-Type');

$response = ['status' => 'error', 'message' => 'An error occurred.'];

// Check if it's a GET request and if 'id' is provided
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $order_id = $_GET['id'] ?? null;

    if (empty($order_id) || !filter_var($order_id, FILTER_VALIDATE_INT)) {
        http_response_code(400); // Bad Request
        $response['message'] = 'Valid integer Order ID parameter is required.';
        echo json_encode($response);
        exit();
    }

    // Fetch order details (Security: Use Prepared Statements)
    $stmt = $conn->prepare("SELECT id, user_id, user_name, user_email, user_phone, product_id, product_name, product_price, order_time FROM orders WHERE id = ?");
    if (!$stmt) {
        http_response_code(500);
        $response['message'] = 'Database error (prepare): ' . $conn->error;
        echo json_encode($response);
        exit();
    }

    $stmt->bind_param("i", $order_id);

    if ($stmt->execute()) {
        $result = $stmt->get_result();
        if ($result->num_rows === 1) {
            $order = $result->fetch_assoc();
            http_response_code(200); // OK
            // Structure the response clearly
            echo json_encode(['status' => 'success', 'order' => $order]);
        } else {
            http_response_code(404); // Not Found
            $response['message'] = 'Order not found.';
            echo json_encode($response);
        }
        $result->free();
    } else {
        http_response_code(500);
        $response['message'] = 'Failed to fetch order details: ' . $stmt->error;
        echo json_encode($response);
    }

    $stmt->close();

} else {
    http_response_code(405); // Method Not Allowed
    $response['message'] = 'Invalid request method. Only GET is allowed.';
    echo json_encode($response);
}

$conn->close();
?>