<?php
/**
 * Debug version of installer - shows PHP errors
 * Use this if install.php gives 500 error
 */

// Enable error reporting for debugging
@error_reporting(E_ALL);
@ini_set('display_errors', 1);
@ini_set('display_startup_errors', 1);

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Installation Debug</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; background: #f5f5f5; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 20px; border-radius: 5px; }
        h1 { color: #333; }
        h2 { color: #666; border-bottom: 2px solid #eee; padding-bottom: 10px; }
        ul { list-style: none; padding: 0; }
        li { padding: 5px 0; }
        .success { color: green; }
        .error { color: red; }
        .warning { color: orange; }
        pre { background: #f0f0f0; padding: 10px; border-radius: 3px; overflow-x: auto; }
    </style>
</head>
<body>
<div class="container">
    <h1>🔍 PHP Debug Information</h1>
    
    <?php
    try {
        echo "<h2>Basic PHP Info</h2>";
        echo "<ul>";
        echo "<li><strong>PHP Version:</strong> " . htmlspecialchars(PHP_VERSION) . "</li>";
        $serverSoftware = isset($_SERVER['SERVER_SOFTWARE']) ? $_SERVER['SERVER_SOFTWARE'] : 'Unknown';
        echo "<li><strong>Server:</strong> " . htmlspecialchars($serverSoftware) . "</li>";
        $docRoot = isset($_SERVER['DOCUMENT_ROOT']) ? $_SERVER['DOCUMENT_ROOT'] : 'Unknown';
        echo "<li><strong>Document Root:</strong> " . htmlspecialchars($docRoot) . "</li>";
        echo "<li><strong>Script Path:</strong> " . htmlspecialchars(__FILE__) . "</li>";
        echo "</ul>";
        
        echo "<h2>Required Extensions</h2>";
        $required = ['pdo', 'pdo_mysql', 'curl', 'session'];
        echo "<ul>";
        foreach ($required as $ext) {
            $loaded = extension_loaded($ext);
            $class = $loaded ? 'success' : 'error';
            $icon = $loaded ? '✅' : '❌';
            echo "<li class='{$class}'>{$icon} <strong>{$ext}</strong>: " . ($loaded ? "Loaded" : "Missing") . "</li>";
        }
        echo "</ul>";
        
        echo "<h2>File Permissions</h2>";
        $dirs = ['config', 'cache', 'uploads', 'logs'];
        echo "<ul>";
        foreach ($dirs as $dir) {
            $path = __DIR__ . '/' . $dir;
            $exists = is_dir($path) || file_exists($path);
            $writable = $exists ? @is_writable($path) : false;
            $class = $exists && $writable ? 'success' : ($exists ? 'warning' : 'error');
            echo "<li class='{$class}'><strong>{$dir}/</strong>: " . 
                 ($exists ? "Exists" : "Missing") . 
                 ($exists ? (" - " . ($writable ? "Writable ✅" : "Not Writable ❌")) : "") . 
                 "</li>";
        }
        echo "</ul>";
        
        echo "<h2>File Existence Check</h2>";
        $files = ['install.php', 'bootstrap.php', 'config/config.php', '.htaccess'];
        echo "<ul>";
        foreach ($files as $file) {
            $path = __DIR__ . '/' . $file;
            $exists = file_exists($path);
            $class = $exists ? 'success' : 'error';
            $icon = $exists ? '✅' : '❌';
            echo "<li class='{$class}'>{$icon} <strong>{$file}</strong>: " . ($exists ? "Exists" : "Missing") . "</li>";
        }
        echo "</ul>";
        
        echo "<h2>PHP Configuration</h2>";
        echo "<ul>";
        $errorReporting = ini_get('error_reporting');
        echo "<li><strong>Error Reporting:</strong> " . ($errorReporting ? $errorReporting : 'Not set') . "</li>";
        echo "<li><strong>Display Errors:</strong> " . (ini_get('display_errors') ? 'On' : 'Off') . "</li>";
        echo "<li><strong>Max Execution Time:</strong> " . ini_get('max_execution_time') . " seconds</li>";
        echo "<li><strong>Memory Limit:</strong> " . ini_get('memory_limit') . "</li>";
        echo "<li><strong>Upload Max Size:</strong> " . ini_get('upload_max_filesize') . "</li>";
        echo "</ul>";
        
        echo "<h2>Syntax Check</h2>";
        $installFile = __DIR__ . '/install.php';
        if (file_exists($installFile)) {
            // Simple syntax check using tokenizer
            $tokens = @token_get_all(file_get_contents($installFile));
            $hasError = false;
            foreach ($tokens as $token) {
                if (is_array($token) && $token[0] === T_OPEN_TAG) {
                    break;
                }
            }
            echo "<p class='success'>✅ install.php file exists and is readable</p>";
            
            // Try to check if it can be parsed
            $code = @file_get_contents($installFile);
            if ($code !== false) {
                // Check for basic PHP syntax
                if (strpos($code, '<?php') !== false) {
                    echo "<p class='success'>✅ File contains PHP code</p>";
                }
            }
        } else {
            echo "<p class='error'>❌ install.php not found</p>";
        }
        
        echo "<h2>Directory Structure</h2>";
        $checkDirs = ['classes', 'database', 'admin', 'api', 'includes'];
        echo "<ul>";
        foreach ($checkDirs as $dir) {
            $path = __DIR__ . '/' . $dir;
            $exists = is_dir($path);
            $class = $exists ? 'success' : 'error';
            $icon = $exists ? '✅' : '❌';
            echo "<li class='{$class}'>{$icon} <strong>{$dir}/</strong>: " . ($exists ? "Exists" : "Missing") . "</li>";
        }
        echo "</ul>";
        
    } catch (Exception $e) {
        echo "<h2>Error</h2>";
        echo "<p class='error'>An error occurred: " . htmlspecialchars($e->getMessage()) . "</p>";
        echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
    } catch (Error $e) {
        // PHP 7+ Error class
        echo "<h2>Fatal Error</h2>";
        echo "<p class='error'>A fatal error occurred: " . htmlspecialchars($e->getMessage()) . "</p>";
        echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
    }
    ?>
    
    <hr>
    <p><a href="install.php">Try install.php again</a></p>
    <p><strong>Note:</strong> If you see errors above, please check your server's PHP error logs for more details.</p>
</div>
</body>
</html>

