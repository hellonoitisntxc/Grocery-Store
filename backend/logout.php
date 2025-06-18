<?php
// logout.php - Handles user logout

session_start(); // Access the existing session

// Unset all session variables related to the user
$_SESSION = array();


if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Finally, destroy the session.
session_destroy();

// Redirect the user to the home page or login page after logout
header("Location: ../frontend/public/index.html");
exit();
?>