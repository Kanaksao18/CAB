<?php
// Define the base path for includes
define('BASE_PATH', dirname(__DIR__));

// Include necessary configuration and authentication functions
require_once BASE_PATH . '/includes/config.php';
require_once BASE_PATH . '/includes/auth.php';

// Check if the user is logged in
if (isLoggedIn()) {
    // Redirect based on user role
    if (isDriver()) {
        header('Location: ' . BASE_URL . '/public/driver/dashboard.php');
    } elseif (isPassenger()) {
        header('Location: ' . BASE_URL . '/public/passenger/dashboard.php');
    } else {
        // If role is somehow not set, logout and redirect to login
        logout();
    }
} else {
    // If not logged in, redirect to the login page
    header('Location: ' . BASE_URL . '/public/login.php');
}

// Ensure no further code execution after redirection
exit();
?> 