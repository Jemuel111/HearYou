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
            1 => ['id' => 1,  'title' => 'Multo',                'artist' => 'Cup of Joe',          'emotion' => 'sad',       'cover' => '👻',  'duration' => '3:20', 'album' => 'Ghost Stories',      'release_year' => 2025, 'genre' => 'OPM'],
            2 => ['id' => 2,  'title' => 'Tibok',                'artist' => 'Earl Agustin',       'emotion' => 'energetic','cover' => '❤️',  'duration' => '2:58', 'album' => 'Heartbeat',          'release_year' => 2025, 'genre' => 'Pop'],
            3 => ['id' => 3,  'title' => 'Marilag',              'artist' => 'Dionela',            'emotion' => 'happy',     'cover' => '🌸',  'duration' => '3:05', 'album' => 'Spring Vibes',       'release_year' => 2025, 'genre' => 'Pop'],
            4 => ['id' => 4,  'title' => 'Sa Bawat Sandali',     'artist' => 'Amiel Sol',          'emotion' => 'calm',      'cover' => '🌅',  'duration' => '3:45', 'album' => 'Evening Calm',       'release_year' => 2024, 'genre' => 'Ballad'],
            5 => ['id' => 5,  'title' => 'My Day',               'artist' => 'HELLMERRY',          'emotion' => 'energetic','cover' => '🔥',  'duration' => '3:10', 'album' => 'Rise Up',            'release_year' => 2025, 'genre' => 'Pop Rock'],
            6 => ['id' => 6,  'title' => 'Isa Lang',             'artist' => 'Arthur Nery',        'emotion' => 'sad',       'cover' => '💧',  'duration' => '3:33', 'album' => 'Heartfelt',          'release_year' => 2024, 'genre' => 'R&B'],
            7 => ['id' => 7,  'title' => 'Tingin',               'artist' => 'Cup of Joe & Janine','emotion' => 'happy',    'cover' => '💖',  'duration' => '3:15', 'album' => 'Eyes on You',        'release_year' => 2025, 'genre' => 'Pop'],
            8 => ['id' => 8,  'title' => 'Saksi Ang Langit',     'artist' => 'December Avenue',    'emotion' => 'sad',       'cover' => '🌧️',  'duration' => '4:02', 'album' => 'Sky Witness',        'release_year' => 2024, 'genre' => 'Alternative Rock'],
            9 => ['id' => 9,  'title' => 'Youll Be In My Heart', 'artist' => 'NIKI',               'emotion' => 'calm',      'cover' => '💙',  'duration' => '3:40', 'album' => 'Blue Heart',         'release_year' => 2024, 'genre' => 'R&B'],
            10 => ['id' => 10,'title' => 'Back to Friends',      'artist' => 'Sombr',              'emotion' => 'calm',      'cover' => '🤍',  'duration' => '3:25', 'album' => 'Chill Nights',       'release_year' => 2025, 'genre' => 'Lo-Fi'],
            11 => ['id' => 11,'title' => 'Palagi',               'artist' => 'TJ Monterde & KZ Tandingan','emotion'=>'happy','cover'=>'🌞','duration'=>'3:50','album'=>'Always','release_year'=>2024,'genre'=>'Pop'],
            12 => ['id' => 12,'title' => 'Dilaw',                'artist' => 'Maki',               'emotion' => 'happy',     'cover' => '🌼',  'duration' => '3:22', 'album' => 'Yellow Days',        'release_year' => 2025, 'genre' => 'Pop'],
            13 => ['id' => 13,'title' => 'Blink Twice',          'artist' => 'BINI',               'emotion' => 'energetic','cover' => '✨',  'duration' => '2:50', 'album' => 'Shining',            'release_year' => 2025, 'genre' => 'Dance Pop'],
            14 => ['id' => 14,'title' => 'DAM',                  'artist' => 'SB19',               'emotion' => 'energetic','cover' => '⚡️',  'duration' => '3:30', 'album' => 'Power Moves',        'release_year' => 2025, 'genre' => 'Pop'],
            15 => ['id' => 15,'title' => 'Time',                 'artist' => 'SB19',               'emotion' => 'calm',      'cover' => '🕰️',  'duration' => '3:45', 'album' => 'Timeless',           'release_year' => 2024, 'genre' => 'Ballad'],
            16 => ['id' => 16,'title' => 'Dungka!',              'artist' => 'SB19',               'emotion' => 'energetic','cover' => '🎶',  'duration' => '3:35', 'album' => 'Rhythm',             'release_year' => 2025, 'genre' => 'Pop'],
            17 => ['id' => 17,'title' => 'Karera',               'artist' => 'BINI',               'emotion' => 'energetic','cover' => '🏁',  'duration' => '3:10', 'album' => 'Race',               'release_year' => 2025, 'genre' => 'Dance Pop'],
            18 => ['id' => 18,'title' => 'Pantropiko',           'artist' => 'BINI',               'emotion' => 'happy',     'cover' => '🇵🇭',  'duration' => '3:27', 'album' => 'Patriotic Beats',    'release_year' => 2025, 'genre' => 'Pop'],
            19 => ['id' => 19,'title' => 'Salamin, Salamin',     'artist' => 'BINI',               'emotion' => 'calm',      'cover' => '🪞',  'duration' => '3:45', 'album' => 'Mirror',             'release_year' => 2025, 'genre' => 'Pop'],
            20 => ['id' => 20,'title' => 'Sining',               'artist' => 'Dionela & Jay R',    'emotion' => 'sad',       'cover' => '🎨',  'duration' => '3:55', 'album' => 'Artistry',           'release_year' => 2024, 'genre' => 'R&B'],
            21 => ['id' => 21,'title' => 'Walang Alam',          'artist' => 'Hev Abi',            'emotion' => 'sad',       'cover' => '😔',  'duration' => '3:30', 'album' => 'Lost Thoughts',      'release_year' => 2025, 'genre' => 'OPM'],
            22 => ['id' => 22,'title' => 'Babaero',              'artist' => 'gins&melodies & Hev Abi','emotion'=>'sad','cover'=>'💔','duration'=>'3:40','album'=>'Cheating Heart','release_year'=>2025,'genre'=>'OPM'],
            23 => ['id' => 23,'title' => 'Makasarili Malambing', 'artist' => 'Kristina Dawn & Hev Abi','emotion'=>'sad','cover'=>'🌙','duration'=>'3:50','album'=>'Selfish Love','release_year'=>2025,'genre'=>'Ballad'],
            24 => ['id' => 24,'title' => 'Take All The Love',     'artist' => 'Arthur Nery',        'emotion' => 'calm',      'cover' => '🤲',  'duration' => '3:35', 'album' => 'Love All',           'release_year' => 2024, 'genre' => 'R&B'],
            25 => ['id' => 25,'title' => 'You and I',            'artist' => 'Various Artists',    'emotion' => 'happy',     'cover' => '💞',  'duration' => '3:40', 'album' => 'Duets',              'release_year' => 2025, 'genre' => 'Pop'],
            26 => ['id' => 26,'title' => 'Lets Go',             'artist' => 'Various Artists',    'emotion' => 'energetic','cover' => '🚀',  'duration' => '3:15', 'album' => 'Launch',             'release_year' => 2025, 'genre' => 'Dance'],
            27 => ['id' => 27,'title' => 'Night Drive',          'artist' => 'Various Artists',    'emotion' => 'calm',      'cover' => '🌙',  'duration' => '4:00', 'album' => 'Midnight Ride',      'release_year' => 2024, 'genre' => 'Chill'],
            28 => ['id' => 28,'title' => 'Sunrise',              'artist' => 'Various Artists',    'emotion' => 'happy',     'cover' => '🌄',  'duration' => '3:35', 'album' => 'New Dawn',           'release_year' => 2025, 'genre' => 'Pop'],
            29 => ['id' => 29,'title' => 'Heartbeat',            'artist' => 'Various Artists',    'emotion' => 'energetic','cover' => '💓',  'duration' => '3:20', 'album' => 'Pulse',              'release_year' => 2025, 'genre' => 'Pop'],
            30 => ['id' => 30,'title' => 'Quiet Moments',        'artist' => 'Various Artists',    'emotion' => 'calm',      'cover' => '🌿',  'duration' => '4:10', 'album' => 'Serenity',           'release_year' => 2024, 'genre' => 'Chill']

        ];
        $song = $demoSongs[$song_id] ?? $demoSongs[1];
    }
} catch (PDOException $e) {
    header('Location: index.php');
    exit;
}

