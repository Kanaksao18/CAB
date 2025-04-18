<?php
require_once 'config.php';

function registerUser($email, $password, $fullName, $role, $phoneNumber) {
    global $conn;
    
    // Hash password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    
    $stmt = $conn->prepare("INSERT INTO users (email, password, full_name, role, phone_number) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $email, $hashedPassword, $fullName, $role, $phoneNumber);
    
    if ($stmt->execute()) {
        return true;
    }
    return false;
}

function loginUser($email, $password) {
    global $conn;
    
    $stmt = $conn->prepare("SELECT id, email, password, role FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($user = $result->fetch_assoc()) {
        if (password_verify($password, $user['password'])) {
            // Set session variables
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_role'] = $user['role'];
            return true;
        }
    }
    return false;
}

function logout() {
    session_destroy();
    // Use the BASE_URL constant for redirection
    header('Location: ' . BASE_URL . '/public/login.php');
    exit();
} 