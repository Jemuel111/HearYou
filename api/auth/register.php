<?php
// api/auth/register.php - Registration Handler

session_start();
require_once '../../config/database.php';
require_once '../../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../../auth.php');
    exit;
}

$full_name = sanitizeInput($_POST['full_name'] ?? '');
$email = sanitizeInput($_POST['email'] ?? '');
$username = sanitizeInput($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';
$confirm_password = $_POST['confirm_password'] ?? '';
$terms = isset($_POST['terms']);

// Validate inputs
$errors = [];

if (empty($full_name)) {
    $errors[] = 'Full name is required';
}

if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'Valid email is required';
}

if (empty($username) || strlen($username) < 3) {
    $errors[] = 'Username must be at least 3 characters';
}

if (empty($password) || strlen($password) < 8) {
    $errors[] = 'Password must be at least 8 characters';
}

if ($password !== $confirm_password) {
    $errors[] = 'Passwords do not match';
}

if (!$terms) {
    $errors[] = 'You must agree to the Terms of Service';
}

if (!empty($errors)) {
    $_SESSION['auth_error'] = implode('<br>', $errors);
    header('Location: ../../auth.php');
    exit;
}

// Initialize database
$database = new Database();
$db = $database->getConnection();

try {
    // Check if email already exists
    $checkEmail = "SELECT id FROM users WHERE email = :email LIMIT 1";
    $stmt = $db->prepare($checkEmail);
    $stmt->bindParam(':email', $email);
    $stmt->execute();
    
    if ($stmt->rowCount() > 0) {
        $_SESSION['auth_error'] = 'Email already registered';
        header('Location: ../../auth.php');
        exit;
    }
    
    // Check if username already exists
    $checkUsername = "SELECT id FROM users WHERE username = :username LIMIT 1";
    $stmt = $db->prepare($checkUsername);
    $stmt->bindParam(':username', $username);
    $stmt->execute();
    
    if ($stmt->rowCount() > 0) {
        $_SESSION['auth_error'] = 'Username already taken';
        header('Location: ../../auth.php');
        exit;
    }
    
    // Hash password
    $password_hash = password_hash($password, PASSWORD_DEFAULT);
    
    // Insert new user
    $query = "INSERT INTO users (username, email, password_hash, full_name, created_at) 
              VALUES (:username, :email, :password_hash, :full_name, NOW())";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':username', $username);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':password_hash', $password_hash);
    $stmt->bindParam(':full_name', $full_name);
    
    if ($stmt->execute()) {
        $_SESSION['auth_success'] = 'Account created successfully! Please login.';
        header('Location: ../../auth.php');
        exit;
    } else {
        throw new Exception('Failed to create account');
    }
    
} catch (PDOException $e) {
    error_log('Registration error: ' . $e->getMessage());
    $_SESSION['auth_error'] = 'An error occurred. Please try again.';
    header('Location: ../../auth.php');
    exit;
}
?>