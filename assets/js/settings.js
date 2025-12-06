// assets/js/settings.js - Settings Page Logic

// Initialize settings from localStorage
document.addEventListener('DOMContentLoaded', function() {
    loadSettings();
    attachEventListeners();
});

// Show settings tab
function showSettingsTab(tabName) {
    // Hide all tabs
    document.querySelectorAll('.settings-tab').forEach(tab => {
        tab.classList.remove('active');
    });
    
    // Show selected tab
    const targetTab = document.getElementById(tabName + 'Tab');
    if (targetTab) {
        targetTab.classList.add('active');
    }
    
    // Update nav
    document.querySelectorAll('.settings-nav-item').forEach(item => {
        item.classList.remove('active');
    });
    
    event.target.closest('.settings-nav-item').classList.add('active');
}

// Load settings from localStorage
function loadSettings() {
    const settings = {
        theme: localStorage.getItem('theme') || 'dark',
        compactMode: localStorage.getItem('compactMode') === 'true',
        showAlbumArt: localStorage.getItem('showAlbumArt') !== 'false',
        autoplay: localStorage.getItem('autoplay') !== 'false',
        crossfade: localStorage.getItem('crossfade') === 'true',
        gapless: localStorage.getItem('gapless') !== 'false',
        streamingQuality: localStorage.getItem('streamingQuality') || 'high',
        newMusicNotifs: localStorage.getItem('newMusicNotifs') !== 'false',
        weeklySummary: localStorage.getItem('weeklySummary') === 'true',
        browserNotifs: localStorage.getItem('browserNotifs') === 'true',
        privateProfile: localStorage.getItem('privateProfile') === 'true',
        showActivity: localStorage.getItem('showActivity') !== 'false'
    };
    
    // Apply settings to UI
    applySettings(settings);
}

// Apply settings to UI elements
function applySettings(settings) {
    // Theme
    const themeRadios = document.querySelectorAll('input[name="theme"]');
    themeRadios.forEach(radio => {
        if (radio.value === settings.theme) {
            radio.checked = true;
        }
    });
    applyTheme(settings.theme);
    
    // Toggles
    document.getElementById('compactMode').checked = settings.compactMode;
    document.getElementById('showAlbumArt').checked = settings.showAlbumArt;
    document.getElementById('autoplay').checked = settings.autoplay;
    document.getElementById('crossfade').checked = settings.crossfade;
    document.getElementById('gapless').checked = settings.gapless;
    document.getElementById('newMusicNotifs').checked = settings.newMusicNotifs;
    document.getElementById('weeklySummary').checked = settings.weeklySummary;
    document.getElementById('browserNotifs').checked = settings.browserNotifs;
    document.getElementById('privateProfile').checked = settings.privateProfile;
    document.getElementById('showActivity').checked = settings.showActivity;
    
    // Select
    const qualitySelect = document.querySelector('.setting-select');
    if (qualitySelect) {
        qualitySelect.value = settings.streamingQuality;
    }
}

// Attach event listeners to all settings
function attachEventListeners() {
    // Theme selection
    document.querySelectorAll('input[name="theme"]').forEach(radio => {
        radio.addEventListener('change', function() {
            const theme = this.value;
            localStorage.setItem('theme', theme);
            applyTheme(theme);
            showNotification('Theme changed to ' + theme);
        });
    });
    
    // All toggles
    const toggles = [
        'compactMode', 'showAlbumArt', 'autoplay', 'crossfade', 
        'gapless', 'newMusicNotifs', 'weeklySummary', 'browserNotifs',
        'privateProfile', 'showActivity'
    ];
    
    toggles.forEach(id => {
        const toggle = document.getElementById(id);
        if (toggle) {
            toggle.addEventListener('change', function() {
                localStorage.setItem(id, this.checked);
                handleSettingChange(id, this.checked);
            });
        }
    });
    
    // Streaming quality
    const qualitySelect = document.querySelector('.setting-select');
    if (qualitySelect) {
        qualitySelect.addEventListener('change', function() {
            localStorage.setItem('streamingQuality', this.value);
            showNotification('Streaming quality updated');
        });
    }
}

// Apply theme to page
function applyTheme(theme) {
    const root = document.documentElement;
    
    if (theme === 'light') {
        root.style.setProperty('--gray-900', '#f3f4f6');
        root.style.setProperty('--gray-800', '#e5e7eb');
        root.style.setProperty('--gray-700', '#d1d5db');
        // Add more light theme colors
    } else if (theme === 'dark') {
        root.style.setProperty('--gray-900', '#111827');
        root.style.setProperty('--gray-800', '#1f2937');
        root.style.setProperty('--gray-700', '#374151');
    } else if (theme === 'auto') {
        // Detect system preference
        if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
            applyTheme('dark');
        } else {
            applyTheme('light');
        }
    }
}

// Handle individual setting changes
function handleSettingChange(setting, value) {
    switch(setting) {
        case 'compactMode':
            document.body.classList.toggle('compact-mode', value);
            showNotification(value ? 'Compact mode enabled' : 'Compact mode disabled');
            break;
            
        case 'showAlbumArt':
            showNotification(value ? 'Album art shown' : 'Album art hidden');
            break;
            
        case 'autoplay':
            showNotification(value ? 'Autoplay enabled' : 'Autoplay disabled');
            break;
            
        case 'crossfade':
            showNotification(value ? 'Crossfade enabled' : 'Crossfade disabled');
            break;
            
        case 'browserNotifs':
            if (value && 'Notification' in window) {
                Notification.requestPermission();
            }
            showNotification(value ? 'Browser notifications enabled' : 'Browser notifications disabled');
            break;
            
        default:
            showNotification('Setting updated');
    }
}

// Show notification (simple implementation)
function showNotification(message) {
    // Check if there's already a notification
    let notification = document.querySelector('.settings-notification');
    
    if (!notification) {
        notification = document.createElement('div');
        notification.className = 'settings-notification';
        document.body.appendChild(notification);
    }
    
    notification.textContent = message;
    notification.classList.add('show');
    
    setTimeout(() => {
        notification.classList.remove('show');
    }, 3000);
}

// Confirm account deletion
function confirmDeleteAccount() {
    const confirmed = confirm(
        'Are you sure you want to delete your account?\n\n' +
        'This action cannot be undone. All your data will be permanently deleted.'
    );
    
    if (confirmed) {
        const doubleConfirm = confirm(
            'This is your last chance!\n\n' +
            'Click OK to permanently delete your account.'
        );
        
        if (doubleConfirm) {
            // Redirect to delete account endpoint
            window.location.href = 'api/settings/delete-account.php';
        }
    }
}

// Add notification styles dynamically
const style = document.createElement('style');
style.textContent = `
    .settings-notification {
        position: fixed;
        bottom: 2rem;
        right: 2rem;
        background: rgba(147, 51, 234, 0.9);
        color: white;
        padding: 1rem 1.5rem;
        border-radius: 0.75rem;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
        transform: translateY(100px);
        opacity: 0;
        transition: all 0.3s;
        z-index: 1000;
    }
    
    .settings-notification.show {
        transform: translateY(0);
        opacity: 1;
    }
    
    .compact-mode .song-item {
        padding: 0.5rem 1rem !important;
    }
    
    .compact-mode .song-cover {
        width: 2rem !important;
        height: 2rem !important;
        font-size: 1rem !important;
    }
`;
document.head.appendChild(style);