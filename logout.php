<?php
session_start(); // Start the session

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
?>
