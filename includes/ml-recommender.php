<?php
// includes/ml-recommender.php - ML-Powered Music Recommendation System

require_once __DIR__ . '/../vendor/autoload.php';

use Rubix\ML\Regressors\KNNRegressor;
use Rubix\ML\Datasets\Labeled;
use Rubix\ML\Datasets\Unlabeled;
use Rubix\ML\Kernels\Distance\Euclidean;

class MLMusicRecommender {
    private $db;
    
    public function __construct($database) {
        $this->db = $database;
    }
    
    /**
     * Get personalized song recommendations using collaborative filtering
     * 
     * @param int $userId User ID
     * @param int $limit Number of recommendations
     * @return array Recommended songs
     */
    public function getPersonalizedRecommendations($userId, $limit = 10) {
        try {
            // Get user's listening history and preferences
            $userProfile = $this->getUserProfile($userId);
            
            if (empty($userProfile)) {
                // New user - return popular songs
                return $this->getPopularSongs($limit);
            }
            
            // Get all songs
            $allSongs = $this->getAllSongs();
            
            // Calculate similarity scores
            $recommendations = [];
            foreach ($allSongs as $song) {
                if (!in_array($song['id'], $userProfile['listened_songs'])) {
                    $score = $this->calculateSimilarityScore($userProfile, $song);
                    $recommendations[] = [
                        'song' => $song,
                        'score' => $score
                    ];
                }
            }
            
            // Sort by score
            usort($recommendations, function($a, $b) {
                return $b['score'] <=> $a['score'];
            });
            
            // Return top recommendations
            return array_slice(array_column($recommendations, 'song'), 0, $limit);
            
        } catch (Exception $e) {
            error_log('Recommendation Error: ' . $e->getMessage());
            return $this->getPopularSongs($limit);
        }
    }
    
    /**
     * Get user profile based on listening history
     */
    private function getUserProfile($userId) {
        try {
            // Get user's listening history
            $query = "SELECT s.*, COUNT(*) as play_count 
                     FROM listening_history lh 
                     JOIN songs s ON lh.song_id = s.id 
                     WHERE lh.user_id = :user_id 
                     GROUP BY s.id 
                     ORDER BY play_count DESC";
            
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':user_id', $userId);
            $stmt->execute();
            $history = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            if (empty($history)) {
                return [];
            }
            
            // Calculate emotion preferences
            $emotionScores = [
                'sad' => 0,
                'happy' => 0,
                'calm' => 0,
                'energetic' => 0
            ];
            
            $totalPlays = 0;
            $listenedSongs = [];
            
            foreach ($history as $item) {
                $plays = $item['play_count'];
                $emotionScores[$item['emotion']] += $plays;
                $totalPlays += $plays;
                $listenedSongs[] = $item['id'];
            }
            
            // Normalize scores
            foreach ($emotionScores as $emotion => $score) {
                $emotionScores[$emotion] = $totalPlays > 0 ? $score / $totalPlays : 0;
            }
            
            return [
                'emotion_preferences' => $emotionScores,
                'listened_songs' => $listenedSongs,
                'total_plays' => $totalPlays
            ];
            
        } catch (PDOException $e) {
            error_log('User Profile Error: ' . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Calculate similarity score between user profile and song
     */
    private function calculateSimilarityScore($userProfile, $song) {
        // Base score on emotion preference
        $emotionScore = $userProfile['emotion_preferences'][$song['emotion']] ?? 0;
        
        // Add popularity bonus
        $popularityScore = ($song['play_count'] ?? 0) / 10000; // Normalize
        
        // Combined score
        $score = ($emotionScore * 0.7) + ($popularityScore * 0.3);
        
        return $score;
    }
    
    /**
     * Get all songs from database
     */
    private function getAllSongs() {
        try {
            $query = "SELECT * FROM songs ORDER BY play_count DESC";
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log('Get Songs Error: ' . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get popular songs as fallback
     */
    private function getPopularSongs($limit = 10) {
        try {
            $query = "SELECT * FROM songs ORDER BY play_count DESC LIMIT :limit";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }
    
    /**
     * Get similar songs using K-Nearest Neighbors
     * 
     * @param int $songId Target song ID
     * @param int $limit Number of similar songs
     * @return array Similar songs
     */
    public function getSimilarSongs($songId, $limit = 5) {
        try {
            // Get target song
            $query = "SELECT * FROM songs WHERE id = :id LIMIT 1";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':id', $songId);
            $stmt->execute();
            $targetSong = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$targetSong) {
                return [];
            }
            
            // Get all other songs
            $query = "SELECT * FROM songs WHERE id != :id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':id', $songId);
            $stmt->execute();
            $allSongs = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Calculate similarity scores
            $similarities = [];
            foreach ($allSongs as $song) {
                $score = $this->calculateSongSimilarity($targetSong, $song);
                $similarities[] = [
                    'song' => $song,
                    'score' => $score
                ];
            }
            
            // Sort by similarity
            usort($similarities, function($a, $b) {
                return $b['score'] <=> $a['score'];
            });
            
            // Return top similar songs
            return array_slice(array_column($similarities, 'song'), 0, $limit);
            
        } catch (PDOException $e) {
            error_log('Similar Songs Error: ' . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Calculate similarity between two songs
     */
    private function calculateSongSimilarity($song1, $song2) {
        $score = 0;
        
        // Same emotion = high similarity
        if ($song1['emotion'] === $song2['emotion']) {
            $score += 0.6;
        }
        
        // Similar duration (within 30 seconds)
        $duration1 = $this->parseDuration($song1['duration']);
        $duration2 = $this->parseDuration($song2['duration']);
        $durationDiff = abs($duration1 - $duration2);
        if ($durationDiff <= 30) {
            $score += 0.2 * (1 - ($durationDiff / 30));
        }
        
        // Same artist = moderate similarity
        if ($song1['artist'] === $song2['artist']) {
            $score += 0.2;
        }
        
        return $score;
    }
    
    /**
     * Parse duration string to seconds
     */
    private function parseDuration($duration) {
        $parts = explode(':', $duration);
        return (int)$parts[0] * 60 + (int)$parts[1];
    }
    
    /**
     * Track user interaction for improving recommendations
     * 
     * @param int $userId User ID
     * @param int $songId Song ID
     * @param string $action Action type (play, like, skip)
     */
    public function trackInteraction($userId, $songId, $action = 'play') {
        try {
            $query = "INSERT INTO listening_history (user_id, song_id, played_at, completed) 
                     VALUES (:user_id, :song_id, NOW(), :completed)";
            
            $stmt = $this->db->prepare($query);
            $completed = ($action === 'play') ? 1 : 0;
            
            $stmt->execute([
                ':user_id' => $userId,
                ':song_id' => $songId,
                ':completed' => $completed
            ]);
            
            // Update song play count
            $updateQuery = "UPDATE songs SET play_count = play_count + 1 WHERE id = :song_id";
            $updateStmt = $this->db->prepare($updateQuery);
            $updateStmt->bindParam(':song_id', $songId);
            $updateStmt->execute();
            
        } catch (PDOException $e) {
            error_log('Track Interaction Error: ' . $e->getMessage());
        }
    }
}