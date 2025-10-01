<?php
// Minimal test to isolate the 500 error
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Simple Test</h1>";
echo "<p>PHP Version: " . PHP_VERSION . "</p>";

// Test session
try {
    session_start();
    echo "<p>✅ Session started</p>";
} catch (Exception $e) {
    echo "<p>❌ Session error: " . $e->getMessage() . "</p>";
}

// Test file operations
echo "<p>Current directory: " . getcwd() . "</p>";
echo "<p>Directory writable: " . (is_writable('.') ? 'Yes' : 'No') . "</p>";

// Test directory creation
if (!is_dir('config')) {
    if (@mkdir('config', 0755, true)) {
        echo "<p>✅ Created config directory</p>";
    } else {
        echo "<p>❌ Failed to create config directory</p>";
    }
} else {
    echo "<p>✅ Config directory exists</p>";
}

// Test .env file
if (file_exists('.env')) {
    echo "<p>✅ .env file exists</p>";
} else {
    echo "<p>⚠️ .env file does not exist</p>";
}

// Test installed.lock
if (file_exists('config/installed.lock')) {
    echo "<p>✅ Installation lock exists</p>";
} else {
    echo "<p>⚠️ Installation lock does not exist</p>";
}

echo "<h2>Next Steps</h2>";
echo "<p><a href='install.php'>Run Installation</a></p>";
echo "<p><a href='test.php'>Run Full Test</a></p>";
?>
