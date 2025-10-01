<?php
// Minimal installation test
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

// Ensure directories exist
if (!is_dir('config')) {
    @mkdir('config', 0755, true);
}

echo "<h1>Minimal Installation Test</h1>";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $youtube_api_key = $_POST['youtube_api_key'] ?? '';
    
    if (empty($youtube_api_key)) {
        echo "<p style='color: red;'>YouTube API Key is required</p>";
    } else {
        // Create .env file
        $env_content = "YOUTUBE_API_KEY={$youtube_api_key}\nSITE_URL=https://timeline.co.zw\n";
        
        if (file_put_contents('.env', $env_content)) {
            // Create lock file
            file_put_contents('config/installed.lock', date('Y-m-d H:i:s'));
            echo "<p style='color: green;'>✅ Installation successful!</p>";
            echo "<p><a href='index.php'>Go to site</a></p>";
        } else {
            echo "<p style='color: red;'>❌ Failed to create .env file</p>";
        }
    }
}
?>

<form method="POST">
    <h2>Quick Installation</h2>
    <p>
        <label>YouTube API Key:</label><br>
        <input type="text" name="youtube_api_key" required style="width: 300px;">
    </p>
    <p>
        <button type="submit">Install</button>
    </p>
</form>

<p><a href="simple-test.php">Back to Simple Test</a></p>
