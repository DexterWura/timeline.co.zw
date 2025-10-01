<?php
// Simple diagnostic script to check server configuration
header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Server Diagnostics - Timeline.co.zw</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .status { padding: 10px; margin: 10px 0; border-radius: 4px; }
        .success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .warning { background: #fff3cd; color: #856404; border: 1px solid #ffeaa7; }
        .info { background: #d1ecf1; color: #0c5460; border: 1px solid #bee5eb; }
        h1 { color: #333; }
        h2 { color: #666; margin-top: 30px; }
        pre { background: #f8f9fa; padding: 10px; border-radius: 4px; overflow-x: auto; }
        .check { margin: 5px 0; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔧 Timeline.co.zw Server Diagnostics</h1>
        
        <h2>PHP Configuration</h2>
        <div class="check">
            <strong>PHP Version:</strong> 
            <?php if (version_compare(PHP_VERSION, '7.4.0', '>=')): ?>
                <span class="status success">✅ <?php echo PHP_VERSION; ?> (OK)</span>
            <?php else: ?>
                <span class="status error">❌ <?php echo PHP_VERSION; ?> (Requires PHP 7.4+)</span>
            <?php endif; ?>
        </div>
        
        <div class="check">
            <strong>Required Extensions:</strong>
            <?php
            $required_extensions = ['curl', 'json', 'mbstring', 'openssl'];
            $missing_extensions = [];
            foreach ($required_extensions as $ext) {
                if (!extension_loaded($ext)) {
                    $missing_extensions[] = $ext;
                }
            }
            if (empty($missing_extensions)): ?>
                <span class="status success">✅ All required extensions loaded</span>
            <?php else: ?>
                <span class="status error">❌ Missing: <?php echo implode(', ', $missing_extensions); ?></span>
            <?php endif; ?>
        </div>
        
        <h2>File System</h2>
        <div class="check">
            <strong>Cache Directory:</strong>
            <?php if (is_dir('cache') && is_writable('cache')): ?>
                <span class="status success">✅ Exists and writable</span>
            <?php elseif (is_dir('cache')): ?>
                <span class="status error">❌ Exists but not writable</span>
            <?php else: ?>
                <span class="status warning">⚠️ Does not exist</span>
            <?php endif; ?>
        </div>
        
        <div class="check">
            <strong>Config Directory:</strong>
            <?php if (is_dir('config')): ?>
                <span class="status success">✅ Exists</span>
            <?php else: ?>
                <span class="status error">❌ Does not exist</span>
            <?php endif; ?>
        </div>
        
        <div class="check">
            <strong>API Directory:</strong>
            <?php if (is_dir('api')): ?>
                <span class="status success">✅ Exists</span>
            <?php else: ?>
                <span class="status error">❌ Does not exist</span>
            <?php endif; ?>
        </div>
        
        <h2>Configuration Files</h2>
        <div class="check">
            <strong>.env File:</strong>
            <?php if (file_exists('.env')): ?>
                <span class="status success">✅ Exists</span>
            <?php else: ?>
                <span class="status warning">⚠️ Does not exist (will redirect to installation)</span>
            <?php endif; ?>
        </div>
        
        <div class="check">
            <strong>Installation Lock:</strong>
            <?php if (file_exists('config/installed.lock')): ?>
                <span class="status success">✅ Installed</span>
            <?php else: ?>
                <span class="status warning">⚠️ Not installed (will redirect to installation)</span>
            <?php endif; ?>
        </div>
        
        <h2>Permissions</h2>
        <div class="check">
            <strong>Current Directory:</strong>
            <span class="status info"><?php echo getcwd(); ?></span>
        </div>
        
        <div class="check">
            <strong>File Permissions:</strong>
            <pre><?php
            $files_to_check = ['index.php', '.htaccess', 'install.php'];
            foreach ($files_to_check as $file) {
                if (file_exists($file)) {
                    echo $file . ': ' . substr(sprintf('%o', fileperms($file)), -4) . "\n";
                } else {
                    echo $file . ': NOT FOUND' . "\n";
                }
            }
            ?></pre>
        </div>
        
        <h2>Error Logs</h2>
        <div class="check">
            <strong>PHP Error Log:</strong>
            <span class="status info"><?php echo ini_get('error_log') ?: 'Not configured'; ?></span>
        </div>
        
        <div class="check">
            <strong>Display Errors:</strong>
            <?php if (ini_get('display_errors')): ?>
                <span class="status warning">⚠️ Enabled (should be disabled in production)</span>
            <?php else: ?>
                <span class="status success">✅ Disabled (good for production)</span>
            <?php endif; ?>
        </div>
        
        <h2>Server Information</h2>
        <div class="check">
            <strong>Server Software:</strong>
            <span class="status info"><?php echo $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown'; ?></span>
        </div>
        
        <div class="check">
            <strong>Document Root:</strong>
            <span class="status info"><?php echo $_SERVER['DOCUMENT_ROOT'] ?? 'Unknown'; ?></span>
        </div>
        
        <h2>Quick Fixes</h2>
        <div class="status info">
            <strong>If you see errors above:</strong>
            <ol>
                <li>Make sure PHP 7.4+ is installed with required extensions</li>
                <li>Set proper file permissions: <code>chmod 755 cache/</code></li>
                <li>Create missing directories: <code>mkdir -p cache config logs</code></li>
                <li>If .env doesn't exist, visit <a href="install.php">install.php</a> to set up</li>
                <li>Check your web server error logs for more details</li>
            </ol>
        </div>
        
        <div style="margin-top: 30px; text-align: center;">
            <a href="install.php" style="background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 4px;">Run Installation</a>
            <a href="index.php" style="background: #28a745; color: white; padding: 10px 20px; text-decoration: none; border-radius: 4px; margin-left: 10px;">Go to Site</a>
        </div>
    </div>
</body>
</html>
