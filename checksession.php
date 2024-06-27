<?php
session_start();
// Check if the user is logged in
if (isset($_SESSION['loggedIn']) && $_SESSION['loggedIn'] === 'true') {
    $response = ['loggedIn' => true];
} else {
    $response = ['loggedIn' => false];
}

// Send JSON response
header('Content-Type: application/json');
echo json_encode($response);
?>
