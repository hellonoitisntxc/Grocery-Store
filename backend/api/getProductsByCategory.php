<?php
// api/getProductsByCategory.php - Fetches products based on category for AJAX calls

require_once '../db.php';

header('Content-Type: application/json');

$category = $_GET['category'] ?? null;

if (!$category) {
    http_response_code(400); // Bad Request
    echo json_encode(['status' => 'error', 'message' => 'Category parameter is required.']);
    exit();
}

$products = [];

// Security: Use Prepared Statements
$stmt = $conn->prepare("SELECT id, name, image, price FROM products WHERE category = ? ORDER BY name ASC");
if (!$stmt) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Database error (prepare): ' . $conn->error]);
    exit();
}

$stmt->bind_param("s", $category);

if ($stmt->execute()) {
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        // Construct image path
        $row['image_path'] = 'assets/' . $row['image'];
        $products[] = $row;
    }
    $result->free();
    echo json_encode(['status' => 'success', 'products' => $products]);
} else {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Failed to fetch products: ' . $stmt->error]);
}

$stmt->close();
$conn->close();
?>