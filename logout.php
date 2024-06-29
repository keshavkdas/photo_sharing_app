<?php
session_start(); // Start the session

header('Content-Type: application/json'); // Set response content type to JSON

try {
    // Check if the user is logged in
    if (isset($_SESSION['username'])) {
        // Unset all session variables
        session_unset();
        // Destroy the session
        session_destroy();
        // Send JSON response indicating successful logout
        echo json_encode(['message' => 'Logout successful']);
    } else {
        // Send JSON response if user is not logged in
        echo json_encode(['message' => 'User not logged in']);
    }
} catch (Exception $e) {
    // Handle any potential errors
    error_log('Error during logout: ' . $e->getMessage());
    echo json_encode(['message' => 'Error during logout']);
}
?>