// Song descriptions
$descriptions = [
    1 => "A haunting OPM ballad that captures the feeling of loneliness and introspection, with soft vocals layered over melancholic melodies.",
    2 => "An upbeat and energetic pop track that gets your heart racing with lively beats and catchy hooks.",
    3 => "A bright, uplifting song that radiates happiness and optimism, perfect for sunny days and cheerful moments.",
    4 => "A calming ballad that evokes peace and serenity, with gentle instrumentation ideal for relaxation.",
    5 => "A high-energy pop-rock anthem designed to make you move, full of infectious rhythm and excitement.",
    6 => "A soulful R&B track that delves into heartache and longing, with smooth vocals and emotional depth.",
    7 => "A joyful duet that sparks happiness and connection, blending playful vocals with light instrumentation.",
    8 => "An emotional alternative rock song that paints a picture of longing and reflection through rich soundscapes.",
    9 => "A soothing R&B piece with heartfelt lyrics, perfect for quiet evenings and introspective moments.",
    10 => "A mellow chill track that promotes calmness and relaxation, ideal for unwinding after a long day.",
    11 => "A bright and feel-good pop song that spreads warmth and positivity, ideal for uplifting your mood.",
    12 => "A cheerful, playful track with catchy melodies that make you want to sing along and smile.",
    13 => "A vibrant dance-pop track that brings high-energy beats to the dance floor, designed for movement and fun.",
    14 => "An electrifying pop song with strong rhythms and dynamic production, perfect for energizing workouts or parties.",
    15 => "A gentle ballad with calming melodies, providing a sense of timeless peace and reflection.",
    16 => "A high-octane pop song full of rhythmic hooks and infectious energy, perfect for getting pumped.",
    17 => "An upbeat dance track with playful energy and driving beats that keep you moving.",
    18 => "A patriotic pop anthem filled with happiness and pride, celebrating culture and heritage.",
    19 => "A serene pop composition that mirrors reflection and tranquility, perfect for quiet listening sessions.",
    20 => "A soulful R&B collaboration with emotive vocals and artistic expression, exploring deep feelings.",
    21 => "A melancholic OPM song that explores loss and introspection, with haunting melodies and reflective lyrics.",
    22 => "A dramatic and emotional ballad about heartbreak, delivered with passion and raw intensity.",
    23 => "A tender love song that explores vulnerability and longing, with soft and emotive musical arrangement.",
    24 => "A calming R&B track that encourages serenity and self-reflection, with gentle instrumentation.",
    25 => "A joyful pop duet that spreads positivity and happiness through catchy hooks and melodies.",
    26 => "A lively dance track designed to energize listeners and keep them moving with infectious rhythm.",
    27 => "A chill, atmospheric track perfect for late-night drives, blending smooth sounds and ambient textures.",
    28 => "A bright pop track celebrating new beginnings and positivity, with uplifting melodies.",
    29 => "A pulsating, energetic pop song designed to get listeners' hearts racing and bodies moving.",
    30 => "A tranquil composition filled with soft melodies and gentle harmonies, ideal for relaxation and mindfulness."

];

