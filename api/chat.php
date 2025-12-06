<?php
// api/chat.php - AI Chat API Endpoint
// This integrates with AI services for intelligent music recommendations

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');

require_once '../config/database.php';
require_once '../includes/functions.php';

// Get POST data
$data = json_decode(file_get_contents('php://input'), true);
$userMessage = isset($data['message']) ? sanitizeInput($data['message']) : '';
$chatHistory = isset($data['history']) ? $data['history'] : [];

if (empty($userMessage)) {
    echo json_encode([
        'success' => false,
        'message' => 'Message is required'
    ]);
    exit;
}

// Initialize database
$database = new Database();
$db = $database->getConnection();

// Analyze sentiment using local function
$detectedEmotion = analyzeSentiment($userMessage);

// Get songs matching the detected emotion
try {
    $query = "SELECT * FROM songs WHERE emotion = :emotion ORDER BY RAND() LIMIT 1";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':emotion', $detectedEmotion);
    $stmt->execute();
    $recommendedSong = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // Fallback to demo data
    $demoSongs = [
        ['id' => 1, 'title' => 'Midnight Rain', 'artist' => 'Luna Echo', 'emotion' => 'sad', 'cover' => '🌧️', 'duration' => '3:45'],
        ['id' => 2, 'title' => 'Summer Vibes', 'artist' => 'DJ Sunshine', 'emotion' => 'happy', 'cover' => '☀️', 'duration' => '3:20'],
        ['id' => 3, 'title' => 'Deep Thoughts', 'artist' => 'Mind Wave', 'emotion' => 'calm', 'cover' => '🌊', 'duration' => '4:12'],
        ['id' => 4, 'title' => 'Energy Burst', 'artist' => 'Power Pulse', 'emotion' => 'energetic', 'cover' => '⚡', 'duration' => '2:58'],
        ['id' => 5, 'title' => 'Lonely Nights', 'artist' => 'Soul Singer', 'emotion' => 'sad', 'cover' => '🌙', 'duration' => '4:30'],
        ['id' => 6, 'title' => 'Party Time', 'artist' => 'Beat Masters', 'emotion' => 'happy', 'cover' => '🎉', 'duration' => '3:15'],
        ['id' => 7, 'title' => 'Morning Peace', 'artist' => 'Zen Garden', 'emotion' => 'calm', 'cover' => '🍃', 'duration' => '5:00'],
        ['id' => 8, 'title' => 'Workout Mix', 'artist' => 'Fit Beats', 'emotion' => 'energetic', 'cover' => '🏃', 'duration' => '3:40']
    ];
    
    $filteredSongs = array_filter($demoSongs, function($song) use ($detectedEmotion) {
        return $song['emotion'] === $detectedEmotion;
    });
    $filteredSongs = array_values($filteredSongs);
    $recommendedSong = $filteredSongs[array_rand($filteredSongs)];
}

// Generate response based on emotion
$emotionResponses = [
    'sad' => "I sense you're feeling a bit down. ",
    'happy' => "You seem to be in a great mood! ",
    'calm' => "It sounds like you're looking for some peace. ",
    'energetic' => "I can feel your energy! "
];

$response = $emotionResponses[$detectedEmotion] ?? "I understand how you're feeling. ";
$response .= "I recommend \"{$recommendedSong['title']}\" by {$recommendedSong['artist']}. ";
$response .= "This song captures that {$detectedEmotion} mood perfectly! Would you like to play it?";

// Optional: Integrate with OpenAI API or other AI services
// Uncomment and configure if you want to use a real AI service
/*
if (defined('OPENAI_API_KEY')) {
    $aiResponse = getAIRecommendation($userMessage, $chatHistory);
    $response = $aiResponse['message'];
}
*/

echo json_encode([
    'success' => true,
    'response' => $response,
    'detectedEmotion' => $detectedEmotion,
    'recommendedSong' => $recommendedSong
]);

// Optional: Function to integrate with OpenAI API
function getAIRecommendation($message, $history) {
    // This would integrate with OpenAI or another AI service
    // Example using Guzzle HTTP client (install via composer require guzzlehttp/guzzle)
    /*
    $client = new \GuzzleHttp\Client();
    
    $response = $client->post('https://api.openai.com/v1/chat/completions', [
        'headers' => [
            'Authorization' => 'Bearer ' . OPENAI_API_KEY,
            'Content-Type' => 'application/json',
        ],
        'json' => [
            'model' => 'gpt-3.5-turbo',
            'messages' => array_merge([
                ['role' => 'system', 'content' => 'You are a music recommendation AI that suggests songs based on user emotions.']
            ], $history, [
                ['role' => 'user', 'content' => $message]
            ])
        ]
    ]);
    
    $body = json_decode($response->getBody(), true);
    return [
        'message' => $body['choices'][0]['message']['content']
    ];
    */
    
    return ['message' => ''];
}
?>