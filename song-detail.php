<?php
// song-detail.php - Song Detail Page
session_start();
require_once 'config/database.php';
require_once 'includes/functions.php';

// Get song ID from URL
$song_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($song_id === 0) {
    header('Location: index.php');
    exit;
}

// Initialize database
$database = new Database();
$db = $database->getConnection();

// Fetch song details
try {
    $query = "SELECT * FROM songs WHERE id = :id LIMIT 1";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':id', $song_id);
    $stmt->execute();
    $song = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$song) {
        // Fallback to demo data
        $demoSongs = [
            1 => ['id' => 1, 'title' => 'Midnight Rain', 'artist' => 'Luna Echo', 'emotion' => 'sad', 'cover' => '🌧️', 'duration' => '3:45', 'album' => 'Night Sessions', 'release_year' => 2023, 'genre' => 'Indie Pop'],
            2 => ['id' => 2, 'title' => 'Summer Vibes', 'artist' => 'DJ Sunshine', 'emotion' => 'happy', 'cover' => '☀️', 'duration' => '3:20', 'album' => 'Bright Days', 'release_year' => 2024, 'genre' => 'Electronic'],
            3 => ['id' => 3, 'title' => 'Deep Thoughts', 'artist' => 'Mind Wave', 'emotion' => 'calm', 'cover' => '🌊', 'duration' => '4:12', 'album' => 'Meditation', 'release_year' => 2023, 'genre' => 'Ambient'],
            4 => ['id' => 4, 'title' => 'Energy Burst', 'artist' => 'Power Pulse', 'emotion' => 'energetic', 'cover' => '⚡', 'duration' => '2:58', 'album' => 'Pump Up', 'release_year' => 2024, 'genre' => 'EDM'],
            5 => ['id' => 5, 'title' => 'Lonely Nights', 'artist' => 'Soul Singer', 'emotion' => 'sad', 'cover' => '🌙', 'duration' => '4:30', 'album' => 'Heartbreak Hotel', 'release_year' => 2022, 'genre' => 'R&B'],
            6 => ['id' => 6, 'title' => 'Party Time', 'artist' => 'Beat Masters', 'emotion' => 'happy', 'cover' => '🎉', 'duration' => '3:15', 'album' => 'Dance Floor', 'release_year' => 2024, 'genre' => 'Dance'],
            7 => ['id' => 7, 'title' => 'Morning Peace', 'artist' => 'Zen Garden', 'emotion' => 'calm', 'cover' => '🍃', 'duration' => '5:00', 'album' => 'Tranquility', 'release_year' => 2023, 'genre' => 'New Age'],
            8 => ['id' => 8, 'title' => 'Workout Mix', 'artist' => 'Fit Beats', 'emotion' => 'energetic', 'cover' => '🏃', 'duration' => '3:40', 'album' => 'Gym Motivation', 'release_year' => 2024, 'genre' => 'Hip Hop'],
            9 => ['id' => 9, 'title' => 'Heartbreak Blues', 'artist' => 'Emotion Express', 'emotion' => 'sad', 'cover' => '💔', 'duration' => '4:15', 'album' => 'Tears & Rain', 'release_year' => 2023, 'genre' => 'Blues'],
            10 => ['id' => 10, 'title' => 'Feel Good', 'artist' => 'Happy Souls', 'emotion' => 'happy', 'cover' => '😊', 'duration' => '3:30', 'album' => 'Positive Vibes', 'release_year' => 2024, 'genre' => 'Pop'],
            11 => ['id' => 11, 'title' => 'Meditation Flow', 'artist' => 'Inner Peace', 'emotion' => 'calm', 'cover' => '🧘', 'duration' => '6:00', 'album' => 'Zen Masters', 'release_year' => 2023, 'genre' => 'Meditation'],
            12 => ['id' => 12, 'title' => 'Adrenaline Rush', 'artist' => 'Extreme Sports', 'emotion' => 'energetic', 'cover' => '🎸', 'duration' => '3:25', 'album' => 'High Octane', 'release_year' => 2024, 'genre' => 'Rock']
        ];
        $song = $demoSongs[$song_id] ?? $demoSongs[1];
    }
} catch (PDOException $e) {
    header('Location: index.php');
    exit;
}

