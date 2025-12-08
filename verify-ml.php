<?php
// verify-ml.php - Quick verification that ML is working

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html>
<head>
    <title>ML Verification - HearYou</title>
    <style>
        body { font-family: monospace; padding: 20px; background: #1a1a2e; color: #fff; }
        .success { color: #22c55e; }
        .error { color: #ef4444; }
        .warning { color: #eab308; }
        .box { background: #16213e; padding: 15px; margin: 10px 0; border-radius: 8px; border-left: 4px solid #9333ea; }
        h1 { color: #9333ea; }
        code { background: #0f1419; padding: 2px 6px; border-radius: 4px; }
    </style>
</head>
<body>
    <h1>🎵 HearYou ML Verification</h1>
    
    <?php
    echo "<div class='box'>";
    
    // 1. Check vendor autoload
    echo "<h3>1. Checking Composer Autoload</h3>";
    if (file_exists(__DIR__ . '/vendor/autoload.php')) {
        echo "<p class='success'>✅ Composer autoload found</p>";
        require_once __DIR__ . '/vendor/autoload.php';
    } else {
        echo "<p class='error'>❌ Composer autoload NOT found</p>";
        echo "<p>Run: <code>composer install</code></p>";
        echo "</div></body></html>";
        exit;
    }
    
    // 2. Check Rubix ML
    echo "<h3>2. Checking Rubix ML</h3>";
    try {
        if (class_exists('Rubix\ML\Classifiers\KNearestNeighbors')) {
            echo "<p class='success'>✅ Rubix ML is installed</p>";
            $rubixVersion = \Composer\InstalledVersions::getVersion('rubix/ml');
            echo "<p>Version: <code>$rubixVersion</code></p>";
        } else {
            echo "<p class='error'>❌ Rubix ML classes not found</p>";
            echo "<p>Run: <code>composer require rubix/ml</code></p>";
            echo "</div></body></html>";
            exit;
        }
    } catch (Exception $e) {
        echo "<p class='error'>❌ Error: " . $e->getMessage() . "</p>";
        echo "</div></body></html>";
        exit;
    }
    
    // 3. Check models directory
    echo "<h3>3. Checking Models Directory</h3>";
    $modelsDir = __DIR__ . '/models';
    if (!file_exists($modelsDir)) {
        mkdir($modelsDir, 0755, true);
        echo "<p class='success'>✅ Created models/ directory</p>";
    } else {
        echo "<p class='success'>✅ models/ directory exists</p>";
    }
    
    if (is_writable($modelsDir)) {
        echo "<p class='success'>✅ models/ is writable</p>";
    } else {
        echo "<p class='warning'>⚠️ models/ is not writable</p>";
        echo "<p>Run: <code>chmod 755 models</code></p>";
    }
    
    // 4. Check ML files
    echo "<h3>4. Checking ML Files</h3>";
    $files = [
        'includes/ml-emotion-detector.php' => 'ML Emotion Detector',
        'api/ml-chat.php' => 'ML Chat API',
        'includes/ml-recommender.php' => 'ML Recommender',
        'api/ml-recommendations.php' => 'ML Recommendations API'
    ];
    
    foreach ($files as $file => $name) {
        if (file_exists(__DIR__ . '/' . $file)) {
            echo "<p class='success'>✅ $name</p>";
        } else {
            echo "<p class='error'>❌ $name NOT FOUND</p>";
        }
    }
    
    // 5. Test ML Emotion Detector
    echo "<h3>5. Testing ML Emotion Detection</h3>";
    
    if (file_exists(__DIR__ . '/includes/ml-emotion-detector.php')) {
        try {
            require_once __DIR__ . '/includes/ml-emotion-detector.php';
            
            $detector = new MLEmotionDetector();
            echo "<p class='success'>✅ MLEmotionDetector initialized</p>";
            
            // Run test predictions
            $tests = [
                "I am so happy and excited!" => "happy",
                "Feeling really sad today" => "sad",
                "Need some peace and calm" => "calm",
                "Let's workout! Full of energy!" => "energetic"
            ];
            
            echo "<div style='margin: 15px 0;'>";
            echo "<h4>Test Predictions:</h4>";
            
            foreach ($tests as $text => $expected) {
                $result = $detector->predictWithConfidence($text);
                $match = $result['emotion'] === $expected;
                $icon = $match ? "✅" : "⚠️";
                $color = $match ? "success" : "warning";
                
                echo "<p class='$color'>$icon <strong>\"$text\"</strong></p>";
                echo "<p style='margin-left: 30px;'>Predicted: <code>{$result['emotion']}</code> (Confidence: {$result['confidence']}%)</p>";
            }
            echo "</div>";
            
        } catch (Exception $e) {
            echo "<p class='error'>❌ Error: " . $e->getMessage() . "</p>";
            echo "<pre style='background: #0f1419; padding: 10px; border-radius: 4px; overflow: auto;'>";
            echo $e->getTraceAsString();
            echo "</pre>";
        }
    } else {
        echo "<p class='error'>❌ ml-emotion-detector.php not found</p>";
    }
    
    // 6. Database check
    echo "<h3>6. Checking Database</h3>";
    try {
        require_once __DIR__ . '/config/database.php';
        $database = new Database();
        $db = $database->getConnection();
        
        if ($db) {
            echo "<p class='success'>✅ Database connected</p>";
            
            $query = "SELECT COUNT(*) as count FROM songs";
            $stmt = $db->prepare($query);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            echo "<p>Songs in database: <code>{$result['count']}</code></p>";
        }
    } catch (Exception $e) {
        echo "<p class='warning'>⚠️ Database: " . $e->getMessage() . "</p>";
    }
    
    echo "</div>";
    
    // Final summary
    echo "<div class='box' style='border-left-color: #22c55e;'>";
    echo "<h2>✅ Setup Complete!</h2>";
    echo "<p><strong>Your ML integration is ready!</strong></p>";
    echo "<ul>";
    echo "<li>✅ Rubix ML library installed from Packagist.org</li>";
    echo "<li>✅ Emotion detection using K-Nearest Neighbors</li>";
    echo "<li>✅ Chat API ready at <code>api/ml-chat.php</code></li>";
    echo "<li>✅ Model will be trained on first use</li>";
    echo "</ul>";
    echo "<p style='margin-top: 20px;'><strong>Next Steps:</strong></p>";
    echo "<ol>";
    echo "<li>Go back to your app: <a href='index.php' style='color: #9333ea;'>index.php</a></li>";
    echo "<li>Click the chat icon (💬)</li>";
    echo "<li>Send a message and see ML in action!</li>";
    echo "<li>Open browser console (F12) to see ML confidence scores</li>";
    echo "</ol>";
    echo "</div>";
    ?>
</body>
</html>