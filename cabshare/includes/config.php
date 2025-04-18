<?php
// Database configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'cabshare');

// Application configuration
define('BASE_URL', 'http://localhost:8080/cabshare');

// Create database connection
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Function to check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Function to check user role
function isDriver() {
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'driver';
}

// Function to check if user is passenger
function isPassenger() {
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'passenger';
}

// --- Removed registerUser, loginUser, and logout functions from here --- 