// Song descriptions
$descriptions = [
    1 => "A melancholic journey through rain-soaked streets and memories. This indie pop masterpiece captures the essence of solitude and reflection, with haunting vocals layered over atmospheric production.",
    2 => "An infectious electronic anthem that embodies the carefree spirit of summer. Bright synths and uplifting melodies create the perfect soundtrack for beach days and sunset drives.",
    3 => "Ambient soundscapes that transport you to a place of inner peace. Gentle waves of synthesizers and natural sounds create a meditative experience perfect for relaxation and contemplation.",
    4 => "High-energy EDM track designed to get your adrenaline pumping. Explosive drops and pulsating beats make this the ultimate workout and party anthem.",
    5 => "A soul-stirring R&B ballad about heartache and longing. Smooth vocals over minimalist production create an intimate atmosphere that resonates with anyone who's experienced loss.",
    6 => "Pure dancefloor energy captured in three minutes. This track's infectious rhythm and catchy hooks guarantee to get everyone moving and having a great time.",
    7 => "A tranquil new age composition perfect for morning meditation. Delicate piano melodies blend with nature sounds to create a peaceful awakening experience.",
    8 => "Hard-hitting hip hop beats engineered for maximum motivation. This track's aggressive energy and powerful rhythm push you to exceed your limits during any workout.",
    9 => "Classic blues meets modern production in this emotional journey through heartbreak. Raw, honest lyrics delivered with soulful passion over traditional blues progressions.",
    10 => "An uplifting pop anthem celebrating life's simple joys. Catchy melodies and positive lyrics create an instantly mood-boosting experience.",
    11 => "Extended meditation piece designed for deep relaxation and mindfulness practice. Slowly evolving soundscapes guide you into a state of profound calm.",
    12 => "Raw, unfiltered rock energy that captures the thrill of extreme sports. Driving guitars and thunderous drums create an unstoppable force of musical adrenaline."
];

$artist_bios = [
    'Luna Echo' => "Luna Echo is an indie pop artist known for her ethereal vocals and introspective lyrics. Her music explores themes of memory, emotion, and the human experience.",
    'DJ Sunshine' => "DJ Sunshine brings pure positive energy to the electronic music scene. Known for infectious beats and feel-good vibes that light up festivals worldwide.",
    'Mind Wave' => "Mind Wave creates immersive sonic experiences that blend ambient music with meditative soundscapes. Their work is designed to facilitate relaxation and inner exploration.",
    'Power Pulse' => "Power Pulse is an EDM powerhouse delivering high-energy tracks that dominate dancefloors and workout playlists globally.",
    'Soul Singer' => "Soul Singer's voice carries the weight of genuine emotion. Their R&B style combines classic soul influences with modern production.",
    'Beat Masters' => "Beat Masters are pioneers in the dance music scene, crafting irresistible rhythms that have defined a generation of club anthems.",
    'Zen Garden' => "Zen Garden specializes in new age compositions that transport listeners to places of tranquility and peace.",
    'Fit Beats' => "Fit Beats produces motivational music specifically designed for athletes and fitness enthusiasts.",
    'Emotion Express' => "Emotion Express channels raw feelings into powerful blues performances that resonate with audiences worldwide.",
    'Happy Souls' => "Happy Souls create uplifting pop music with a mission to spread joy and positivity through every song.",
    'Inner Peace' => "Inner Peace develops extended meditation compositions used by practitioners around the world for deep mindfulness work.",
    'Extreme Sports' => "Extreme Sports captures the adrenaline and intensity of action sports through powerful rock music."
];

$song['description'] = $descriptions[$song_id] ?? "A beautiful musical composition that captures the essence of " . $song['emotion'] . " emotions.";
$song['artist_bio'] = $artist_bios[$song['artist']] ?? $song['artist'] . " is a talented artist creating amazing music.";

