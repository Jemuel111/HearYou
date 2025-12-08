<?php
// api/playlists/manage.php - Playlist Management API

session_start();
header('Content-Type: application/json');

require_once '../../config/database.php';
require_once '../../includes/functions.php';

$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? '';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Please login first']);
    exit;
}

$user_id = $_SESSION['user_id'];
$database = new Database();
$db = $database->getConnection();

try {
    switch ($action) {
        case 'create':
            if ($method !== 'POST') break;
            
            $data = json_decode(file_get_contents('php://input'), true);
            $name = sanitizeInput($data['name'] ?? '');
            $description = sanitizeInput($data['description'] ?? '');
            $is_public = isset($data['is_public']) ? (bool)$data['is_public'] : false;
            
            if (empty($name)) {
                echo json_encode(['success' => false, 'message' => 'Playlist name is required']);
                exit;
            }
            
            $query = "INSERT INTO playlists (user_id, name, description, is_public, created_at) 
                      VALUES (:user_id, :name, :description, :is_public, NOW())";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':user_id', $user_id);
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':description', $description);
            $stmt->bindParam(':is_public', $is_public, PDO::PARAM_BOOL);
            
            if ($stmt->execute()) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Playlist created successfully',
                    'playlist_id' => $db->lastInsertId()
                ]);
            }
            break;
            
        case 'list':
            $query = "SELECT p.*, COUNT(ps.song_id) as song_count 
                      FROM playlists p 
                      LEFT JOIN playlist_songs ps ON p.id = ps.playlist_id 
                      WHERE p.user_id = :user_id 
                      GROUP BY p.id 
                      ORDER BY p.updated_at DESC";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':user_id', $user_id);
            $stmt->execute();
            $playlists = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            echo json_encode(['success' => true, 'playlists' => $playlists]);
            break;
            
        case 'get':
            $playlist_id = intval($_GET['id'] ?? 0);
            
            $query = "SELECT p.*, 
                      (SELECT COUNT(*) FROM playlist_songs WHERE playlist_id = p.id) as song_count
                      FROM playlists p 
                      WHERE p.id = :playlist_id AND p.user_id = :user_id";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':playlist_id', $playlist_id);
            $stmt->bindParam(':user_id', $user_id);
            $stmt->execute();
            $playlist = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$playlist) {
                echo json_encode(['success' => false, 'message' => 'Playlist not found']);
                exit;
            }
            
            // Get songs
            $query = "SELECT s.*, ps.position, ps.added_at 
                      FROM songs s 
                      INNER JOIN playlist_songs ps ON s.id = ps.song_id 
                      WHERE ps.playlist_id = :playlist_id 
                      ORDER BY ps.position ASC";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':playlist_id', $playlist_id);
            $stmt->execute();
            $songs = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            $playlist['songs'] = $songs;
            
            echo json_encode(['success' => true, 'playlist' => $playlist]);
            break;
            
        case 'update':
            if ($method !== 'POST') break;
            
            $data = json_decode(file_get_contents('php://input'), true);
            $playlist_id = intval($data['id'] ?? 0);
            $name = sanitizeInput($data['name'] ?? '');
            $description = sanitizeInput($data['description'] ?? '');
            $is_public = isset($data['is_public']) ? (bool)$data['is_public'] : false;
            
            $query = "UPDATE playlists 
                      SET name = :name, description = :description, is_public = :is_public, updated_at = NOW() 
                      WHERE id = :playlist_id AND user_id = :user_id";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':description', $description);
            $stmt->bindParam(':is_public', $is_public, PDO::PARAM_BOOL);
            $stmt->bindParam(':playlist_id', $playlist_id);
            $stmt->bindParam(':user_id', $user_id);
            
            if ($stmt->execute()) {
                echo json_encode(['success' => true, 'message' => 'Playlist updated successfully']);
            }
            break;
            
        case 'delete':
            if ($method !== 'POST') break;
            
            $data = json_decode(file_get_contents('php://input'), true);
            $playlist_id = intval($data['id'] ?? 0);
            
            $query = "DELETE FROM playlists WHERE id = :playlist_id AND user_id = :user_id";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':playlist_id', $playlist_id);
            $stmt->bindParam(':user_id', $user_id);
            
            if ($stmt->execute()) {
                echo json_encode(['success' => true, 'message' => 'Playlist deleted successfully']);
            }
            break;
            
        case 'add_song':
            if ($method !== 'POST') break;
            
            $data = json_decode(file_get_contents('php://input'), true);
            $playlist_id = intval($data['playlist_id'] ?? 0);
            $song_id = intval($data['song_id'] ?? 0);
            
            // Verify playlist ownership
            $query = "SELECT id FROM playlists WHERE id = :playlist_id AND user_id = :user_id";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':playlist_id', $playlist_id);
            $stmt->bindParam(':user_id', $user_id);
            $stmt->execute();
            
            if (!$stmt->fetch()) {
                echo json_encode(['success' => false, 'message' => 'Playlist not found']);
                exit;
            }
            
            // Get next position
            $query = "SELECT COALESCE(MAX(position), 0) + 1 as next_position 
                      FROM playlist_songs WHERE playlist_id = :playlist_id";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':playlist_id', $playlist_id);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $position = $result['next_position'];
            
            // Add song
            $query = "INSERT INTO playlist_songs (playlist_id, song_id, position, added_at) 
                      VALUES (:playlist_id, :song_id, :position, NOW())
                      ON DUPLICATE KEY UPDATE position = :position";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':playlist_id', $playlist_id);
            $stmt->bindParam(':song_id', $song_id);
            $stmt->bindParam(':position', $position);
            
            if ($stmt->execute()) {
                // Update playlist updated_at
                $query = "UPDATE playlists SET updated_at = NOW() WHERE id = :playlist_id";
                $stmt = $db->prepare($query);
                $stmt->bindParam(':playlist_id', $playlist_id);
                $stmt->execute();
                
                echo json_encode(['success' => true, 'message' => 'Song added to playlist']);
            }
            break;
            
        case 'remove_song':
            if ($method !== 'POST') break;
            
            $data = json_decode(file_get_contents('php://input'), true);
            $playlist_id = intval($data['playlist_id'] ?? 0);
            $song_id = intval($data['song_id'] ?? 0);
            
            // Verify playlist ownership
            $query = "SELECT id FROM playlists WHERE id = :playlist_id AND user_id = :user_id";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':playlist_id', $playlist_id);
            $stmt->bindParam(':user_id', $user_id);
            $stmt->execute();
            
            if (!$stmt->fetch()) {
                echo json_encode(['success' => false, 'message' => 'Playlist not found']);
                exit;
            }
            
            $query = "DELETE FROM playlist_songs 
                      WHERE playlist_id = :playlist_id AND song_id = :song_id";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':playlist_id', $playlist_id);
            $stmt->bindParam(':song_id', $song_id);
            
            if ($stmt->execute()) {
                // Update playlist updated_at
                $query = "UPDATE playlists SET updated_at = NOW() WHERE id = :playlist_id";
                $stmt = $db->prepare($query);
                $stmt->bindParam(':playlist_id', $playlist_id);
                $stmt->execute();
                
                echo json_encode(['success' => true, 'message' => 'Song removed from playlist']);
            }
            break;
            
        case 'reorder':
            if ($method !== 'POST') break;
            
            $data = json_decode(file_get_contents('php://input'), true);
            $playlist_id = intval($data['playlist_id'] ?? 0);
            $song_order = $data['order'] ?? [];
            
            // Verify playlist ownership
            $query = "SELECT id FROM playlists WHERE id = :playlist_id AND user_id = :user_id";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':playlist_id', $playlist_id);
            $stmt->bindParam(':user_id', $user_id);
            $stmt->execute();
            
            if (!$stmt->fetch()) {
                echo json_encode(['success' => false, 'message' => 'Playlist not found']);
                exit;
            }
            
            // Update positions
            $db->beginTransaction();
            try {
                $query = "UPDATE playlist_songs SET position = :position 
                          WHERE playlist_id = :playlist_id AND song_id = :song_id";
                $stmt = $db->prepare($query);
                
                foreach ($song_order as $index => $song_id) {
                    $position = $index + 1;
                    $stmt->bindParam(':position', $position);
                    $stmt->bindParam(':playlist_id', $playlist_id);
                    $stmt->bindParam(':song_id', $song_id);
                    $stmt->execute();
                }
                
                $db->commit();
                echo json_encode(['success' => true, 'message' => 'Playlist reordered successfully']);
            } catch (Exception $e) {
                $db->rollBack();
                echo json_encode(['success' => false, 'message' => 'Failed to reorder playlist']);
            }
            break;
            
        default:
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
    }
} catch (PDOException $e) {
    error_log('Playlist API error: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Database error occurred']);
}
?>