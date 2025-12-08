<?php
// api/ml-recommendations.php - ML-Powered Recommendations API

session_start();
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');

require_once '../config/database.php';
require_once '../includes/functions.php';
require_once '../includes/ml-recommender.php';

// Initialize database
$database = new Database();
$db = $database->getConnection();

// Initialize ML recommender
$recommender = new MLMusicRecommender($db);

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    $action = $_GET['action'] ?? 'personalized';
    
    switch ($action) {
        case 'personalized':
            // Get personalized recommendations
            $userId = $_SESSION['user_id'] ?? null;
            $limit = isset($_GET['limit']) ? intval($_GET['limit']) : 10;
            
            if ($userId) {
                $recommendations = $recommender->getPersonalizedRecommendations($userId, $limit);
            } else {
                // Guest user - return popular songs
                $query = "SELECT * FROM songs ORDER BY play_count DESC LIMIT :limit";
                $stmt = $db->prepare($query);
                $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
                $stmt->execute();
                $recommendations = $stmt->fetchAll(PDO::FETCH_ASSOC);
            }
            
            echo json_encode([
                'success' => true,
                'recommendations' => $recommendations,
                'ml_powered' => true,
                'algorithm' => 'Collaborative Filtering with K-NN',
                'user_id' => $userId
            ]);
            break;
            
        case 'similar':
            // Get similar songs
            $songId = isset($_GET['song_id']) ? intval($_GET['song_id']) : 0;
            $limit = isset($_GET['limit']) ? intval($_GET['limit']) : 5;
            
            if ($songId === 0) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Song ID is required'
                ]);
                exit;
            }
            
            $similarSongs = $recommender->getSimilarSongs($songId, $limit);
            
            echo json_encode([
                'success' => true,
                'similar_songs' => $similarSongs,
                'ml_powered' => true,
                'algorithm' => 'K-Nearest Neighbors',
                'base_song_id' => $songId
            ]);
            break;
            
        default:
            echo json_encode([
                'success' => false,
                'message' => 'Invalid action'
            ]);
    }
    
} elseif ($method === 'POST') {
    // Track user interaction
    $data = json_decode(file_get_contents('php://input'), true);
    
    $userId = $_SESSION['user_id'] ?? null;
    $songId = isset($data['song_id']) ? intval($data['song_id']) : 0;
    $action = $data['action'] ?? 'play';
    
    if (!$userId || $songId === 0) {
        echo json_encode([
            'success' => false,
            'message' => 'User ID and Song ID are required'
        ]);
        exit;
    }
    
    $recommender->trackInteraction($userId, $songId, $action);
    
    echo json_encode([
        'success' => true,
        'message' => 'Interaction tracked',
        'ml_learning' => true
    ]);
}