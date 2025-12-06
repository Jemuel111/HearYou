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
            ['id' => 1, 'title' => 'Midnight Rain', 'artist' => 'Luna Echo', 'emotion' => 'sad', 'cover' => '🌧️', 'duration' => '3:45', 'file_path' => 'songs/midnight_rain.mp3'],
            ['id' => 2, 'title' => 'Summer Vibes', 'artist' => 'DJ Sunshine', 'emotion' => 'happy', 'cover' => '☀️', 'duration' => '3:20', 'file_path' => 'songs/summer_vibes.mp3'],
            ['id' => 3, 'title' => 'Deep Thoughts', 'artist' => 'Mind Wave', 'emotion' => 'calm', 'cover' => '🌊', 'duration' => '4:12', 'file_path' => 'songs/deep_thoughts.mp3'],
            ['id' => 4, 'title' => 'Energy Burst', 'artist' => 'Power Pulse', 'emotion' => 'energetic', 'cover' => '⚡', 'duration' => '2:58', 'file_path' => 'songs/energy_burst.mp3'],
            ['id' => 5, 'title' => 'Lonely Nights', 'artist' => 'Soul Singer', 'emotion' => 'sad', 'cover' => '🌙', 'duration' => '4:30', 'file_path' => 'songs/lonely_nights.mp3'],
            ['id' => 6, 'title' => 'Party Time', 'artist' => 'Beat Masters', 'emotion' => 'happy', 'cover' => '🎉', 'duration' => '3:15', 'file_path' => 'songs/party_time.mp3'],
            ['id' => 7, 'title' => 'Morning Peace', 'artist' => 'Zen Garden', 'emotion' => 'calm', 'cover' => '🍃', 'duration' => '5:00', 'file_path' => 'songs/morning_peace.mp3'],
            ['id' => 8, 'title' => 'Workout Mix', 'artist' => 'Fit Beats', 'emotion' => 'energetic', 'cover' => '🏃', 'duration' => '3:40', 'file_path' => 'songs/workout_mix.mp3'],
            ['id' => 9, 'title' => 'Heartbreak Blues', 'artist' => 'Emotion Express', 'emotion' => 'sad', 'cover' => '💔', 'duration' => '4:15', 'file_path' => 'songs/heartbreak_blues.mp3'],
            ['id' => 10, 'title' => 'Feel Good', 'artist' => 'Happy Souls', 'emotion' => 'happy', 'cover' => '😊', 'duration' => '3:30', 'file_path' => 'songs/feel_good.mp3'],
            ['id' => 11, 'title' => 'Meditation Flow', 'artist' => 'Inner Peace', 'emotion' => 'calm', 'cover' => '🧘', 'duration' => '6:00', 'file_path' => 'songs/meditation_flow.mp3'],
            ['id' => 12, 'title' => 'Adrenaline Rush', 'artist' => 'Extreme Sports', 'emotion' => 'energetic', 'cover' => '🎸', 'duration' => '3:25', 'file_path' => 'songs/adrenaline_rush.mp3']
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