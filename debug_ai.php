<?php
// debug_ai.php - Debug version to test AI loading
?>
<!DOCTYPE html>
<html>
<head>
    <title>AI Debug</title>
    <style>
        body { font-family: Arial; padding: 20px; background: #f0f0f0; }
        .debug { background: white; padding: 20px; border-radius: 10px; margin: 10px 0; }
        .success { color: green; }
        .error { color: red; }
        .info { color: blue; }
        pre { background: #f5f5f5; padding: 10px; border-radius: 5px; overflow-x: auto; }
    </style>
</head>
<body>
    <h1>🔍 AI Debug Tool</h1>
    
    <?php
    // Get project ID from URL
    $projectId = isset($_GET['id']) ? $_GET['id'] : '';
    
    echo "<div class='debug'>";
    echo "<h3>1. URL Parameters</h3>";
    echo "<p class='info'>Project ID from URL: <strong>" . htmlspecialchars($projectId) . "</strong></p>";
    echo "<p class='info'>Full URL: " . htmlspecialchars($_SERVER['REQUEST_URI']) . "</p>";
    echo "</div>";
    
    if (empty($projectId)) {
        echo "<div class='debug'>";
        echo "<p class='error'>❌ No project ID provided!</p>";
        echo "<p>Try: <code>debug_ai.php?id=ai_1757245595_68bd709b12551_9750</code></p>";
        echo "</div>";
        exit;
    }
    
    echo "<div class='debug'>";
    echo "<h3>2. File System Check</h3>";
    echo "<p class='info'>Current directory: " . __DIR__ . "</p>";
    echo "<p class='info'>Current file: " . __FILE__ . "</p>";
    
    // List all files in current directory
    $files = scandir(__DIR__);
    echo "<p class='info'>Files in current directory:</p>";
    echo "<pre>";
    foreach ($files as $file) {
        if ($file != '.' && $file != '..') {
            echo "- " . $file;
            if (is_dir(__DIR__ . '/' . $file)) {
                echo " (directory)";
            }
            echo "\n";
        }
    }
    echo "</pre>";
    echo "</div>";
    
    // Function to load AI configuration from multiple storage methods
    function loadAIConfigDebug($projectId) {
        $results = [];
        
        // Method 1: Try individual JSON file in ais directory
        $configFile = __DIR__ . '/ais/' . $projectId . '.json';
        $results['method1_file'] = $configFile;
        $results['method1_exists'] = file_exists($configFile);
        if (file_exists($configFile)) {
            $configData = file_get_contents($configFile);
            $config = json_decode($configData, true);
            $results['method1_valid'] = ($config && isset($config['is_active']) && $config['is_active']);
            $results['method1_data'] = $config;
            if ($config && $config['is_active']) {
                return ['config' => $config, 'method' => 'Individual file in ais/', 'results' => $results];
            }
        }
        
        // Method 2: Try individual JSON file in current directory
        $configFile = __DIR__ . '/' . $projectId . '.json';
        $results['method2_file'] = $configFile;
        $results['method2_exists'] = file_exists($configFile);
        if (file_exists($configFile)) {
            $configData = file_get_contents($configFile);
            $config = json_decode($configData, true);
            $results['method2_valid'] = ($config && isset($config['is_active']) && $config['is_active']);
            $results['method2_data'] = $config;
            if ($config && $config['is_active']) {
                return ['config' => $config, 'method' => 'Individual file in root', 'results' => $results];
            }
        }
        
        // Method 3: Try database file (single JSON file with all AIs)
        $databaseFile = __DIR__ . '/ai_database.json';
        $results['method3_file'] = $databaseFile;
        $results['method3_exists'] = file_exists($databaseFile);
        if (file_exists($databaseFile)) {
            $databaseData = file_get_contents($databaseFile);
            $database = json_decode($databaseData, true);
            $results['method3_valid'] = ($database && isset($database[$projectId]));
            if ($database && isset($database[$projectId])) {
                $config = $database[$projectId];
                $results['method3_active'] = ($config && isset($config['is_active']) && $config['is_active']);
                $results['method3_data'] = $config;
                if ($config && $config['is_active']) {
                    return ['config' => $config, 'method' => 'Database file', 'results' => $results];
                }
            }
        }
        
        return ['config' => null, 'method' => 'None found', 'results' => $results];
    }
    
    // Load AI configuration
    $loadResult = loadAIConfigDebug($projectId);
    $config = $loadResult['config'];
    
    echo "<div class='debug'>";
    echo "<h3>3. AI Configuration Loading</h3>";
    
    foreach ($loadResult['results'] as $key => $value) {
        if (strpos($key, '_file') !== false) {
            echo "<p class='info'><strong>" . $key . ":</strong> " . $value . "</p>";
        } elseif (strpos($key, '_exists') !== false) {
            $status = $value ? '✅ EXISTS' : '❌ NOT FOUND';
            $class = $value ? 'success' : 'error';
            echo "<p class='$class'><strong>" . $key . ":</strong> $status</p>";
        } elseif (strpos($key, '_valid') !== false || strpos($key, '_active') !== false) {
            $status = $value ? '✅ VALID' : '❌ INVALID';
            $class = $value ? 'success' : 'error';
            echo "<p class='$class'><strong>" . $key . ":</strong> $status</p>";
        }
    }
    
    echo "<p class='info'><strong>Method used:</strong> " . $loadResult['method'] . "</p>";
    echo "</div>";
    
    if (!$config) {
        echo "<div class='debug'>";
        echo "<p class='error'>❌ AI Configuration NOT FOUND!</p>";
        echo "<p>Debug info:</p>";
        echo "<pre>" . json_encode($loadResult['results'], JSON_PRETTY_PRINT) . "</pre>";
        echo "</div>";
    } else {
        echo "<div class='debug'>";
        echo "<p class='success'>✅ AI Configuration FOUND!</p>";
        echo "<p class='info'><strong>AI Name:</strong> " . htmlspecialchars($config['ai_name']) . "</p>";
        echo "<p class='info'><strong>Developer:</strong> " . htmlspecialchars($config['developer_name']) . "</p>";
        echo "<p class='info'><strong>Created:</strong> " . htmlspecialchars($config['created_at']) . "</p>";
        echo "<p class='info'><strong>Active:</strong> " . ($config['is_active'] ? 'Yes' : 'No') . "</p>";
        echo "</div>";
        
        echo "<div class='debug'>";
        echo "<h3>4. Full Configuration Data</h3>";
        echo "<pre>" . json_encode($config, JSON_PRETTY_PRINT) . "</pre>";
        echo "</div>";
        
        echo "<div class='debug'>";
        echo "<h3>5. Test Links</h3>";
        echo "<p><a href='ai.php?id=" . urlencode($projectId) . "' target='_blank'>🔗 Test Direct Link: ai.php?id=" . htmlspecialchars($projectId) . "</a></p>";
        echo "<p><a href='ai/" . urlencode($projectId) . "' target='_blank'>🔗 Test Pretty Link: ai/" . htmlspecialchars($projectId) . "</a></p>";
        echo "</div>";
    }
    ?>
    
    <div class='debug'>
        <h3>6. Quick Tests</h3>
        <p><a href="test.php">🧪 Test PHP/JSON</a></p>
        <p><a href="./">🏠 Back to Home</a></p>
    </div>
</body>
</html>