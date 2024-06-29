<?php
session_start();

// Set response content type to JSON
header('Content-Type: application/json');

try {
    // Check if the user is logged in
    if (isset($_SESSION['loggedIn']) && $_SESSION['loggedIn'] === true) {
        $response = ['loggedIn' => true];
    } else {
        $response = ['loggedIn' => false];
    }

    // Send JSON response
    echo json_encode($response);
} catch (Exception $e) {
    // Handle any potential errors
    error_log('Error checking login status: ' . $e->getMessage());
    echo json_encode(['error' => 'Error checking login status']);
}
?>
