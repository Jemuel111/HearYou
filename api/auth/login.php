<?php
// api/auth/login.php - Login Handler

session_start();
require_once '../../config/database.php';
require_once '../../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../../auth.php');
    exit;
}

$email = sanitizeInput($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';
$remember = isset($_POST['remember']);

// Validate inputs
if (empty($email) || empty($password)) {
    $_SESSION['auth_error'] = 'Please enter both email and password';
    header('Location: ../../auth.php');
    exit;
}

// Initialize database
$database = new Database();
$db = $database->getConnection();

try {
    // Check if user exists
    $query = "SELECT id, username, email, password_hash, full_name, is_active 
              FROM users WHERE email = :email LIMIT 1";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':email', $email);
    $stmt->execute();
    
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$user) {
        $_SESSION['auth_error'] = 'Invalid email or password';
        header('Location: ../../auth.php');
        exit;
    }
    
    // Check if account is active
    if (!$user['is_active']) {
        $_SESSION['auth_error'] = 'Your account has been deactivated. Please contact support.';
        header('Location: ../../auth.php');
        exit;
    }
    
    // Verify password
    if (!password_verify($password, $user['password_hash'])) {
        $_SESSION['auth_error'] = 'Invalid email or password';
        header('Location: ../../auth.php');
        exit;
    }
    
    // Login successful - set session variables
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['email'] = $user['email'];
    $_SESSION['full_name'] = $user['full_name'];
    $_SESSION['logged_in'] = true;
    
    // Update last login
    $updateQuery = "UPDATE users SET last_login = NOW() WHERE id = :id";
    $updateStmt = $db->prepare($updateQuery);
    $updateStmt->bindParam(':id', $user['id']);
    $updateStmt->execute();
    
    // Remember me functionality
    if ($remember) {
        $token = bin2hex(random_bytes(32));
        setcookie('remember_token', $token, time() + (86400 * 30), '/'); // 30 days
        
        // Store token in database (you'd need to create a remember_tokens table)
        // For now, we'll skip this
    }
    
    // Redirect to home
    header('Location: ../../index.php');
    exit;
    
} catch (PDOException $e) {
    error_log('Login error: ' . $e->getMessage());
    $_SESSION['auth_error'] = 'An error occurred. Please try again.';
    header('Location: ../../auth.php');
    exit;
}
?>