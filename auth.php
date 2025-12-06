<?php
// auth.php - Login & Signup Page
session_start();

// If already logged in, redirect to home
if (isset($_SESSION['user_id']) && isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
    header('Location: index.php');
    exit;
}

$error = '';
$success = '';

// Check for messages
if (isset($_SESSION['auth_error'])) {
    $error = $_SESSION['auth_error'];
    unset($_SESSION['auth_error']);
}

if (isset($_SESSION['auth_success'])) {
    $success = $_SESSION['auth_success'];
    unset($_SESSION['auth_success']);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - HearYou</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/auth.css">
</head>
<body class="auth-page">
    <div class="auth-container">
        <div class="auth-card">
            <!-- Logo -->
            <div class="auth-logo">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <path d="M9 18V5l12-2v13M9 18l-7 2V7l7-2M9 18l7 2" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                <h1>HearYou</h1>
            </div>

            <!-- Messages -->
            <?php if ($error): ?>
                <div class="alert alert-error">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <circle cx="12" cy="12" r="10" stroke-width="2"/>
                        <line x1="12" y1="8" x2="12" y2="12" stroke-width="2"/>
                        <line x1="12" y1="16" x2="12.01" y2="16" stroke-width="2"/>
                    </svg>
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="alert alert-success">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14" stroke-width="2"/>
                        <polyline points="22 4 12 14.01 9 11.01" stroke-width="2"/>
                    </svg>
                    <?php echo htmlspecialchars($success); ?>
                </div>
            <?php endif; ?>

            <!-- Login Form -->
            <div id="loginForm" class="auth-form active">
                <h2>Welcome Back</h2>
                <p class="auth-subtitle">Sign in to continue to HearYou</p>

                <form action="api/auth/login.php" method="POST">
                    <div class="form-group">
                        <label for="login_email">Email Address</label>
                        <input type="email" id="login_email" name="email" required autocomplete="email" placeholder="you@example.com">
                    </div>

                    <div class="form-group">
                        <label for="login_password">Password</label>
                        <input type="password" id="login_password" name="password" required autocomplete="current-password" placeholder="Enter your password">
                    </div>

                    <div class="form-options">
                        <label class="checkbox-label">
                            <input type="checkbox" name="remember" value="1">
                            <span>Remember me</span>
                        </label>
                        <a href="#" class="link">Forgot password?</a>
                    </div>

                    <button type="submit" class="btn btn-primary btn-block">
                        Sign In
                    </button>
                </form>

                <div class="auth-switch">
                    Don't have an account? 
                    <a href="#" onclick="switchToSignup(); return false;">Sign up</a>
                </div>

                <div class="auth-divider">
                    <span>OR</span>
                </div>

                <button class="btn btn-secondary btn-block" onclick="continueAsGuest()">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" stroke-width="2"/>
                        <circle cx="12" cy="7" r="4" stroke-width="2"/>
                    </svg>
                    Continue as Guest
                </button>
            </div>

            <!-- Signup Form -->
            <div id="signupForm" class="auth-form">
                <h2>Create Account</h2>
                <p class="auth-subtitle">Join HearYou and discover your mood</p>

                <form action="api/auth/register.php" method="POST">
                    <div class="form-group">
                        <label for="signup_name">Full Name</label>
                        <input type="text" id="signup_name" name="full_name" required placeholder="Your Name">
                    </div>

                    <div class="form-group">
                        <label for="signup_email">Email Address</label>
                        <input type="email" id="signup_email" name="email" required autocomplete="email" placeholder="you@example.com">
                    </div>

                    <div class="form-group">
                        <label for="signup_username">Username</label>
                        <input type="text" id="signup_username" name="username" required placeholder="Username">
                    </div>

                    <div class="form-group">
                        <label for="signup_password">Password</label>
                        <input type="password" id="signup_password" name="password" required autocomplete="new-password" placeholder="At least 8 characters">
                        <small class="form-hint">Must be at least 8 characters</small>
                    </div>

                    <div class="form-group">
                        <label for="signup_confirm">Confirm Password</label>
                        <input type="password" id="signup_confirm" name="confirm_password" required autocomplete="new-password" placeholder="Confirm your password">
                    </div>

                    <div class="form-options">
                        <label class="checkbox-label">
                            <input type="checkbox" name="terms" required>
                            <span>I agree to the <a href="#" class="link">Terms of Service</a> and <a href="#" class="link">Privacy Policy</a></span>
                        </label>
                    </div>

                    <button type="submit" class="btn btn-primary btn-block">
                        Create Account
                    </button>
                </form>

                <div class="auth-switch">
                    Already have an account? 
                    <a href="#" onclick="switchToLogin(); return false;">Sign in</a>
                </div>
            </div>
        </div>

        <!-- Features Section -->
        <div class="auth-features">
            <h3>Why HearYou?</h3>
            <div class="feature-list">
                <div class="feature-item">
                    <div class="feature-icon">😢😊😌⚡</div>
                    <div class="feature-text">
                        <h4>Emotion-Based Filtering</h4>
                        <p>Find the perfect song for your mood</p>
                    </div>
                </div>
                <div class="feature-item">
                    <div class="feature-icon">🤖</div>
                    <div class="feature-text">
                        <h4>AI Music Companion</h4>
                        <p>Get personalized recommendations</p>
                    </div>
                </div>
                <div class="feature-item">
                    <div class="feature-icon">❤️</div>
                    <div class="feature-text">
                        <h4>Your Library</h4>
                        <p>Save and organize your favorites</p>
                    </div>
                </div>
                <div class="feature-item">
                    <div class="feature-icon">🎵</div>
                    <div class="feature-text">
                        <h4>Seamless Playback</h4>
                        <p>Beautiful player with full controls</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function switchToSignup() {
            document.getElementById('loginForm').classList.remove('active');
            document.getElementById('signupForm').classList.add('active');
        }

        function switchToLogin() {
            document.getElementById('signupForm').classList.remove('active');
            document.getElementById('loginForm').classList.add('active');
        }

        function continueAsGuest() {
            window.location.href = 'index.php';
        }

        // Password strength indicator
        const signupPassword = document.getElementById('signup_password');
        if (signupPassword) {
            signupPassword.addEventListener('input', function() {
                const password = this.value;
                const hint = this.nextElementSibling;
                
                if (password.length < 8) {
                    hint.textContent = 'Password must be at least 8 characters';
                    hint.style.color = '#ef4444';
                } else if (password.length < 12) {
                    hint.textContent = 'Good password strength';
                    hint.style.color = '#eab308';
                } else {
                    hint.textContent = 'Strong password!';
                    hint.style.color = '#22c55e';
                }
            });
        }

        // Form validation
        const signupForm = document.querySelector('#signupForm form');
        if (signupForm) {
            signupForm.addEventListener('submit', function(e) {
                const password = document.getElementById('signup_password').value;
                const confirm = document.getElementById('signup_confirm').value;
                
                if (password !== confirm) {
                    e.preventDefault();
                    alert('Passwords do not match!');
                    return false;
                }
            });
        }
    </script>
</body>
</html>