$artist_bios = [
    'Cup of Joe' => "Cup of Joe is a Filipino artist known for soulful OPM ballads that explore love, loss, and everyday emotions with heartfelt lyrics and warm melodies.",
    'Earl Agustin' => "Earl Agustin produces energetic pop tracks with catchy hooks and uplifting rhythms that get listeners moving and singing along.",
    'Dionela' => "Dionela is a versatile pop artist blending cheerful melodies with expressive vocals to create joyful, feel-good music.",
    'Amiel Sol' => "Amiel Sol crafts calming ballads and reflective compositions that evoke peace and introspection through gentle instrumentation.",
    'HELLMERRY' => "HELLMERRY delivers high-energy pop-rock songs with infectious beats, designed to energize and excite audiences.",
    'Arthur Nery' => "Arthur Nery's R&B style is intimate and soulful, exploring themes of love, longing, and vulnerability with smooth vocals.",
    'Cup of Joe & Janine' => "This duo combines expressive vocals and playful harmonies to create uplifting pop music that sparks happiness and connection.",
    'December Avenue' => "December Avenue is a celebrated Filipino alternative rock band known for emotive lyrics and atmospheric compositions exploring love and heartbreak.",
    'NIKI' => "NIKI is an internationally acclaimed R&B/pop artist whose music blends soulful vocals with smooth, reflective production.",
    'Sombr' => "Sombr creates chill, calming tracks perfect for relaxation and introspection, blending modern lo-fi sounds with melodic sensibility.",
    'TJ Monterde & KZ Tandingan' => "TJ Monterde & KZ Tandingan are dynamic OPM artists whose collaborative work blends pop sensibilities with heartfelt vocal performances.",
    'Maki' => "Maki produces bright and playful pop music that radiates happiness and lightheartedness, perfect for cheerful listening moments.",
    'BINI' => "BINI is a rising Filipino girl group delivering dance-pop and energetic tracks that are vibrant, catchy, and fun.",
    'SB19' => "SB19 is a globally recognized P-pop boy group known for powerful performances, dynamic pop songs, and high-energy anthems.",
    'Dionela & Jay R' => "Dionela & Jay R collaborate to produce soulful R&B tracks that explore deep emotions with expressive vocals and smooth arrangements.",
    'Hev Abi' => "Hev Abi is an OPM artist whose music delves into heartache and introspection, offering raw and emotive lyrical storytelling.",
    'gins&melodies & Hev Abi' => "This collaboration produces heartfelt OPM ballads, combining expressive vocals with rich melodic arrangements.",
    'Kristina Dawn & Hev Abi' => "Kristina Dawn & Hev Abi are known for tender and emotive duets exploring vulnerability, love, and human connection.",
    'Various Artists' => "Various Artists represents a diverse collection of Filipino musicians producing pop, chill, and dance tracks that span multiple moods and styles."

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