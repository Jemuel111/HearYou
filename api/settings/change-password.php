<?php
// api/settings/change-password.php - Change Password Handler

session_start();
require_once '../../config/database.php';
require_once '../../includes/functions.php';

// Check if user is logged in
if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
    $_SESSION['settings_error'] = 'Please login to change your password';
    header('Location: ../../settings.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../../settings.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$current_password = $_POST['current_password'] ?? '';
$new_password = $_POST['new_password'] ?? '';
$confirm_password = $_POST['confirm_password'] ?? '';

// Validate inputs
$errors = [];

if (empty($current_password)) {
    $errors[] = 'Current password is required';
}

if (empty($new_password) || strlen($new_password) < 8) {
    $errors[] = 'New password must be at least 8 characters';
}

if ($new_password !== $confirm_password) {
    $errors[] = 'New passwords do not match';
}

if ($current_password === $new_password) {
    $errors[] = 'New password must be different from current password';
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
    // Get current password hash
    $query = "SELECT password_hash FROM users WHERE id = :user_id LIMIT 1";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();
    
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$user) {
        $_SESSION['settings_error'] = 'User not found';
        header('Location: ../../settings.php');
        exit;
    }
    
    // Verify current password
    if (!password_verify($current_password, $user['password_hash'])) {
        $_SESSION['settings_error'] = 'Current password is incorrect';
        header('Location: ../../settings.php');
        exit;
    }
    
    // Hash new password
    $new_password_hash = password_hash($new_password, PASSWORD_DEFAULT);
    
    // Update password
    $updateQuery = "UPDATE users SET 
                    password_hash = :password_hash,
                    updated_at = NOW()
                    WHERE id = :user_id";
    
    $stmt = $db->prepare($updateQuery);
    $stmt->bindParam(':password_hash', $new_password_hash);
    $stmt->bindParam(':user_id', $user_id);
    
    if ($stmt->execute()) {
        $_SESSION['settings_success'] = 'Password changed successfully!';
        header('Location: ../../settings.php');
        exit;
    } else {
        throw new Exception('Failed to update password');
    }
    
} catch (PDOException $e) {
    error_log('Password change error: ' . $e->getMessage());
    $_SESSION['settings_error'] = 'An error occurred. Please try again.';
    header('Location: ../../settings.php');
    exit;
}
?>