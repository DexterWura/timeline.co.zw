<?php
/**
 * Debug version of installer - shows PHP errors
 * Use this if install.php gives 500 error
 */

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

echo "<h1>PHP Debug Info</h1>";
echo "<p>PHP Version: " . PHP_VERSION . "</p>";
echo "<p>Server: " . ($_SERVER['SERVER_SOFTWARE'] ?? 'Unknown') . "</p>";

// Check required extensions
$required = ['pdo', 'pdo_mysql', 'curl', 'session'];
echo "<h2>Required Extensions:</h2><ul>";
foreach ($required as $ext) {
    $loaded = extension_loaded($ext);
    echo "<li>{$ext}: " . ($loaded ? "✅ Loaded" : "❌ Missing") . "</li>";
}
echo "</ul>";

// Check file permissions
echo "<h2>File Permissions:</h2><ul>";
$dirs = ['config', 'cache', 'uploads', 'logs'];
foreach ($dirs as $dir) {
    $path = __DIR__ . '/' . $dir;
    $exists = is_dir($path) || file_exists($path);
    $writable = $exists ? is_writable($path) : false;
    echo "<li>{$dir}: " . ($exists ? "Exists" : "Missing") . " - " . ($writable ? "Writable" : "Not Writable") . "</li>";
}
echo "</ul>";

// Check if install.php syntax is valid
echo "<h2>Syntax Check:</h2>";
$installFile = __DIR__ . '/install.php';
if (file_exists($installFile)) {
    $output = [];
    $return = 0;
    exec("php -l " . escapeshellarg($installFile) . " 2>&1", $output, $return);
    if ($return === 0) {
        echo "<p style='color: green;'>✅ install.php syntax is valid</p>";
    } else {
        echo "<p style='color: red;'>❌ install.php has syntax errors:</p>";
        echo "<pre>" . htmlspecialchars(implode("\n", $output)) . "</pre>";
    }
} else {
    echo "<p style='color: red;'>❌ install.php not found</p>";
}

// Try to include install.php
echo "<h2>Testing install.php:</h2>";
try {
    ob_start();
    include $installFile;
    $output = ob_get_clean();
    if (!empty($output)) {
        echo "<p style='color: green;'>✅ install.php executed successfully</p>";
        echo "<p>Output length: " . strlen($output) . " bytes</p>";
    } else {
        echo "<p style='color: orange;'>⚠️ install.php executed but produced no output</p>";
    }
} catch (Throwable $e) {
    echo "<p style='color: red;'>❌ Error executing install.php:</p>";
    echo "<pre>" . htmlspecialchars($e->getMessage()) . "\n" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
}

echo "<hr>";
echo "<p><a href='install.php'>Try install.php again</a></p>";

