<?php
// check-ml.php - Quick ML Diagnostic

echo "<h1>HearYou ML Diagnostics</h1>";
echo "<pre>";

// 1. Check if vendor autoload exists
echo "1. Checking Composer autoload...\n";
if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    echo "   ✅ vendor/autoload.php EXISTS\n";
    require_once __DIR__ . '/vendor/autoload.php';
} else {
    echo "   ❌ vendor/autoload.php NOT FOUND\n";
    echo "   Solution: Run 'composer install'\n";
    die();
}

// 2. Check if Rubix ML is installed
echo "\n2. Checking Rubix ML installation...\n";
try {
    if (class_exists('Rubix\ML\Classifiers\KNearestNeighbors')) {
        echo "   ✅ Rubix ML classes are available\n";
    } else {
        echo "   ❌ Rubix ML classes NOT FOUND\n";
        echo "   Solution: Run 'composer require rubix/ml'\n";
        die();
    }
} catch (Exception $e) {
    echo "   ❌ Error loading Rubix ML: " . $e->getMessage() . "\n";
    die();
}

// 3. Check models directory
echo "\n3. Checking models directory...\n";
$modelsDir = __DIR__ . '/models';
if (file_exists($modelsDir)) {
    echo "   ✅ models/ directory EXISTS\n";
    if (is_writable($modelsDir)) {
        echo "   ✅ models/ is WRITABLE\n";
    } else {
        echo "   ⚠️  models/ is NOT WRITABLE\n";
        echo "   Solution: chmod 755 models/\n";
    }
} else {
    echo "   ⚠️  models/ directory NOT FOUND\n";
    echo "   Creating it now...\n";
    mkdir($modelsDir, 0755, true);
    echo "   ✅ Created models/ directory\n";
}

// 4. Check if ML emotion detector file exists
echo "\n4. Checking ML files...\n";
if (file_exists(__DIR__ . '/includes/ml-emotion-detector.php')) {
    echo "   ✅ ml-emotion-detector.php EXISTS\n";
} else {
    echo "   ❌ ml-emotion-detector.php NOT FOUND\n";
    echo "   Solution: Create the file from the artifacts\n";
    die();
}

// 5. Try to initialize ML Detector
echo "\n5. Testing ML Emotion Detector...\n";
try {
    require_once __DIR__ . '/includes/ml-emotion-detector.php';
    $detector = new MLEmotionDetector();
    echo "   ✅ MLEmotionDetector initialized successfully\n";
    
    // Test prediction
    echo "\n6. Testing emotion prediction...\n";
    $testText = "I am so happy and excited today!";
    $result = $detector->predictWithConfidence($testText);
    
    echo "   Input: \"$testText\"\n";
    echo "   Detected Emotion: {$result['emotion']}\n";
    echo "   Confidence: {$result['confidence']}%\n";
    echo "   ✅ Prediction successful!\n";
    
} catch (Exception $e) {
    echo "   ❌ Error: " . $e->getMessage() . "\n";
    echo "   Stack trace:\n";
    echo "   " . $e->getTraceAsString() . "\n";
}

// 6. Check API files
echo "\n7. Checking API files...\n";
if (file_exists(__DIR__ . '/api/ml-chat.php')) {
    echo "   ✅ api/ml-chat.php EXISTS\n";
} else {
    echo "   ⚠️  api/ml-chat.php NOT FOUND\n";
}

// 7. Check database connection
echo "\n8. Testing database connection...\n";
try {
    require_once __DIR__ . '/config/database.php';
    $database = new Database();
    $db = $database->getConnection();
    
    if ($db) {
        echo "   ✅ Database connection successful\n";
        
        // Count songs
        $query = "SELECT COUNT(*) as count FROM songs";
        $stmt = $db->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        echo "   Songs in database: {$result['count']}\n";
    }
} catch (Exception $e) {
    echo "   ⚠️  Database error: " . $e->getMessage() . "\n";
}

echo "\n" . str_repeat("=", 60) . "\n";
echo "SUMMARY:\n";
echo "If all checks passed (✅), your ML integration is working!\n";
echo "If you see errors (❌), follow the solutions provided.\n";
echo str_repeat("=", 60) . "\n";

echo "</pre>";