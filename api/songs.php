<?php
// api/songs.php - Songs API Endpoint

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');

require_once '../config/database.php';
require_once '../includes/functions.php';

// Initialize database
$database = new Database();
$db = $database->getConnection();

// Get request method
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    // Get all songs or filter by emotion
    $emotion = isset($_GET['emotion']) ? sanitizeInput($_GET['emotion']) : null;
    
    try {
        $query = "SELECT * FROM songs";
        
        if ($emotion) {
            $query .= " WHERE emotion = :emotion";
        }
        
        $query .= " ORDER BY title ASC";
        
        $stmt = $db->prepare($query);
        
        if ($emotion) {
            $stmt->bindParam(':emotion', $emotion);
        }
        
        $stmt->execute();
        $songs = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo json_encode([
            'success' => true,
            'songs' => $songs,
            'count' => count($songs)
        ]);
        
    } catch (PDOException $e) {
        // Fallback to demo data if database fails
        $demoSongs = [
['id' => 1,  'title' => 'Multo',                'artist' => 'Cup of Joe',          'emotion' => 'sad',       'cover' => '👻',  'duration' => '3:20', 'file_path' => 'songs/multo.mp3'],
['id' => 2,  'title' => 'Tibok',                'artist' => 'Earl Agustin',       'emotion' => 'energetic','cover' => '❤️',  'duration' => '2:58', 'file_path' => 'songs/tibok.mp3'],
['id' => 3,  'title' => 'Marilag',              'artist' => 'Dionela',            'emotion' => 'happy',     'cover' => '🌸',  'duration' => '3:05', 'file_path' => 'songs/marilag.mp3'],
['id' => 4,  'title' => 'Sa Bawat Sandali',     'artist' => 'Amiel Sol',          'emotion' => 'calm',      'cover' => '🌅',  'duration' => '3:45', 'file_path' => 'songs/sa_bawat_sandali.mp3'],
['id' => 5,  'title' => 'My Day',               'artist' => 'HELLMERRY',          'emotion' => 'energetic','cover' => '🔥',  'duration' => '3:10', 'file_path' => 'songs/my_day.mp3'],
['id' => 6,  'title' => 'Isa Lang',             'artist' => 'Arthur Nery',        'emotion' => 'sad',       'cover' => '💧',  'duration' => '3:33', 'file_path' => 'songs/isa_lang.mp3'],
['id' => 7,  'title' => 'Tingin',               'artist' => 'Cup of Joe & Janine', 'emotion' => 'happy',    'cover' => '💖',  'duration' => '3:15', 'file_path' => 'songs/tingin.mp3'],
['id' => 8,  'title' => 'Saksi Ang Langit',     'artist' => 'December Avenue',    'emotion' => 'sad',       'cover' => '🌧️',  'duration' => '4:02', 'file_path' => 'songs/saksi_ang_langit.mp3'],
['id' => 9,  'title' => 'Youll Be In My Heart', 'artist' => 'NIKI',               'emotion' => 'calm',      'cover' => '💙',  'duration' => '3:40', 'file_path' => 'songs/youll_be_in_my_heart.mp3'],
['id' => 10, 'title' => 'Back to Friends',      'artist' => 'Sombr',              'emotion' => 'calm',      'cover' => '🤍',  'duration' => '3:25', 'file_path' => 'songs/back_to_friends.mp3'],

['id' => 11, 'title' => 'Palagi',               'artist' => 'TJ Monterde & KZ Tandingan','emotion'=>'happy','cover'=>'🌞','duration'=>'3:50','file_path'=>'songs/palagi.mp3'],
['id' => 12, 'title' => 'Dilaw',                'artist' => 'Maki',               'emotion' => 'happy',     'cover' => '🌼',  'duration' => '3:22', 'file_path' => 'songs/dilaw.mp3'],
['id' => 13, 'title' => 'Blink Twice',          'artist' => 'BINI',               'emotion' => 'energetic','cover' => '✨',  'duration' => '2:50', 'file_path' => 'songs/blink_twice.mp3'],
['id' => 14, 'title' => 'DAM',                  'artist' => 'SB19',               'emotion' => 'energetic','cover' => '⚡️',  'duration' => '3:30', 'file_path' => 'songs/dam.mp3'],
['id' => 15, 'title' => 'Time',                 'artist' => 'SB19',               'emotion' => 'calm',      'cover' => '🕰️',  'duration' => '3:45', 'file_path' => 'songs/time.mp3'],
['id' => 16, 'title' => 'Dungka!',              'artist' => 'SB19',               'emotion' => 'energetic','cover' => '🎶',  'duration' => '3:35', 'file_path' => 'songs/dungka.mp3'],
['id' => 17, 'title' => 'Karera',               'artist' => 'BINI',               'emotion' => 'energetic','cover' => '🏁',  'duration' => '3:10', 'file_path' => 'songs/karera.mp3'],
['id' => 18, 'title' => 'Pantropiko',           'artist' => 'BINI',               'emotion' => 'happy',     'cover' => '🇵🇭',  'duration' => '3:27', 'file_path' => 'songs/pantropiko.mp3'],
['id' => 19, 'title' => 'Salamin, Salamin',     'artist' => 'BINI',               'emotion' => 'calm',      'cover' => '🪞',  'duration' => '3:45', 'file_path' => 'songs/salamin_salamin.mp3'],
['id' => 20, 'title' => 'Sining',               'artist' => 'Dionela & Jay R',    'emotion' => 'sad',       'cover' => '🎨',  'duration' => '3:55', 'file_path' => 'songs/sining.mp3'],

['id' => 21, 'title' => 'Walang Alam',          'artist' => 'Hev Abi',            'emotion' => 'sad',       'cover' => '😔',  'duration' => '3:30', 'file_path' => 'songs/walang_alam.mp3'],
['id' => 22, 'title' => 'Babaero',              'artist' => 'gins&melodies & Hev Abi','emotion'=>'sad','cover'=>'💔','duration'=>'3:40','file_path'=>'songs/babaero.mp3'],
['id' => 23, 'title' => 'Makasarili Malambing', 'artist' => 'Kristina Dawn & Hev Abi','emotion'=>'sad','cover'=>'🌙','duration'=>'3:50','file_path'=>'songs/makasarili_malambing.mp3'],
['id' => 24, 'title' => 'Take All The Love',     'artist' => 'Arthur Nery',        'emotion' => 'calm',      'cover' => '🤲',  'duration' => '3:35', 'file_path' => 'songs/take_all_the_love.mp3'],
['id' => 25, 'title' => 'You and I',            'artist' => 'Various Artists',    'emotion' => 'happy',     'cover' => '💞',  'duration' => '3:40', 'file_path' => 'songs/you_and_i.mp3'],   
['id' => 26, 'title' => 'Lets Go',             'artist' => 'Various Artists',    'emotion' => 'energetic','cover' => '🚀',  'duration' => '3:15', 'file_path' => 'songs/lets_go.mp3'],   
['id' => 27, 'title' => 'Night Drive',          'artist' => 'Various Artists',    'emotion' => 'calm',      'cover' => '🌙',  'duration' => '4:00', 'file_path' => 'songs/night_drive.mp3'],  
['id' => 28, 'title' => 'Sunrise',              'artist' => 'Various Artists',    'emotion' => 'happy',     'cover' => '🌄',  'duration' => '3:35', 'file_path' => 'songs/sunrise.mp3'],    
['id' => 29, 'title' => 'Heartbeat',            'artist' => 'Various Artists',    'emotion' => 'energetic','cover' => '💓',  'duration' => '3:20', 'file_path' => 'songs/heartbeat.mp3'],   
['id' => 30, 'title' => 'Quiet Moments',        'artist' => 'Various Artists',    'emotion' => 'calm',      'cover' => '🌿',  'duration' => '4:10', 'file_path' => 'songs/quiet_moments.mp3'], 

        ];
        
        if ($emotion) {
            $demoSongs = array_filter($demoSongs, function($song) use ($emotion) {
                return $song['emotion'] === $emotion;
            });
            $demoSongs = array_values($demoSongs);
        }
        
        echo json_encode([
            'success' => true,
            'songs' => $demoSongs,
            'count' => count($demoSongs),
            'demo_mode' => true
        ]);
    }
    
} elseif ($method === 'POST') {
    // Add new song (admin functionality)
    $data = json_decode(file_get_contents('php://input'), true);
    
    $title = sanitizeInput($data['title']);
    $artist = sanitizeInput($data['artist']);
    $emotion = sanitizeInput($data['emotion']);
    $cover = sanitizeInput($data['cover']);
    $duration = sanitizeInput($data['duration']);
    $file_path = sanitizeInput($data['file_path']);
    
    try {
        $query = "INSERT INTO songs (title, artist, emotion, cover, duration, file_path) 
                  VALUES (:title, :artist, :emotion, :cover, :duration, :file_path)";
        
        $stmt = $db->prepare($query);
        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':artist', $artist);
        $stmt->bindParam(':emotion', $emotion);
        $stmt->bindParam(':cover', $cover);
        $stmt->bindParam(':duration', $duration);
        $stmt->bindParam(':file_path', $file_path);
        
        if ($stmt->execute()) {
            echo json_encode([
                'success' => true,
                'message' => 'Song added successfully',
                'song_id' => $db->lastInsertId()
            ]);
        }
    } catch (PDOException $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Error adding song: ' . $e->getMessage()
        ]);
    }
}
?>