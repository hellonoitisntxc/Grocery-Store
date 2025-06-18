<?php
// backend/get_captcha.php
// Selects a random CAPTCHA image/code, stores code in session, returns image path.

session_start(); // Start session to store the correct code
header('Content-Type: application/json'); // return JSON data

// Define the available CAPTCHA images and their corresponding codes
// Keys are the paths relative to the frontend/public directory
$captcha_options = [
    'assets/CaptchaImages/image1.jpg' => 'Aeik2',
    'assets/CaptchaImages/image2.jpg' => 'ecb4f',
    'assets/CaptchaImages/image3.jpg' => '7plBJ8',
    'assets/CaptchaImages/image4.jpg' => '24qv3',
];

// --- Select a random image ---
// Get an array of the keys (image paths)
$image_paths = array_keys($captcha_options);
// Pick a random index from the array of keys
$random_key_index = array_rand($image_paths);
// Get the randomly selected image path using the index
$selected_image_path = $image_paths[$random_key_index];

// --- Get the corresponding code ---
$correct_code = $captcha_options[$selected_image_path];

// --- Store the correct code in the session ---
// Store it in lowercase for case-insensitive comparison later
$_SESSION['captcha_code'] = strtolower($correct_code);

// --- Return the path of the selected image to the frontend ---
echo json_encode(['status' => 'success', 'imagePath' => $selected_image_path]);
exit();
?>