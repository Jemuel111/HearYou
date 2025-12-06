<?php
// includes/functions.php - Helper Functions

// Sanitize input
function sanitizeInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Generate JSON response
function jsonResponse($success, $data = [], $message = '') {
    header('Content-Type: application/json');
    echo json_encode([
        'success' => $success,
        'data' => $data,
        'message' => $message
    ]);
    exit;
}

// Check if user is logged in (for future authentication)
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Get current user ID
function getCurrentUserId() {
    return $_SESSION['user_id'] ?? null;
}

// Format duration
function formatDuration($seconds) {
    $minutes = floor($seconds / 60);
    $remainingSeconds = $seconds % 60;
    return sprintf("%d:%02d", $minutes, $remainingSeconds);
}

// Get emotion emoji
function getEmotionEmoji($emotion) {
    $emojis = [
        'sad' => '😢',
        'happy' => '😊',
        'calm' => '😌',
        'energetic' => '⚡'
    ];
    return $emojis[$emotion] ?? '🎵';
}

// Analyze sentiment from text
function analyzeSentiment($text) {
    $text = strtolower($text);
    
    $emotionKeywords = [
        'sad' => ['sad', 'down', 'depressed', 'lonely', 'hurt', 'crying', 'heartbroken', 'miss', 'blue', 'unhappy'],
        'happy' => ['happy', 'joy', 'excited', 'great', 'amazing', 'wonderful', 'love', 'celebrate', 'awesome', 'fantastic'],
        'calm' => ['calm', 'peace', 'relax', 'chill', 'meditate', 'tranquil', 'zen', 'quiet', 'serene', 'peaceful'],
        'energetic' => ['energy', 'workout', 'pump', 'active', 'dance', 'party', 'hype', 'motivated', 'powerful', 'intense']
    ];
    
    $scores = [];
    
    foreach ($emotionKeywords as $emotion => $keywords) {
        $score = 0;
        foreach ($keywords as $keyword) {
            if (strpos($text, $keyword) !== false) {
                $score++;
            }
        }
        $scores[$emotion] = $score;
    }
    
    arsort($scores);
    $detectedEmotion = key($scores);
    
    return $scores[$detectedEmotion] > 0 ? $detectedEmotion : 'calm';
}
?>