<?php
// includes/ml-emotion-detector.php - Machine Learning Emotion Detection

require_once __DIR__ . '/../vendor/autoload.php';

use Rubix\ML\Classifiers\KNearestNeighbors;
use Rubix\ML\Datasets\Labeled;
use Rubix\ML\Datasets\Unlabeled;
use Rubix\ML\Kernels\Distance\Manhattan;
use Rubix\ML\Transformers\TfIdfTransformer;
use Rubix\ML\Transformers\WordCountVectorizer;
use Rubix\ML\Pipeline;
use Rubix\ML\PersistentModel;
use Rubix\ML\Persisters\Filesystem;

class MLEmotionDetector {
    private $model;
    private $modelPath;
    
    public function __construct() {
        $this->modelPath = __DIR__ . '/../models/emotion_classifier.rbx';
        $this->initializeModel();
    }
    
    /**
     * Initialize or load the trained model
     */
    private function initializeModel() {
        // Check if models directory is writable
        $modelsDir = dirname($this->modelPath);
        $canSave = is_writable($modelsDir);
        
        if ($canSave && file_exists($this->modelPath)) {
            // Load existing model
            try {
                $persister = new Filesystem($this->modelPath);
                $this->model = PersistentModel::load($persister);
                return;
            } catch (Exception $e) {
                error_log('Failed to load model: ' . $e->getMessage());
            }
        }
        
        // Train a new model (but don't save if can't write)
        $this->trainModel($canSave);
    }
    
    /**
     * Train the emotion detection model with sample data
     */
    private function trainModel($saveModel = true) {
        // Training dataset with emotion-labeled texts
        $samples = [
            // Sad emotions
            ["I'm feeling so down today", "I miss you", "This is heartbreaking", "I feel lonely and empty", 
             "Crying all night", "Lost and confused", "Everything feels hopeless", "I'm so depressed",
             "Tears won't stop", "My heart is broken", "Why did this happen", "I feel so sad",
             "Nothing makes me happy anymore", "I'm hurting so much", "Feeling blue today"],
            
            // Happy emotions
            ["I'm so excited!", "This is amazing!", "Best day ever!", "I love this feeling",
             "Feeling blessed and grateful", "Pure joy right now", "I'm on cloud nine", "So much fun!",
             "This makes me smile", "Awesome vibes", "Celebrating life", "Feeling fantastic",
             "I'm so happy!", "What a wonderful day", "Everything is perfect"],
            
            // Calm emotions
            ["I feel peaceful", "So relaxed right now", "Finding my zen", "Quiet morning meditation",
             "Just breathing and being present", "Tranquil moments", "Serenity and calm", "Still waters",
             "Peace of mind", "Centered and balanced", "Mindful and present", "Gentle and calm",
             "Taking it slow", "Quiet contemplation", "Inner peace"],
            
            // Energetic emotions
            ["Let's go!", "Pumped up and ready!", "Energy levels through the roof!", "Time to workout!",
             "Adrenaline rush!", "I'm fired up!", "Let's party!", "Full of energy", "Ready to dance!",
             "Bring it on!", "Feeling powerful", "Unstoppable force", "High energy vibes",
             "Let's do this!", "Action time!"]
        ];
        
        $labels = [
            ...array_fill(0, 15, 'sad'),
            ...array_fill(0, 15, 'happy'),
            ...array_fill(0, 15, 'calm'),
            ...array_fill(0, 15, 'energetic')
        ];
        
        // Flatten samples array
        $flatSamples = array_merge(...$samples);
        
        // Create labeled dataset
        $dataset = new Labeled($flatSamples, $labels);
        
        // Create pipeline with text processing and KNN classifier
        $estimator = new Pipeline([
            new WordCountVectorizer(10000),
            new TfIdfTransformer()
        ], new KNearestNeighbors(5, true, new Manhattan()));
        
        // Train the model
        $estimator->train($dataset);
        
        // Save the model only if directory is writable
        if ($saveModel) {
            try {
                $this->model = new PersistentModel($estimator, new Filesystem($this->modelPath));
                $this->model->save();
            } catch (Exception $e) {
                error_log('Could not save model: ' . $e->getMessage());
                // Use the estimator directly without persistence
                $this->model = $estimator;
            }
        } else {
            // Use the estimator directly without persistence
            $this->model = $estimator;
        }
    }
    
