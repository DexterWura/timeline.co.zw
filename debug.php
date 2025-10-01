<?php
// Comprehensive debug script
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>🔧 Timeline.co.zw Debug Tool</h1>";

// Test 1: Basic PHP
echo "<h2>✅ Test 1: Basic PHP</h2>";
echo "<p>PHP Version: " . PHP_VERSION . "</p>";
echo "<p>Server: " . ($_SERVER['SERVER_SOFTWARE'] ?? 'Unknown') . "</p>";
echo "<p>Document Root: " . ($_SERVER['DOCUMENT_ROOT'] ?? 'Unknown') . "</p>";

// Test 2: Extensions
echo "<h2>✅ Test 2: PHP Extensions</h2>";
$required = ['curl', 'json', 'mbstring', 'openssl'];
foreach ($required as $ext) {
    $status = extension_loaded($ext) ? '✅' : '❌';
    echo "<p>{$status} {$ext}</p>";
}

// Test 3: File System
echo "<h2>✅ Test 3: File System</h2>";
echo "<p>Current Directory: " . getcwd() . "</p>";
echo "<p>Directory Writable: " . (is_writable('.') ? '✅ Yes' : '❌ No') . "</p>";

// Test 4: Directory Creation
echo "<h2>✅ Test 4: Directory Creation</h2>";
$dirs = ['config', 'cache', 'logs'];
foreach ($dirs as $dir) {
    if (!is_dir($dir)) {
        if (@mkdir($dir, 0755, true)) {
            echo "<p>✅ Created {$dir}/</p>";
        } else {
            echo "<p>❌ Failed to create {$dir}/</p>";
        }
    } else {
        echo "<p>✅ {$dir}/ exists</p>";
    }
}

// Test 5: File Operations
echo "<h2>✅ Test 5: File Operations</h2>";
$test_file = 'test_write.txt';
if (@file_put_contents($test_file, 'test')) {
    echo "<p>✅ Can write files</p>";
    @unlink($test_file);
} else {
    echo "<p>❌ Cannot write files</p>";
}

// Test 6: Session
echo "<h2>✅ Test 6: Session</h2>";
try {
    session_start();
    echo "<p>✅ Session started</p>";
} catch (Exception $e) {
    echo "<p>❌ Session error: " . $e->getMessage() . "</p>";
}

// Test 7: .htaccess
echo "<h2>✅ Test 7: .htaccess</h2>";
if (file_exists('.htaccess')) {
    echo "<p>✅ .htaccess exists</p>";
    echo "<p><strong>⚠️ If you're getting 500 errors, try renaming .htaccess temporarily:</strong></p>";
    echo "<p><code>mv .htaccess .htaccess.backup</code></p>";
} else {
    echo "<p>⚠️ .htaccess does not exist</p>";
}

// Test 8: Environment
echo "<h2>✅ Test 8: Environment</h2>";
echo "<p>PHP SAPI: " . php_sapi_name() . "</p>";
echo "<p>Memory Limit: " . ini_get('memory_limit') . "</p>";
echo "<p>Max Execution Time: " . ini_get('max_execution_time') . "</p>";
echo "<p>Upload Max Filesize: " . ini_get('upload_max_filesize') . "</p>";

// Test 9: Error Reporting
echo "<h2>✅ Test 9: Error Reporting</h2>";
echo "<p>Display Errors: " . (ini_get('display_errors') ? 'On' : 'Off') . "</p>";
echo "<p>Error Log: " . (ini_get('error_log') ?: 'Not set') . "</p>";

// Test 10: Simple Installation Test
echo "<h2>✅ Test 10: Installation Test</h2>";
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['test_install'])) {
    $api_key = $_POST['api_key'] ?? '';
    if (!empty($api_key)) {
        $env_content = "YOUTUBE_API_KEY={$api_key}\nSITE_URL=https://timeline.co.zw\n";
        if (file_put_contents('.env', $env_content)) {
            file_put_contents('config/installed.lock', date('Y-m-d H:i:s'));
            echo "<p style='color: green; font-weight: bold;'>✅ Installation successful!</p>";
            echo "<p><a href='index.php'>Go to main site</a></p>";
        } else {
            echo "<p style='color: red;'>❌ Failed to create .env file</p>";
        }
    }
}

echo "<form method='POST' style='background: #f0f0f0; padding: 20px; margin: 20px 0; border-radius: 5px;'>";
echo "<h3>Quick Installation Test</h3>";
echo "<p><label>YouTube API Key: <input type='text' name='api_key' style='width: 300px;'></label></p>";
echo "<p><button type='submit' name='test_install'>Test Installation</button></p>";
echo "</form>";

echo "<h2>🔧 Troubleshooting Steps</h2>";
echo "<ol>";
echo "<li><strong>If you see 500 errors:</strong> Rename .htaccess to .htaccess.backup</li>";
echo "<li><strong>If directories are missing:</strong> Create them manually: <code>mkdir -p config cache logs</code></li>";
echo "<li><strong>If file permissions are wrong:</strong> Set them: <code>chmod 755 config cache logs</code></li>";
echo "<li><strong>If PHP extensions are missing:</strong> Contact your hosting provider</li>";
echo "<li><strong>If nothing works:</strong> Check your server error logs</li>";
echo "</ol>";

echo "<h2>📁 Test Files</h2>";
echo "<ul>";
echo "<li><a href='basic.php'>basic.php</a> - Minimal PHP test</li>";
echo "<li><a href='info.php'>info.php</a> - PHP information</li>";
echo "<li><a href='test.html'>test.html</a> - HTML test (no PHP)</li>";
echo "</ul>";

echo "<p><strong>Last updated:</strong> " . date('Y-m-d H:i:s') . "</p>";
?>
