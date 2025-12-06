<?php
// settings.php - User Settings Page

session_start();
require_once 'config/database.php';
require_once 'includes/functions.php';

// Check if user is logged in
$isLoggedIn = isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
$username = $_SESSION['username'] ?? 'Guest';
$full_name = $_SESSION['full_name'] ?? 'Guest User';
$email = $_SESSION['email'] ?? '';

$success = '';
$error = '';

if (isset($_SESSION['settings_success'])) {
    $success = $_SESSION['settings_success'];
    unset($_SESSION['settings_success']);
}

if (isset($_SESSION['settings_error'])) {
    $error = $_SESSION['settings_error'];
    unset($_SESSION['settings_error']);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings - HearYou</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/settings.css">
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="logo">
            <svg class="logo-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                <path d="M9 18V5l12-2v13M9 18l-7 2V7l7-2M9 18l7 2" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            <h1>HearYou</h1>
        </div>
        <div class="header-actions">
            <a href="index.php" class="btn-back">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <path d="M19 12H5M12 19l-7-7 7-7" stroke-width="2" stroke-linecap="round"/>
                </svg>
                Back to Home
            </a>
        </div>
    </header>

    <div class="settings-container">
        <div class="settings-sidebar">
            <div class="settings-nav">
                <button class="settings-nav-item active" onclick="showSettingsTab('account')">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" stroke-width="2"/>
                        <circle cx="12" cy="7" r="4" stroke-width="2"/>
                    </svg>
                    <span>Account</span>
                </button>
                <button class="settings-nav-item" onclick="showSettingsTab('appearance')">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <circle cx="12" cy="12" r="5" stroke-width="2"/>
                        <line x1="12" y1="1" x2="12" y2="3" stroke-width="2"/>
                        <line x1="12" y1="21" x2="12" y2="23" stroke-width="2"/>
                        <line x1="4.22" y1="4.22" x2="5.64" y2="5.64" stroke-width="2"/>
                        <line x1="18.36" y1="18.36" x2="19.78" y2="19.78" stroke-width="2"/>
                        <line x1="1" y1="12" x2="3" y2="12" stroke-width="2"/>
                        <line x1="21" y1="12" x2="23" y2="12" stroke-width="2"/>
                        <line x1="4.22" y1="19.78" x2="5.64" y2="18.36" stroke-width="2"/>
                        <line x1="18.36" y1="5.64" x2="19.78" y2="4.22" stroke-width="2"/>
                    </svg>
                    <span>Appearance</span>
                </button>
                <button class="settings-nav-item" onclick="showSettingsTab('playback')">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <circle cx="12" cy="12" r="10" stroke-width="2"/>
                        <polygon points="10 8 16 12 10 16 10 8" fill="currentColor"/>
                    </svg>
                    <span>Playback</span>
                </button>
                <button class="settings-nav-item" onclick="showSettingsTab('notifications')">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9M13.73 21a2 2 0 0 1-3.46 0" stroke-width="2"/>
                    </svg>
                    <span>Notifications</span>
                </button>
                <button class="settings-nav-item" onclick="showSettingsTab('privacy')">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" stroke-width="2"/>
                    </svg>
                    <span>Privacy</span>
                </button>
            </div>
        </div>

        <div class="settings-content">
            <?php if ($success): ?>
                <div class="alert alert-success">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14" stroke-width="2"/>
                        <polyline points="22 4 12 14.01 9 11.01" stroke-width="2"/>
                    </svg>
                    <?php echo htmlspecialchars($success); ?>
                </div>
            <?php endif; ?>

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

            <!-- Account Settings -->
            <div id="accountTab" class="settings-tab active">
                <h2>Account Settings</h2>
                <p class="settings-subtitle">Manage your account information and preferences</p>

                <div class="settings-section">
                    <h3>Profile Information</h3>
                    <form action="api/settings/update-profile.php" method="POST" class="settings-form">
                        <div class="form-group">
                            <label>Full Name</label>
                            <input type="text" name="full_name" value="<?php echo htmlspecialchars($full_name); ?>" <?php echo !$isLoggedIn ? 'disabled' : ''; ?>>
                        </div>
                        <div class="form-group">
                            <label>Username</label>
                            <input type="text" name="username" value="<?php echo htmlspecialchars($username); ?>" <?php echo !$isLoggedIn ? 'disabled' : ''; ?>>
                        </div>
                        <div class="form-group">
                            <label>Email</label>
                            <input type="email" name="email" value="<?php echo htmlspecialchars($email); ?>" <?php echo !$isLoggedIn ? 'disabled' : ''; ?>>
                        </div>
                        <?php if ($isLoggedIn): ?>
                            <button type="submit" class="btn btn-primary">Save Changes</button>
                        <?php else: ?>
                            <p class="info-text">Please <a href="auth.php">login</a> to edit your profile</p>
                        <?php endif; ?>
                    </form>
                </div>

                <div class="settings-section">
                    <h3>Change Password</h3>
                    <form action="api/settings/change-password.php" method="POST" class="settings-form">
                        <div class="form-group">
                            <label>Current Password</label>
                            <input type="password" name="current_password" <?php echo !$isLoggedIn ? 'disabled' : ''; ?>>
                        </div>
                        <div class="form-group">
                            <label>New Password</label>
                            <input type="password" name="new_password" <?php echo !$isLoggedIn ? 'disabled' : ''; ?>>
                        </div>
                        <div class="form-group">
                            <label>Confirm New Password</label>
                            <input type="password" name="confirm_password" <?php echo !$isLoggedIn ? 'disabled' : ''; ?>>
                        </div>
                        <?php if ($isLoggedIn): ?>
                            <button type="submit" class="btn btn-primary">Update Password</button>
                        <?php endif; ?>
                    </form>
                </div>

                <div class="settings-section danger-zone">
                    <h3>Danger Zone</h3>
                    <?php if ($isLoggedIn): ?>
                        <button class="btn btn-danger" onclick="confirmDeleteAccount()">Delete Account</button>
                        <p class="info-text">Once you delete your account, there is no going back. Please be certain.</p>
                    <?php else: ?>
                        <p class="info-text">Account actions are only available when logged in</p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Appearance Settings -->
            <div id="appearanceTab" class="settings-tab">
                <h2>Appearance</h2>
                <p class="settings-subtitle">Customize how HearYou looks</p>

                <div class="settings-section">
                    <h3>Theme</h3>
                    <div class="theme-selector">
                        <label class="theme-option">
                            <input type="radio" name="theme" value="dark" checked>
                            <div class="theme-card dark-theme">
                                <div class="theme-preview"></div>
                                <span>Dark</span>
                            </div>
                        </label>
                        <label class="theme-option">
                            <input type="radio" name="theme" value="light">
                            <div class="theme-card light-theme">
                                <div class="theme-preview"></div>
                                <span>Light</span>
                            </div>
                        </label>
                        <label class="theme-option">
                            <input type="radio" name="theme" value="auto">
                            <div class="theme-card auto-theme">
                                <div class="theme-preview"></div>
                                <span>Auto</span>
                            </div>
                        </label>
                    </div>
                </div>

                <div class="settings-section">
                    <h3>Display</h3>
                    <div class="setting-item">
                        <div>
                            <strong>Compact Mode</strong>
                            <p>Show more songs in less space</p>
                        </div>
                        <label class="toggle">
                            <input type="checkbox" id="compactMode">
                            <span class="toggle-slider"></span>
                        </label>
                    </div>
                    <div class="setting-item">
                        <div>
                            <strong>Show Album Art</strong>
                            <p>Display album covers in song lists</p>
                        </div>
                        <label class="toggle">
                            <input type="checkbox" id="showAlbumArt" checked>
                            <span class="toggle-slider"></span>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Playback Settings -->
            <div id="playbackTab" class="settings-tab">
                <h2>Playback</h2>
                <p class="settings-subtitle">Control your music playback experience</p>

                <div class="settings-section">
                    <h3>Audio Quality</h3>
                    <div class="setting-item">
                        <div>
                            <strong>Streaming Quality</strong>
                            <p>Higher quality uses more data</p>
                        </div>
                        <select class="setting-select">
                            <option value="low">Low (96 kbps)</option>
                            <option value="medium">Medium (160 kbps)</option>
                            <option value="high" selected>High (320 kbps)</option>
                        </select>
                    </div>
                </div>

                <div class="settings-section">
                    <h3>Playback Options</h3>
                    <div class="setting-item">
                        <div>
                            <strong>Autoplay</strong>
                            <p>Automatically play similar songs when queue ends</p>
                        </div>
                        <label class="toggle">
                            <input type="checkbox" id="autoplay" checked>
                            <span class="toggle-slider"></span>
                        </label>
                    </div>
                    <div class="setting-item">
                        <div>
                            <strong>Crossfade</strong>
                            <p>Smooth transition between songs</p>
                        </div>
                        <label class="toggle">
                            <input type="checkbox" id="crossfade">
                            <span class="toggle-slider"></span>
                        </label>
                    </div>
                    <div class="setting-item">
                        <div>
                            <strong>Gapless Playback</strong>
                            <p>Eliminate silence between tracks</p>
                        </div>
                        <label class="toggle">
                            <input type="checkbox" id="gapless" checked>
                            <span class="toggle-slider"></span>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Notifications Settings -->
            <div id="notificationsTab" class="settings-tab">
                <h2>Notifications</h2>
                <p class="settings-subtitle">Manage your notification preferences</p>

                <div class="settings-section">
                    <h3>Email Notifications</h3>
                    <div class="setting-item">
                        <div>
                            <strong>New Music Alerts</strong>
                            <p>Get notified about new songs matching your taste</p>
                        </div>
                        <label class="toggle">
                            <input type="checkbox" id="newMusicNotifs" checked>
                            <span class="toggle-slider"></span>
                        </label>
                    </div>
                    <div class="setting-item">
                        <div>
                            <strong>Weekly Summary</strong>
                            <p>Receive your listening stats every week</p>
                        </div>
                        <label class="toggle">
                            <input type="checkbox" id="weeklySummary">
                            <span class="toggle-slider"></span>
                        </label>
                    </div>
                </div>

                <div class="settings-section">
                    <h3>Push Notifications</h3>
                    <div class="setting-item">
                        <div>
                            <strong>Browser Notifications</strong>
                            <p>Get desktop notifications</p>
                        </div>
                        <label class="toggle">
                            <input type="checkbox" id="browserNotifs">
                            <span class="toggle-slider"></span>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Privacy Settings -->
            <div id="privacyTab" class="settings-tab">
                <h2>Privacy & Security</h2>
                <p class="settings-subtitle">Control your data and privacy</p>

                <div class="settings-section">
                    <h3>Privacy Controls</h3>
                    <div class="setting-item">
                        <div>
                            <strong>Private Profile</strong>
                            <p>Hide your profile from other users</p>
                        </div>
                        <label class="toggle">
                            <input type="checkbox" id="privateProfile">
                            <span class="toggle-slider"></span>
                        </label>
                    </div>
                    <div class="setting-item">
                        <div>
                            <strong>Show Listening Activity</strong>
                            <p>Let others see what you're listening to</p>
                        </div>
                        <label class="toggle">
                            <input type="checkbox" id="showActivity" checked>
                            <span class="toggle-slider"></span>
                        </label>
                    </div>
                </div>

                <div class="settings-section">
                    <h3>Data Management</h3>
                    <button class="btn btn-secondary">Download Your Data</button>
                    <p class="info-text">Get a copy of all your data in HearYou</p>
                </div>
            </div>
        </div>
    </div>

    <script src="assets/js/settings.js"></script>
</body>
</html>