    /**
     * Predict emotion from user text input
     * 
     * @param string $text User's input text
     * @return string Predicted emotion (sad, happy, calm, energetic)
     */
    public function predictEmotion($text) {
        if (empty(trim($text))) {
            return 'calm'; // Default emotion
        }
        
        try {
            // Prepare data for prediction
            $dataset = new Unlabeled([$text]);
            
            // Make prediction (works with both PersistentModel and regular estimator)
            if ($this->model instanceof PersistentModel) {
                $predictions = $this->model->predict($dataset);
            } else {
                $predictions = $this->model->predict($dataset);
            }
            
            return $predictions[0] ?? 'calm';
        } catch (Exception $e) {
            error_log('ML Prediction Error: ' . $e->getMessage());
            // Fallback to keyword-based detection
            return $this->fallbackKeywordDetection($text);
        }
    }
    
    /**
     * Fallback keyword-based emotion detection
     * 
     * @param string $text Input text
     * @return string Detected emotion
     */
    private function fallbackKeywordDetection($text) {
        $text = strtolower($text);
        
        $emotionKeywords = [
            'sad' => ['sad', 'down', 'depressed', 'lonely', 'hurt', 'crying', 'heartbroken', 
                     'miss', 'blue', 'unhappy', 'upset', 'terrible', 'awful', 'pain', 'tears'],
            'happy' => ['happy', 'joy', 'excited', 'great', 'amazing', 'wonderful', 'love', 
                       'celebrate', 'awesome', 'fantastic', 'good', 'excellent', 'blessed', 'smile'],
            'calm' => ['calm', 'peace', 'relax', 'chill', 'meditate', 'tranquil', 'zen', 
                      'quiet', 'serene', 'peaceful', 'tired', 'sleepy', 'still', 'gentle'],
            'energetic' => ['energy', 'workout', 'pump', 'active', 'dance', 'party', 'hype', 
                           'motivated', 'powerful', 'intense', 'pumped', 'excited', 'go', 'action']
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
        return key($scores) ?: 'calm';
    }
    
    /**
     * Get confidence score for the prediction
     * 
     * @param string $text Input text
     * @return array ['emotion' => string, 'confidence' => float]
     */
    public function predictWithConfidence($text) {
        try {
            $dataset = new Unlabeled([$text]);
            
            // Get predictions
            if ($this->model instanceof PersistentModel) {
                $predictions = $this->model->predict($dataset);
                $probabilities = $this->model->proba($dataset);
            } else {
                $predictions = $this->model->predict($dataset);
                $probabilities = $this->model->proba($dataset);
            }
            
            $emotion = $predictions[0];
            $confidence = max($probabilities[0]);
            
            return [
                'emotion' => $emotion,
                'confidence' => round($confidence * 100, 2),
                'all_probabilities' => $probabilities[0]
            ];
        } catch (Exception $e) {
            error_log('ML Confidence Error: ' . $e->getMessage());
            $emotion = $this->fallbackKeywordDetection($text);
            return [
                'emotion' => $emotion,
                'confidence' => 75.0,
                'all_probabilities' => [$emotion => 0.75]
            ];
        }
    }
    
    /**
     * Retrain model with new data
     * 
     * @param array $texts Array of text samples
     * @param array $labels Array of corresponding emotion labels
     */
    public function retrainWithNewData($texts, $labels) {
        try {
            $dataset = new Labeled($texts, $labels);
            
            // Get existing model estimator
            $estimator = $this->model->base();
            
            // Partially train with new data
            $estimator->partial($dataset);
            
            // Save updated model
            $this->model->save();
            
            return true;
        } catch (Exception $e) {
            error_log('Model Retraining Error: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Analyze multiple texts and return emotion distribution
     * 
     * @param array $texts Array of text samples
     * @return array Emotion distribution statistics
     */
    public function analyzeBatch($texts) {
        $emotions = [];
        foreach ($texts as $text) {
            $emotion = $this->predictEmotion($text);
            $emotions[] = $emotion;
        }
        
        $distribution = array_count_values($emotions);
        $total = count($emotions);
        
        $percentages = [];
        foreach ($distribution as $emotion => $count) {
            $percentages[$emotion] = round(($count / $total) * 100, 2);
        }
        
        return [
            'total_analyzed' => $total,
            'distribution' => $distribution,
            'percentages' => $percentages,
            'dominant_emotion' => array_search(max($distribution), $distribution)
        ];
    }
}