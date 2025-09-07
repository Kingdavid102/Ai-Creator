<?php
// log.php - KSWEB Compatible Chat Logging (Multiple Storage Support)
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Invalid request method']);
    exit;
}

// Get input data
$input = json_decode(file_get_contents('php://input'), true);

if (!$input) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Invalid JSON data']);
    exit;
}

// Validate required fields
$required_fields = ['project_id', 'user_message', 'ai_response'];
foreach ($required_fields as $field) {
    if (empty($input[$field])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => "Missing required field: $field"]);
        exit;
    }
}

// Sanitize inputs
$projectId = htmlspecialchars(trim($input['project_id']));
$userMessage = htmlspecialchars(trim($input['user_message']));
$aiResponse = htmlspecialchars(trim($input['ai_response']));
$userIP = $_SERVER['REMOTE_ADDR'] ?? 'unknown';

try {
    // Check if AI exists using the same method as ai.php
    $aiExists = false;
    
    // Method 1: Check individual JSON file in ais directory
    $configFile = __DIR__ . '/ais/' . $projectId . '.json';
    if (file_exists($configFile)) {
        $aiExists = true;
    }
    // Method 2: Check individual JSON file in current directory
    else {
        $configFile = __DIR__ . '/' . $projectId . '.json';
        if (file_exists($configFile)) {
            $aiExists = true;
        }
        // Method 3: Check database file
        else {
            $databaseFile = __DIR__ . '/ai_database.json';
            if (file_exists($databaseFile)) {
                $databaseData = file_get_contents($databaseFile);
                $database = json_decode($databaseData, true);
                if ($database && isset($database[$projectId])) {
                    $aiExists = true;
                }
            }
        }
    }
    
    if (!$aiExists) {
        http_response_code(404);
        echo json_encode(['success' => false, 'error' => 'Invalid project ID']);
        exit;
    }
    
    // Try to update message count in AI config
    try {
        // Method 1: Individual file in ais directory
        $configFile = __DIR__ . '/ais/' . $projectId . '.json';
        if (file_exists($configFile)) {
            $configData = file_get_contents($configFile);
            $config = json_decode($configData, true);
            if ($config) {
                $config['total_messages'] = isset($config['total_messages']) ? $config['total_messages'] + 1 : 1;
                $config['last_used'] = date('Y-m-d H:i:s');
                @file_put_contents($configFile, json_encode($config, JSON_PRETTY_PRINT));
            }
        }
        // Method 2: Individual file in current directory
        else {
            $configFile = __DIR__ . '/' . $projectId . '.json';
            if (file_exists($configFile)) {
                $configData = file_get_contents($configFile);
                $config = json_decode($configData, true);
                if ($config) {
                    $config['total_messages'] = isset($config['total_messages']) ? $config['total_messages'] + 1 : 1;
                    $config['last_used'] = date('Y-m-d H:i:s');
                    @file_put_contents($configFile, json_encode($config, JSON_PRETTY_PRINT));
                }
            }
            // Method 3: Database file
            else {
                $databaseFile = __DIR__ . '/ai_database.json';
                if (file_exists($databaseFile)) {
                    $databaseData = file_get_contents($databaseFile);
                    $database = json_decode($databaseData, true);
                    if ($database && isset($database[$projectId])) {
                        $database[$projectId]['total_messages'] = isset($database[$projectId]['total_messages']) 
                            ? $database[$projectId]['total_messages'] + 1 : 1;
                        $database[$projectId]['last_used'] = date('Y-m-d H:i:s');
                        @file_put_contents($databaseFile, json_encode($database, JSON_PRETTY_PRINT));
                    }
                }
            }
        }
    } catch (Exception $e) {
        // Ignore errors when updating message count
    }
    
    // Create chat log entry
    $logEntry = [
        'timestamp' => date('Y-m-d H:i:s'),
        'project_id' => $projectId,
        'user_message' => $userMessage,
        'ai_response' => $aiResponse,
        'user_ip' => $userIP
    ];
    
    // Try to save chat logs (multiple approaches for KSWEB compatibility)
    $logSaved = false;
    
    // Approach 1: Try logs directory
    try {
        $logsDir = __DIR__ . '/logs';
        if (!is_dir($logsDir)) {
            @mkdir($logsDir, 0755);
        }
        
        if (is_dir($logsDir)) {
            $logFile = $logsDir . '/chat_' . date('Y-m-d') . '.json';
            
            // Load existing logs or create new array
            $logs = [];
            if (file_exists($logFile)) {
                $existingLogs = file_get_contents($logFile);
                $logs = json_decode($existingLogs, true) ?: [];
            }
            
            // Add new log entry
            $logs[] = $logEntry;
            
            // Save logs
            $result = @file_put_contents($logFile, json_encode($logs, JSON_PRETTY_PRINT));
            if ($result !== false) {
                $logSaved = true;
            }
        }
    } catch (Exception $e) {
        // Try next approach
    }
    
    // Approach 2: Save in current directory
    if (!$logSaved) {
        try {
            $logFile = __DIR__ . '/chat_logs_' . date('Y-m-d') . '.json';
            
            // Load existing logs or create new array
            $logs = [];
            if (file_exists($logFile)) {
                $existingLogs = file_get_contents($logFile);
                $logs = json_decode($existingLogs, true) ?: [];
            }
            
            // Add new log entry
            $logs[] = $logEntry;
            
            // Save logs
            $result = @file_put_contents($logFile, json_encode($logs, JSON_PRETTY_PRINT));
            if ($result !== false) {
                $logSaved = true;
            }
        } catch (Exception $e) {
            // Try next approach
        }
    }
    
    // Approach 3: Append to single log file
    if (!$logSaved) {
        try {
            $logFile = __DIR__ . '/all_chat_logs.json';
            
            // Load existing logs or create new array
            $logs = [];
            if (file_exists($logFile)) {
                $existingLogs = file_get_contents($logFile);
                $logs = json_decode($existingLogs, true) ?: [];
            }
            
            // Add new log entry
            $logs[] = $logEntry;
            
            // Keep only last 1000 entries to prevent file from getting too large
            if (count($logs) > 1000) {
                $logs = array_slice($logs, -1000);
            }
            
            // Save logs
            $result = @file_put_contents($logFile, json_encode($logs, JSON_PRETTY_PRINT));
            if ($result !== false) {
                $logSaved = true;
            }
        } catch (Exception $e) {
            // Continue without logging
        }
    }
    
    echo json_encode([
        'success' => true,
        'message' => 'Interaction processed successfully',
        'logged' => $logSaved
    ]);
    
} catch(Exception $e) {
    error_log("Chat logging error: " . $e->getMessage());
    // Don't fail the request if logging fails
    echo json_encode([
        'success' => true,
        'message' => 'Response processed (logging may have failed)',
        'error' => $e->getMessage()
    ]);
}
?>