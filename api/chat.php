<?php
// api/ml-chat.php - ML-Enhanced AI Chat API Endpoint

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');

require_once '../config/database.php';
require_once '../includes/functions.php';
require_once '../includes/ml-emotion-detector.php';

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

try {
    // Initialize ML emotion detector
    $mlDetector = new MLEmotionDetector();
    
    // Predict emotion using Machine Learning
    $predictionResult = $mlDetector->predictWithConfidence($userMessage);
    $detectedEmotion = $predictionResult['emotion'];
    $confidence = $predictionResult['confidence'];
    
    // Initialize database
    $database = new Database();
    $db = $database->getConnection();
    
    // Get songs matching the detected emotion
    $query = "SELECT * FROM songs WHERE emotion = :emotion ORDER BY RAND() LIMIT 3";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':emotion', $detectedEmotion);
    $stmt->execute();
    $recommendedSongs = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // If no songs found in DB, use demo data
    if (empty($recommendedSongs)) {
        $demoSongs = [
            'sad' => [
                ['id' => 1, 'title' => 'Midnight Rain', 'artist' => 'Luna Echo', 'emotion' => 'sad', 'cover' => '🌧️', 'duration' => '3:45'],
                ['id' => 5, 'title' => 'Lonely Nights', 'artist' => 'Soul Singer', 'emotion' => 'sad', 'cover' => '🌙', 'duration' => '4:30'],
                ['id' => 9, 'title' => 'Heartbreak Blues', 'artist' => 'Emotion Express', 'emotion' => 'sad', 'cover' => '💔', 'duration' => '4:15']
            ],
            'happy' => [
                ['id' => 2, 'title' => 'Summer Vibes', 'artist' => 'DJ Sunshine', 'emotion' => 'happy', 'cover' => '☀️', 'duration' => '3:20'],
                ['id' => 6, 'title' => 'Party Time', 'artist' => 'Beat Masters', 'emotion' => 'happy', 'cover' => '🎉', 'duration' => '3:15'],
                ['id' => 10, 'title' => 'Feel Good', 'artist' => 'Happy Souls', 'emotion' => 'happy', 'cover' => '😊', 'duration' => '3:30']
            ],
            'calm' => [
                ['id' => 3, 'title' => 'Deep Thoughts', 'artist' => 'Mind Wave', 'emotion' => 'calm', 'cover' => '🌊', 'duration' => '4:12'],
                ['id' => 7, 'title' => 'Morning Peace', 'artist' => 'Zen Garden', 'emotion' => 'calm', 'cover' => '🍃', 'duration' => '5:00'],
                ['id' => 11, 'title' => 'Meditation Flow', 'artist' => 'Inner Peace', 'emotion' => 'calm', 'cover' => '🧘', 'duration' => '6:00']
            ],
            'energetic' => [
                ['id' => 4, 'title' => 'Energy Burst', 'artist' => 'Power Pulse', 'emotion' => 'energetic', 'cover' => '⚡', 'duration' => '2:58'],
                ['id' => 8, 'title' => 'Workout Mix', 'artist' => 'Fit Beats', 'emotion' => 'energetic', 'cover' => '🏃', 'duration' => '3:40'],
                ['id' => 12, 'title' => 'Adrenaline Rush', 'artist' => 'Extreme Sports', 'emotion' => 'energetic', 'cover' => '🎸', 'duration' => '3:25']
            ]
        ];
        $recommendedSongs = $demoSongs[$detectedEmotion] ?? $demoSongs['calm'];
    }
    
    // Generate contextual response
    $emotionResponses = [
        'sad' => [
            "I can sense you're going through a tough time. Music can be really healing. 💙",
            "I'm sorry you're feeling down. Let me find something that might comfort you.",
            "I hear you. Sometimes the right song can help us process difficult emotions."
        ],
        'happy' => [
            "Your positive energy is contagious! Let's keep those good vibes going! ✨",
            "I love that you're in such a great mood! Here's something to match your energy!",
            "That's wonderful! Let me find the perfect soundtrack for your happiness!"
        ],
        'calm' => [
            "Finding peace within yourself is beautiful. Here's something soothing. 🌿",
            "I sense your tranquil mood. Let me enhance that peaceful feeling.",
            "Calm moments are precious. Here's a song that flows like gentle water."
        ],
        'energetic' => [
            "I can feel that energy! Let's channel it into something powerful! ⚡",
            "You're fired up! Here's something to match that incredible energy!",
            "That drive and motivation is amazing! Let's amplify it with the perfect beat!"
        ]
    ];
    
    $responses = $emotionResponses[$detectedEmotion];
    $mainResponse = $responses[array_rand($responses)];
    
    // Build response with song recommendations
    $response = $mainResponse . "\n\nBased on my ML analysis (";
    $response .= $confidence . "% confidence), I think you'd love these ";
    $response .= $detectedEmotion . " songs:\n\n";
    
    foreach ($recommendedSongs as $index => $song) {
        $response .= ($index + 1) . ". " . $song['title'] . " by " . $song['artist'] . "\n";
    }
    
    // Save to chat history if user is logged in
    if (isset($_SESSION['user_id'])) {
        try {
            $insertQuery = "INSERT INTO chat_history (user_id, message, role, detected_emotion, recommended_song_id) 
                           VALUES (:user_id, :message, 'user', :emotion, :song_id)";
            $insertStmt = $db->prepare($insertQuery);
            $insertStmt->execute([
                ':user_id' => $_SESSION['user_id'],
                ':message' => $userMessage,
                ':emotion' => $detectedEmotion,
                ':song_id' => $recommendedSongs[0]['id']
            ]);
            
            // Save assistant response
            $insertQuery = "INSERT INTO chat_history (user_id, message, role) 
                           VALUES (:user_id, :message, 'assistant')";
            $insertStmt = $db->prepare($insertQuery);
            $insertStmt->execute([
                ':user_id' => $_SESSION['user_id'],
                ':message' => $response
            ]);
        } catch (PDOException $e) {
            error_log('Chat history save error: ' . $e->getMessage());
        }
    }
    
    echo json_encode([
        'success' => true,
        'response' => $response,
        'detectedEmotion' => $detectedEmotion,
        'confidence' => $confidence,
        'allProbabilities' => $predictionResult['all_probabilities'],
        'recommendedSongs' => $recommendedSongs,
        'mlPowered' => true,
        'model' => 'Rubix ML - K-Nearest Neighbors'
    ]);
    
} catch (Exception $e) {
    error_log('ML Chat Error: ' . $e->getMessage());
    
    // Fallback to simple keyword detection
    $detectedEmotion = analyzeSentiment($userMessage);
    
    echo json_encode([
        'success' => true,
        'response' => 'I understand how you\'re feeling. Let me find you the perfect song!',
        'detectedEmotion' => $detectedEmotion,
        'confidence' => 0,
        'recommendedSongs' => [],
        'mlPowered' => false,
        'error' => 'ML model fallback'
    ]);
}