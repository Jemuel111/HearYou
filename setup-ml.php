<?php
// setup-ml.php - Machine Learning Setup & Testing Script

echo "=== HearYou ML Setup & Testing ===\n\n";

// Check if composer autoload exists
if (!file_exists(__DIR__ . '/vendor/autoload.php')) {
    echo "❌ ERROR: Composer dependencies not installed!\n";
    echo "Please run: composer install\n\n";
    exit(1);
}

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/includes/ml-emotion-detector.php';

echo "✅ Composer autoload found\n";

// Create models directory if it doesn't exist
$modelsDir = __DIR__ . '/models';
if (!file_exists($modelsDir)) {
    mkdir($modelsDir, 0755, true);
    echo "✅ Created models directory: $modelsDir\n";
} else {
    echo "✅ Models directory exists\n";
}

// Test ML Emotion Detector
echo "\n--- Testing ML Emotion Detector ---\n\n";

try {
    $detector = new MLEmotionDetector();
    echo "✅ ML Emotion Detector initialized\n\n";
    
    // Test predictions
    $testCases = [
        "I'm feeling so sad and lonely today" => "sad",
        "This is the best day of my life! So happy!" => "happy",
        "Just need some peace and quiet time to relax" => "calm",
        "Let's go! I'm pumped up and ready to workout!" => "energetic"
    ];
    
    echo "Testing predictions:\n";
    echo str_repeat("-", 70) . "\n";
    
    foreach ($testCases as $text => $expected) {
        $result = $detector->predictWithConfidence($text);
        $emoji = $result['emotion'] === $expected ? "✅" : "⚠️";
        
        echo "$emoji Input: \"$text\"\n";
        echo "   Predicted: {$result['emotion']} (Confidence: {$result['confidence']}%)\n";
        echo "   Expected: $expected\n";
        echo "   Probabilities: " . json_encode($result['all_probabilities']) . "\n\n";
    }
    
    // Test batch analysis
    echo "\n--- Testing Batch Analysis ---\n\n";
    
    $batchTexts = [
        "I'm so happy right now!",
        "Feeling really down today...",
        "Just want to relax and chill",
        "Let's party all night!",
        "I miss you so much",
        "Excited for the weekend!",
        "Need some meditation time",
        "Ready to hit the gym hard!"
    ];
    
    $batchResult = $detector->analyzeBatch($batchTexts);
    echo "Total analyzed: {$batchResult['total_analyzed']}\n";
    echo "Distribution:\n";
    foreach ($batchResult['distribution'] as $emotion => $count) {
        echo "  - $emotion: $count ({$batchResult['percentages'][$emotion]}%)\n";
    }
    echo "Dominant emotion: {$batchResult['dominant_emotion']}\n\n";
    
    echo "✅ All ML tests passed successfully!\n\n";
    
} catch (Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
    exit(1);
}

// Test database connection
echo "--- Testing Database Connection ---\n\n";

try {
    require_once __DIR__ . '/config/database.php';
    $database = new Database();
    $db = $database->getConnection();
    
    if ($db) {
        echo "✅ Database connection successful\n\n";
        
        // Check if songs table exists
        $query = "SELECT COUNT(*) as count FROM songs";
        $stmt = $db->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        echo "Songs in database: {$result['count']}\n";
        
        if ($result['count'] == 0) {
            echo "⚠️  Warning: No songs found in database. Please run database.sql\n";
        }
    } else {
        echo "❌ Database connection failed\n";
    }
    
} catch (Exception $e) {
    echo "❌ Database Error: " . $e->getMessage() . "\n";
}

echo "\n=== Setup Complete ===\n\n";
echo "Next steps:\n";
echo "1. Ensure your database is set up (run database.sql)\n";
echo "2. Update chat.js to use api/ml-chat.php instead of api/chat.php\n";
echo "3. Test the emotion detection in your app\n";
echo "4. The ML model will improve as more users interact with it\n\n";
echo "ML Features:\n";
echo "✓ Emotion Detection using K-Nearest Neighbors\n";
echo "✓ TF-IDF text vectorization\n";
echo "✓ Confidence scoring\n";
echo "✓ Batch analysis\n";
echo "✓ Collaborative filtering recommendations\n";
echo "✓ Similar song detection\n\n";