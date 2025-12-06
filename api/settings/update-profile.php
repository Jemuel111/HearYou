<?php
// api/settings/update-profile.php - Update Profile Handler

session_start();
require_once '../../config/database.php';
require_once '../../includes/functions.php';

// Check if user is logged in
if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
    $_SESSION['settings_error'] = 'Please login to update your profile';
    header('Location: ../../settings.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../../settings.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$full_name = sanitizeInput($_POST['full_name'] ?? '');
$username = sanitizeInput($_POST['username'] ?? '');
$email = sanitizeInput($_POST['email'] ?? '');

// Validate inputs
$errors = [];

if (empty($full_name)) {
    $errors[] = 'Full name is required';
}

if (empty($username) || strlen($username) < 3) {
    $errors[] = 'Username must be at least 3 characters';
}

if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'Valid email is required';
}

if (!empty($errors)) {
    $_SESSION['settings_error'] = implode('<br>', $errors);
    header('Location: ../../settings.php');
    exit;
}

// Initialize database
$database = new Database();
$db = $database->getConnection();

try {
    // Check if username is taken by another user
    $checkUsername = "SELECT id FROM users WHERE username = :username AND id != :user_id LIMIT 1";
    $stmt = $db->prepare($checkUsername);
    $stmt->bindParam(':username', $username);
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();
    
    if ($stmt->rowCount() > 0) {
        $_SESSION['settings_error'] = 'Username already taken';
        header('Location: ../../settings.php');
        exit;
    }
    
    // Check if email is taken by another user
    $checkEmail = "SELECT id FROM users WHERE email = :email AND id != :user_id LIMIT 1";
    $stmt = $db->prepare($checkEmail);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();
    
    if ($stmt->rowCount() > 0) {
        $_SESSION['settings_error'] = 'Email already registered';
        header('Location: ../../settings.php');
        exit;
    }
    
    // Update user profile
    $query = "UPDATE users SET 
              full_name = :full_name,
              username = :username,
              email = :email,
              updated_at = NOW()
              WHERE id = :user_id";
    
    $stmt = $db->prepare($query);
    $stmt->bindParam(':full_name', $full_name);
    $stmt->bindParam(':username', $username);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':user_id', $user_id);
    
    if ($stmt->execute()) {
        // Update session variables
        $_SESSION['full_name'] = $full_name;
        $_SESSION['username'] = $username;
        $_SESSION['email'] = $email;
        
        $_SESSION['settings_success'] = 'Profile updated successfully!';
        header('Location: ../../settings.php');
        exit;
    } else {
        throw new Exception('Failed to update profile');
    }
    
} catch (PDOException $e) {
    error_log('Profile update error: ' . $e->getMessage());
    $_SESSION['settings_error'] = 'An error occurred. Please try again.';
    header('Location: ../../settings.php');
    exit;
}
?>