$isLoggedIn = isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($song['title']); ?> - <?php echo htmlspecialchars($song['artist']); ?> | HearYou</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/song-detail.css">
</head>
<body class="song-detail-page">
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

    <div class="song-detail-container">
        <!-- Hero Section -->
        <section class="song-hero">
            <div class="hero-background"></div>
            <div class="hero-content">
                <div class="song-cover-large">
                    <?php echo $song['cover']; ?>
                </div>
                <div class="song-info-main">
                    <div class="song-badge"><?php echo ucfirst($song['emotion']); ?></div>
                    <h1 class="song-title-large"><?php echo htmlspecialchars($song['title']); ?></h1>
                    <h2 class="song-artist-large"><?php echo htmlspecialchars($song['artist']); ?></h2>
                    <div class="song-meta">
                        <span><?php echo $song['album'] ?? 'Single'; ?></span>
                        <span>•</span>
                        <span><?php echo $song['release_year'] ?? date('Y'); ?></span>
                        <span>•</span>
                        <span><?php echo $song['duration']; ?></span>
                        <span>•</span>
                        <span><?php echo $song['genre'] ?? 'Music'; ?></span>
                    </div>
                    <div class="song-actions-main">
                        <button class="btn-play-large" onclick="playSong(<?php echo $song['id']; ?>)">
                            <svg viewBox="0 0 24 24" fill="currentColor">
                                <polygon points="5 3 19 12 5 21 5 3"/>
                            </svg>
                            Play Song
                        </button>
                        <button class="btn-favorite-large" onclick="toggleFavorite(<?php echo $song['id']; ?>)">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z" stroke-width="2"/>
                            </svg>
                            Add to Favorites
                        </button>
                        <button class="btn-share" onclick="shareSong()">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <circle cx="18" cy="5" r="3" stroke-width="2"/>
                                <circle cx="6" cy="12" r="3" stroke-width="2"/>
                                <circle cx="18" cy="19" r="3" stroke-width="2"/>
                                <line x1="8.59" y1="13.51" x2="15.42" y2="17.49" stroke-width="2"/>
                                <line x1="15.41" y1="6.51" x2="8.59" y2="10.49" stroke-width="2"/>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </section>

        <!-- Main Content -->
        <div class="song-content">
            <!-- About Section -->
            <section class="content-section">
                <h3>About This Song</h3>
                <p class="song-description"><?php echo htmlspecialchars($song['description']); ?></p>
            </section>

            <!-- Artist Section -->
            <section class="content-section artist-section">
                <h3>About <?php echo htmlspecialchars($song['artist']); ?></h3>
                <div class="artist-card">
                    <div class="artist-avatar">
                        <?php echo mb_substr($song['artist'], 0, 1); ?>
                    </div>
                    <div class="artist-info">
                        <h4><?php echo htmlspecialchars($song['artist']); ?></h4>
                        <p><?php echo htmlspecialchars($song['artist_bio']); ?></p>
                        <button class="btn-follow">Follow Artist</button>
                    </div>
                </div>
            </section>

            <!-- Lyrics Section -->
            <section class="content-section lyrics-section">
                <h3>Lyrics</h3>
                <div class="lyrics-content">
                    <p class="lyrics-line">[Verse 1]</p>
                    <p class="lyrics-line">Through the midnight rain I walk alone</p>
                    <p class="lyrics-line">Every drop reminds me of what's gone</p>
                    <p class="lyrics-line">Searching for the light in endless dark</p>
                    <p class="lyrics-line">Memories that leave their mark</p>
                    <br>
                    <p class="lyrics-line">[Chorus]</p>
                    <p class="lyrics-line">And I'm feeling this emotion</p>
                    <p class="lyrics-line">Like waves upon the ocean</p>
                    <p class="lyrics-line">Let the music take me higher</p>
                    <p class="lyrics-line">Set my soul on fire</p>
                    <br>
                    <p class="lyrics-note">🎵 Full lyrics available for premium members</p>
                </div>
            </section>

            <!-- Stats Section -->
            <section class="content-section stats-section">
                <h3>Song Statistics</h3>
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-icon">▶️</div>
                        <div class="stat-value">1.2M</div>
                        <div class="stat-label">Plays</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon">❤️</div>
                        <div class="stat-value">45K</div>
                        <div class="stat-label">Favorites</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon">📋</div>
                        <div class="stat-value">23K</div>
                        <div class="stat-label">In Playlists</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon">🔄</div>
                        <div class="stat-value">8.5K</div>
                        <div class="stat-label">Shares</div>
                    </div>
                </div>
            </section>

            <!-- Similar Songs Section -->
            <section class="content-section">
                <h3>More <?php echo ucfirst($song['emotion']); ?> Songs</h3>
                <div class="similar-songs" id="similarSongs"></div>
            </section>
        </div>
    </div>

    <script src="assets/js/song-detail.js"></script>
    <script src="assets/js/player.js"></script>
    <script>
        const currentSong = <?php echo json_encode($song); ?>;
        
        function playSong(songId) {
            // Load the song in the player and redirect to main app
            const song = currentSong;
            
            // Store song in sessionStorage to play it on the main app
            sessionStorage.setItem('playSong', JSON.stringify(song));
            
            // Redirect to main app
            window.location.href = 'index.php?play=' + songId;
        }

        function toggleFavorite(songId) {
            <?php if ($isLoggedIn): ?>
                // Add to favorites logic
                let favorites = JSON.parse(localStorage.getItem('favorites')) || [];
                const index = favorites.indexOf(songId);
                
                if (index > -1) {
                    favorites.splice(index, 1);
                    alert('Removed from favorites!');
                } else {
                    favorites.push(songId);
                    alert('Added to favorites!');
                }
                
                localStorage.setItem('favorites', JSON.stringify(favorites));
            <?php else: ?>
                if (confirm('Please login to add songs to favorites. Go to login page?')) {
                    window.location.href = 'auth.php';
                }
            <?php endif; ?>
        }

        function shareSong() {
            const url = window.location.href;
            if (navigator.share) {
                navigator.share({
                    title: currentSong.title,
                    text: 'Check out this song on HearYou: ' + currentSong.title + ' by ' + currentSong.artist,
                    url: url
                }).catch(() => {
                    // Fallback if share fails
                    copyToClipboard(url);
                });
            } else {
                copyToClipboard(url);
            }
        }
        
        function copyToClipboard(text) {
            navigator.clipboard.writeText(text).then(() => {
                alert('Link copied to clipboard!');
            }).catch(() => {
                // Fallback for older browsers
                const input = document.createElement('input');
                input.value = text;
                document.body.appendChild(input);
                input.select();
                document.execCommand('copy');
                document.body.removeChild(input);
                alert('Link copied to clipboard!');
            });
        }

        // Load similar songs
        document.addEventListener('DOMContentLoaded', function() {
            loadSimilarSongs('<?php echo $song['emotion']; ?>', <?php echo $song['id']; ?>);
        });
    </script>
</body>
</html>