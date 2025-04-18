<?php
// Include necessary configuration and authentication functions
require_once '../includes/config.php';
require_once '../includes/auth.php';

// Call the logout function
logout();

// If for some reason the logout function doesn't redirect
header('Location: ' . BASE_URL . '/public/login.php');
exit();
?> 