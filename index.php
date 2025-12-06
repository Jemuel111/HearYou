<?php
// index.php - Main Entry Point

// Enable error reporting for debugging (remove in production)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Start session
session_start();

// Check if required files exist
if (!file_exists('config/database.php')) {
    die('Error: config/database.php not found. Please check your file structure.');
}

if (!file_exists('includes/functions.php')) {
    die('Error: includes/functions.php not found. Please check your file structure.');
}

// Include required files
require_once 'config/database.php';
require_once 'includes/functions.php';

// Initialize user session if not exists
if (!isset($_SESSION['user_id'])) {
    $_SESSION['user_id'] = uniqid('user_', true);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HearYou - Emotion-Based Music Streaming</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/player.css">
    <link rel="stylesheet" href="assets/css/chat.css">
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
            <?php if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true): ?>
                <div class="user-menu">
                    <button class="user-avatar" onclick="toggleUserMenu()">
                        <span><?php echo strtoupper(substr($_SESSION['username'], 0, 1)); ?></span>
                    </button>
                    <div class="user-dropdown" id="userDropdown">
                        <div class="user-info">
                            <strong><?php echo htmlspecialchars($_SESSION['full_name']); ?></strong>
                            <span><?php echo htmlspecialchars($_SESSION['email']); ?></span>
                        </div>
                        <hr>
                        <a href="settings.php">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <circle cx="12" cy="12" r="3" stroke-width="2"/>
                                <path d="M12 1v6m0 6v6M5.64 5.64l4.24 4.24m4.24 4.24l4.24 4.24M1 12h6m6 0h6M5.64 18.36l4.24-4.24m4.24-4.24l4.24-4.24" stroke-width="2"/>
                            </svg>
                            Settings
                        </a>
                        <a href="api/auth/logout.php">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4M16 17l5-5-5-5M21 12H9" stroke-width="2"/>
                            </svg>
                            Logout
                        </a>
                    </div>
                </div>
            <?php else: ?>
                <a href="auth.php" class="btn-login">Login</a>
            <?php endif; ?>
            <button class="chat-toggle" onclick="toggleChat()">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z" stroke-width="2"/>
                </svg>
            </button>
        </div>
    </header>

    <div class="main-container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <nav class="main-nav">
                <button class="nav-btn active" onclick="showView('home')">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z" stroke-width="2"/>
                    </svg>
                    <span>Home</span>
                </button>
                <button class="nav-btn" onclick="showView('search')">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <circle cx="11" cy="11" r="8" stroke-width="2"/>
                        <path d="m21 21-4.35-4.35" stroke-width="2"/>
                    </svg>
                    <span>Search</span>
                </button>
                <button class="nav-btn" onclick="showView('library')">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20M4 19.5A2.5 2.5 0 0 0 6.5 22H20M4 19.5V4.5A2.5 2.5 0 0 1 6.5 2H20v20H6.5" stroke-width="2"/>
                    </svg>
                    <span>Your Library</span>
                </button>
            </nav>

            <div class="mood-filter">
                <h3>FILTER BY MOOD</h3>
                <button class="mood-btn sad" onclick="filterByMood('sad')">
                    <span class="mood-icon">😢</span>
                    <span>Sad</span>
                </button>
                <button class="mood-btn happy" onclick="filterByMood('happy')">
                    <span class="mood-icon">😊</span>
                    <span>Happy</span>
                </button>
                <button class="mood-btn calm" onclick="filterByMood('calm')">
                    <span class="mood-icon">😌</span>
                    <span>Calm</span>
                </button>
                <button class="mood-btn energetic" onclick="filterByMood('energetic')">
                    <span class="mood-icon">⚡</span>
                    <span>Energetic</span>
                </button>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="content" id="mainContent">
            <!-- Home View -->
            <div id="homeView" class="view active">
                <h2>Discover Your Mood</h2>
                <p class="subtitle">Let your emotions guide your music. Choose a mood or chat with our AI for personalized recommendations.</p>
                
                <div class="mood-grid">
                    <button class="mood-card sad" onclick="filterByMood('sad')">
                        <div class="mood-card-icon">😢</div>
                        <div class="mood-card-title">Sad</div>
                    </button>
                    <button class="mood-card happy" onclick="filterByMood('happy')">
                        <div class="mood-card-icon">😊</div>
                        <div class="mood-card-title">Happy</div>
                    </button>
                    <button class="mood-card calm" onclick="filterByMood('calm')">
                        <div class="mood-card-icon">😌</div>
                        <div class="mood-card-title">Calm</div>
                    </button>
                    <button class="mood-card energetic" onclick="filterByMood('energetic')">
                        <div class="mood-card-icon">⚡</div>
                        <div class="mood-card-title">Energetic</div>
                    </button>
                </div>

                <h3>All Songs</h3>
                <div id="allSongsList" class="song-list"></div>
            </div>

            <!-- Filtered View -->
            <div id="filteredView" class="view">
                <div id="filteredHeader"></div>
                <div id="filteredSongsList" class="song-list"></div>
            </div>

            <!-- Search View -->
            <div id="searchView" class="view">
                <div class="search-container">
                    <h2>Search</h2>
                    <div class="search-box">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" class="search-icon">
                            <circle cx="11" cy="11" r="8" stroke-width="2"/>
                            <path d="m21 21-4.35-4.35" stroke-width="2"/>
                        </svg>
                        <input 
                            type="text" 
                            id="searchInput" 
                            placeholder="Search for songs, artists, or moods..." 
                            autocomplete="off"
                        />
                    </div>
                </div>
                <div id="searchResultsHeader"></div>
                <div id="searchResultsList" class="song-list"></div>
            </div>

            <!-- Library View -->
            <div id="libraryView" class="view">
                <h2>Your Library</h2>
                <p class="subtitle">Your music collection in one place</p>

                <!-- Quick Stats -->
                <div class="library-stats">
                    <div class="stat-box">
                        <div class="stat-number" id="totalSongsCount">0</div>
                        <div class="stat-label">Total Songs</div>
                    </div>
                    <div class="stat-box">
                        <div class="stat-number" id="favoritesCount">0</div>
                        <div class="stat-label">Favorites</div>
                    </div>
                    <div class="stat-box">
                        <div class="stat-number" id="playlistsCount">0</div>
                        <div class="stat-label">Playlists</div>
                    </div>
                    <div class="stat-box">
                        <div class="stat-number" id="hoursListened">0h</div>
                        <div class="stat-label">Time Listened</div>
                    </div>
                </div>

                <!-- Playlists Section -->
                <div class="library-section">
                    <div class="section-header-flex">
                        <h3>Your Playlists</h3>
                        <button class="btn-see-all" onclick="showAllPlaylists()">See All</button>
                    </div>
                    <div id="playlistsGrid" class="playlists-grid"></div>
                </div>

                <!-- Favorites Section -->
                <div class="library-section">
                    <div class="section-header-flex">
                        <h3>Favorite Songs</h3>
                        <button class="btn-see-all" onclick="showAllFavorites()">See All</button>
                    </div>
                    <div id="favoritesList" class="library-grid"></div>
                </div>

                <!-- Recently Played -->
                <div class="library-section">
                    <div class="section-header-flex">
                        <h3>Recently Played</h3>
                        <button class="btn-see-all" onclick="showAllRecentlyPlayed()">See All</button>
                    </div>
                    <div id="recentlyPlayedList" class="library-grid"></div>
                </div>

                <!-- Most Played -->
                <div class="library-section">
                    <div class="section-header-flex">
                        <h3>Most Played</h3>
                        <button class="btn-see-all" onclick="showAllMostPlayed()">See All</button>
                    </div>
                    <div id="mostPlayedList" class="library-grid"></div>
                </div>
            </div>
        </main>

        <!-- AI Chat Panel -->
        <aside class="chat-panel" id="chatPanel">
            <div class="chat-header">
                <h3>AI Music Companion</h3>
                <button class="close-chat" onclick="toggleChat()">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <path d="M18 6L6 18M6 6l12 12" stroke-width="2"/>
                    </svg>
                </button>
            </div>
            <div class="chat-messages" id="chatMessages"></div>
            <div class="chat-input">
                <input type="text" id="chatInput" placeholder="How are you feeling?" />
                <button onclick="sendMessage()">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <path d="M22 2L11 13M22 2l-7 20-4-9-9-4 20-7z" stroke-width="2"/>
                    </svg>
                </button>
            </div>
        </aside>
    </div>

    <!-- Music Player -->
    <footer class="player" id="musicPlayer" style="display: none;">
        <div class="player-info">
            <div class="player-cover" id="playerCover"></div>
            <div class="player-details">
                <div class="player-title" id="playerTitle">Song Title</div>
                <div class="player-artist" id="playerArtist">Artist Name</div>
            </div>
            <button class="favorite-btn" id="favoriteBtn" onclick="toggleFavorite()">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z" stroke-width="2"/>
                </svg>
            </button>
        </div>

        <div class="player-controls">
            <div class="control-buttons">
                <button onclick="previousSong()">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <polygon points="19 20 9 12 19 4 19 20" stroke-width="2"/>
                        <line x1="5" y1="19" x2="5" y2="5" stroke-width="2"/>
                    </svg>
                </button>
                <button class="play-btn" onclick="togglePlay()">
                    <svg id="playIcon" viewBox="0 0 24 24" fill="currentColor">
                        <polygon points="5 3 19 12 5 21 5 3"/>
                    </svg>
                    <svg id="pauseIcon" viewBox="0 0 24 24" fill="currentColor" style="display: none;">
                        <rect x="6" y="4" width="4" height="16"/>
                        <rect x="14" y="4" width="4" height="16"/>
                    </svg>
                </button>
                <button onclick="nextSong()">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <polygon points="5 4 15 12 5 20 5 4" stroke-width="2"/>
                        <line x1="19" y1="5" x2="19" y2="19" stroke-width="2"/>
                    </svg>
                </button>
            </div>
            <div class="progress-bar">
                <span class="time-current">0:00</span>
                <div class="progress">
                    <div class="progress-fill" id="progressFill"></div>
                </div>
                <span class="time-total" id="timeTotal">0:00</span>
            </div>
        </div>

        <div class="player-volume">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                <polygon points="11 5 6 9 2 9 2 15 6 15 11 19 11 5" stroke-width="2"/>
                <path d="M19.07 4.93a10 10 0 0 1 0 14.14M15.54 8.46a5 5 0 0 1 0 7.07" stroke-width="2"/>
            </svg>
            <div class="volume-bar">
                <div class="volume-fill"></div>
            </div>
        </div>
    </footer>

    <script src="assets/js/app.js?v=<?php echo time(); ?>"></script>
    <script src="assets/js/player.js?v=<?php echo time(); ?>"></script>
    <script src="assets/js/chat.js?v=<?php echo time(); ?>"></script>
    <script>
        // Toggle user dropdown menu
        function toggleUserMenu() {
            const dropdown = document.getElementById('userDropdown');
            dropdown.classList.toggle('show');
        }

        // Close dropdown when clicking outside
        document.addEventListener('click', function(event) {
            const userMenu = document.querySelector('.user-menu');
            if (userMenu && !userMenu.contains(event.target)) {
                const dropdown = document.getElementById('userDropdown');
                if (dropdown) {
                    dropdown.classList.remove('show');
                }
            }
        });

        // Check if we should auto-play a song
        document.addEventListener('DOMContentLoaded', function() {
            // Check URL parameter
            const urlParams = new URLSearchParams(window.location.search);
            const playSongId = urlParams.get('play');
            
            if (playSongId) {
                // Wait for songs to load, then play
                setTimeout(() => {
                    const song = allSongs.find(s => s.id == playSongId);
                    if (song) {
                        loadSongInPlayer(song);
                    }
                }, 1000);
            }
            
            // Check sessionStorage for song to play
            const storedSong = sessionStorage.getItem('playSong');
            if (storedSong) {
                try {
                    const song = JSON.parse(storedSong);
                    setTimeout(() => {
                        loadSongInPlayer(song);
                    }, 500);
                    sessionStorage.removeItem('playSong');
                } catch (e) {
                    console.error('Error parsing stored song:', e);
                }
            }
        });

        // Debug: Log when scripts load
        console.log('%c🎵 HearYou App Loaded!', 'color: #9333ea; font-size: 16px; font-weight: bold;');
        console.log('Functions available:', {
            goToSongDetail: typeof goToSongDetail,
            togglePlay: typeof togglePlay,
            loadSongInPlayer: typeof loadSongInPlayer
        });
    </script>
</body>
</html>