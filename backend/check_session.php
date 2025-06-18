<?php
// check_session.php - Checks if a user is logged in via session

session_start();
header('Content-Type: application/json');

$response = [
    'loggedIn' => false,
    'userName' => null,
    'userId' => null // Optionally send user ID if needed frontend side
];

if (isset($_SESSION['user_id']) && isset($_SESSION['user_name'])) {
    $response['loggedIn'] = true;
    $response['userName'] = $_SESSION['user_name'];
    $response['userId'] = $_SESSION['user_id'];
}

echo json_encode($response);
?>