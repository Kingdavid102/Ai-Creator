<?php
// create_ai.php - KSWEB Compatible Version (Simplified File Handling)
// Disable HTML error output and force JSON
ini_set('display_errors', 0);
ini_set('log_errors', 1);
error_reporting(0);

// Start output buffering to catch any unexpected output
ob_start();

// Set headers first
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

function sendJsonResponse($data) {
    // Clear any buffered output
    ob_clean();
    echo json_encode($data);
    exit;
}

function sendError($message, $code = 400) {
    http_response_code($code);
    sendJsonResponse(['success' => false, 'error' => $message]);
}

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        sendError('Invalid request method', 405);
    }

    // Get input data
    $rawInput = file_get_contents('php://input');
    
    if (empty($rawInput)) {
        sendError('No input data received');
    }
    
    $input = json_decode($rawInput, true);
    
    if (json_last_error() !== JSON_ERROR_NONE) {
        sendError('Invalid JSON data: ' . json_last_error_msg());
    }

    // Validate required fields
    $required_fields = ['aiName', 'developerName', 'country', 'backgroundColor', 'textColor', 'personality'];
    foreach ($required_fields as $field) {
        if (!isset($input[$field]) || trim($input[$field]) === '') {
            sendError("Missing or empty required field: $field");
        }
    }

    // Sanitize inputs
    $aiName = htmlspecialchars(trim($input['aiName']));
    $developerName = htmlspecialchars(trim($input['developerName']));
    $country = htmlspecialchars(trim($input['country']));
    $backgroundColor = trim($input['backgroundColor']);
    $textColor = trim($input['textColor']);
    $personality = trim($input['personality']);
    $greeting = isset($input['greeting']) && !empty(trim($input['greeting'])) 
        ? htmlspecialchars(trim($input['greeting'])) 
        : "Hi! I'm $aiName, your AI assistant. How can I help you today?";

    // Validate inputs
    if (strlen($aiName) > 50) {
        sendError('AI name is too long. Maximum 50 characters allowed.');
    }

    if (strlen($greeting) > 500) {
        sendError('Greeting message is too long. Maximum 500 characters allowed.');
    }

    // Validate color format
    if (!preg_match('/^#[a-fA-F0-9]{6}$/', $backgroundColor)) {
        sendError('Invalid background color format. Please use hex colors (e.g., #667eea).');
    }
    
    if (!preg_match('/^#[a-fA-F0-9]{6}$/', $textColor)) {
        sendError('Invalid text color format. Please use hex colors (e.g., #ffffff).');
    }

    // Validate personality
    $validPersonalities = ['friendly', 'professional', 'casual', 'creative', 'technical', 'humorous'];
    if (!in_array($personality, $validPersonalities)) {
        sendError('Invalid personality type: ' . $personality);
    }

    // Generate unique project ID
    $projectId = 'ai_' . time() . '_' . uniqid() . '_' . mt_rand(1000, 9999);

    // Create personality prompts
    $personalityPrompts = [
        'friendly' => "You are $aiName, a friendly and helpful AI assistant created by $developerName. You're warm, approachable, and always eager to help with a positive attitude.",
        'professional' => "You are $aiName, a professional AI assistant developed by $developerName. You maintain a formal, business-like tone and provide precise, well-structured responses.",
        'casual' => "You are $aiName, a laid-back AI assistant made by $developerName. You're casual, relaxed, and speak in a conversational, informal way.",
        'creative' => "You are $aiName, a creative AI assistant built by $developerName. You're imaginative, artistic, and love to think outside the box with innovative ideas.",
        'technical' => "You are $aiName, a technical AI assistant engineered by $developerName. You provide detailed, accurate technical information and prefer precise, analytical responses.",
        'humorous' => "You are $aiName, a fun-loving AI assistant created by $developerName. You enjoy humor, jokes, and making conversations light and entertaining."
    ];

    $systemPrompt = $personalityPrompts[$personality];

    // Create AI configuration
    $aiConfig = [
        'project_id' => $projectId,
        'ai_name' => $aiName,
        'developer_name' => $developerName,
        'country' => $country,
        'background_color' => $backgroundColor,
        'text_color' => $textColor,
        'personality' => $personality,
        'greeting' => $greeting,
        'system_prompt' => $systemPrompt,
        'created_at' => date('Y-m-d H:i:s'),
        'is_active' => true,
        'total_messages' => 0,
        'last_used' => date('Y-m-d H:i:s')
    ];

    // Try multiple approaches for KSWEB compatibility
    $success = false;
    $configFile = '';
    $errorDetails = [];
    
    // Approach 1: Try to create ais directory in current folder
    try {
        $aisDir = __DIR__ . '/ais';
        if (!is_dir($aisDir)) {
            $created = @mkdir($aisDir, 0755);
            if (!$created) {
                // Try with different permissions
                $created = @mkdir($aisDir, 0777);
            }
        }
        
        if (is_dir($aisDir) && is_writable($aisDir)) {
            $configFile = $aisDir . '/' . $projectId . '.json';
            $jsonData = json_encode($aiConfig, JSON_PRETTY_PRINT);
            
            // Try to write the file
            $result = @file_put_contents($configFile, $jsonData);
            if ($result !== false && $result > 0) {
                $success = true;
            } else {
                $errorDetails[] = "Failed to write to $configFile";
            }
        } else {
            $errorDetails[] = "Directory $aisDir not writable";
        }
    } catch (Exception $e) {
        $errorDetails[] = "Approach 1 failed: " . $e->getMessage();
    }
    
    // Approach 2: Try to save in same directory as this script
    if (!$success) {
        try {
            $configFile = __DIR__ . '/' . $projectId . '.json';
            $jsonData = json_encode($aiConfig, JSON_PRETTY_PRINT);
            
            $result = @file_put_contents($configFile, $jsonData);
            if ($result !== false && $result > 0) {
                $success = true;
            } else {
                $errorDetails[] = "Failed to write to current directory: $configFile";
            }
        } catch (Exception $e) {
            $errorDetails[] = "Approach 2 failed: " . $e->getMessage();
        }
    }
    
    // Approach 3: Use a single JSON database file (most compatible with KSWEB)
    if (!$success) {
        try {
            $databaseFile = __DIR__ . '/ai_database.json';
            
            // Load existing database or create new one
            $database = [];
            if (file_exists($databaseFile)) {
                $existingData = @file_get_contents($databaseFile);
                if ($existingData) {
                    $database = json_decode($existingData, true) ?: [];
                }
            }
            
            // Add new AI to database
            $database[$projectId] = $aiConfig;
            
            // Save database
            $result = @file_put_contents($databaseFile, json_encode($database, JSON_PRETTY_PRINT));
            if ($result !== false && $result > 0) {
                $success = true;
                $configFile = $databaseFile;
            } else {
                $errorDetails[] = "Failed to write to database file: $databaseFile";
            }
        } catch (Exception $e) {
            $errorDetails[] = "Approach 3 failed: " . $e->getMessage();
        }
    }
    
    if (!$success) {
        sendError('Unable to save AI configuration. KSWEB write permissions denied. Details: ' . implode('; ', $errorDetails));
    }
    
    // Generate AI link (use direct link for KSWEB compatibility)
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'];
    $siteUrl = $protocol . '://' . $host;
    $aiLink = $siteUrl . "/ai.php?id=" . $projectId; // Direct link instead of pretty URL
    
    // Success response
    sendJsonResponse([
        'success' => true,
        'project_id' => $projectId,
        'ai_link' => $aiLink,
        'message' => 'AI created successfully! Your custom AI assistant is now live.',
        'ai_name' => $aiName,
        'developer_name' => $developerName,
        'storage_method' => $configFile
    ]);
    
} catch (Exception $e) {
    error_log("Create AI Exception: " . $e->getMessage());
    sendError('Server error: ' . $e->getMessage(), 500);
} catch (Error $e) {
    error_log("Create AI Fatal Error: " . $e->getMessage());
    sendError('Fatal server error occurred.', 500);
}

// This should never be reached, but just in case
sendError('Unexpected error occurred.', 500);
?>