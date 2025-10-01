<?php
// Installation Wizard for Timeline.co.zw
session_start();

// Ensure required directories exist
if (!is_dir('config')) {
    @mkdir('config', 0755, true);
}
if (!is_dir('cache')) {
    @mkdir('cache', 0755, true);
}
if (!is_dir('logs')) {
    @mkdir('logs', 0755, true);
}

// Check if already installed
if (file_exists('.env') && file_exists('config/installed.lock')) {
    header('Location: index.php');
    exit();
}

// Handle installation form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $errors = [];
    $success = false;
    
    // Validate form data
    $youtube_api_key = trim($_POST['youtube_api_key'] ?? '');
    $google_analytics_id = trim($_POST['google_analytics_id'] ?? '');
    $site_url = trim($_POST['site_url'] ?? '');
    $site_name = trim($_POST['site_name'] ?? 'Timeline.co.zw');
    $admin_email = trim($_POST['admin_email'] ?? '');
    
    // Validation
    if (empty($youtube_api_key)) {
        $errors[] = 'YouTube API Key is required';
    }
    
    if (empty($site_url)) {
        $errors[] = 'Site URL is required';
    }
    
    if (!empty($admin_email) && !filter_var($admin_email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Invalid admin email address';
    }
    
    // Test YouTube API key
    if (!empty($youtube_api_key) && empty($errors)) {
        $test_url = "https://www.googleapis.com/youtube/v3/videos?part=snippet&chart=mostPopular&maxResults=1&key=" . urlencode($youtube_api_key);
        $context = stream_context_create([
            'http' => [
                'timeout' => 10,
                'method' => 'GET'
            ]
        ]);
        
        $response = @file_get_contents($test_url, false, $context);
        if ($response === false) {
            $errors[] = 'YouTube API Key is invalid or quota exceeded';
        } else {
            $data = json_decode($response, true);
            if (isset($data['error'])) {
                $errors[] = 'YouTube API Error: ' . ($data['error']['message'] ?? 'Unknown error');
            }
        }
    }
    
    // Create .env file if no errors
    if (empty($errors)) {
        $env_content = "# Timeline.co.zw Environment Configuration
# Generated on " . date('Y-m-d H:i:s') . "

# YouTube API Configuration
YOUTUBE_API_KEY={$youtube_api_key}

# Google Analytics
GOOGLE_ANALYTICS_ID={$google_analytics_id}

# Site Configuration
SITE_URL={$site_url}
SITE_NAME={$site_name}
ADMIN_EMAIL={$admin_email}

# Security
SESSION_LIFETIME=3600
CSRF_TOKEN_LIFETIME=1800
MAX_LOGIN_ATTEMPTS=5

# Cache Configuration
CACHE_ENABLED=true
CACHE_TIMEOUT=600

# Rate Limiting
RATE_LIMIT_REQUESTS=100
RATE_LIMIT_WINDOW=3600

# Debug Mode (set to false in production)
DEBUG=false

# Logging
LOG_LEVEL=error
LOG_FILE=logs/app.log

# African Countries Priority
AFRICAN_COUNTRIES=ZW,ZA,NG,KE,GH,EG,MA,TN,DZ,LY,SD,ET,UG,TZ,RW,BI,MW,ZM,BW,SZ,LS,MZ,MG,MU,SC,KM,DJ,SO,ER,SS,CF,TD,NE,ML,BF,CI,LR,SL,GN,GW,GM,SN,MR,CV,AO,CD,CG,GA,GQ,ST,CM,TG,BJ
";
        
        if (file_put_contents('.env', $env_content)) {
            // Create installed lock file
            file_put_contents('config/installed.lock', date('Y-m-d H:i:s'));
            
            // Create cache directory if it doesn't exist
            if (!is_dir('cache')) {
                mkdir('cache', 0755, true);
            }
            
            // Create logs directory if it doesn't exist
            if (!is_dir('logs')) {
                mkdir('logs', 0755, true);
            }
            
            $success = true;
        } else {
            $errors[] = 'Failed to create configuration file. Check file permissions.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Installation - Timeline.co.zw</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .install-container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            max-width: 600px;
            width: 100%;
            overflow: hidden;
        }
        
        .install-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 40px;
            text-align: center;
        }
        
        .install-header h1 {
            font-size: 2.5rem;
            margin-bottom: 10px;
            font-weight: 700;
        }
        
        .install-header p {
            font-size: 1.1rem;
            opacity: 0.9;
        }
        
        .install-content {
            padding: 40px;
        }
        
        .form-group {
            margin-bottom: 25px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: #333;
        }
        
        .form-group input {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid #e1e5e9;
            border-radius: 8px;
            font-size: 16px;
            transition: border-color 0.3s ease;
        }
        
        .form-group input:focus {
            outline: none;
            border-color: #667eea;
        }
        
        .form-group .help-text {
            font-size: 14px;
            color: #666;
            margin-top: 5px;
        }
        
        .required {
            color: #e74c3c;
        }
        
        .btn {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            padding: 15px 30px;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 500;
            cursor: pointer;
            transition: transform 0.3s ease;
            width: 100%;
        }
        
        .btn:hover {
            transform: translateY(-2px);
        }
        
        .btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }
        
        .alert {
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        
        .alert-error {
            background: #fee;
            border: 1px solid #fcc;
            color: #c33;
        }
        
        .alert-success {
            background: #efe;
            border: 1px solid #cfc;
            color: #3c3;
        }
        
        .step-indicator {
            display: flex;
            justify-content: center;
            margin-bottom: 30px;
        }
        
        .step {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #e1e5e9;
            color: #666;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 10px;
            font-weight: 600;
        }
        
        .step.active {
            background: #667eea;
            color: white;
        }
        
        .step.completed {
            background: #27ae60;
            color: white;
        }
        
        .api-help {
            background: #f8f9fa;
            border: 1px solid #e9ecef;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
        }
        
        .api-help h3 {
            color: #495057;
            margin-bottom: 10px;
        }
        
        .api-help ol {
            color: #6c757d;
            padding-left: 20px;
        }
        
        .api-help li {
            margin-bottom: 5px;
        }
        
        .api-help a {
            color: #667eea;
            text-decoration: none;
        }
        
        .api-help a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="install-container">
        <div class="install-header">
            <h1><i class="fas fa-music"></i> Timeline.co.zw</h1>
            <p>African Music & Entertainment Hub - Installation Wizard</p>
        </div>
        
        <div class="install-content">
            <div class="step-indicator">
                <div class="step active">1</div>
                <div class="step">2</div>
                <div class="step">3</div>
            </div>
            
            <?php if (isset($success) && $success): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i>
                    <strong>Installation Successful!</strong><br>
                    Your Timeline.co.zw installation is complete. You can now access your site.
                </div>
                
                <div style="text-align: center;">
                    <a href="index.php" class="btn">
                        <i class="fas fa-rocket"></i>
                        Launch Your Site
                    </a>
                </div>
            <?php else: ?>
                <?php if (!empty($errors)): ?>
                    <div class="alert alert-error">
                        <i class="fas fa-exclamation-triangle"></i>
                        <strong>Installation Failed:</strong>
                        <ul style="margin-top: 10px; padding-left: 20px;">
                            <?php foreach ($errors as $error): ?>
                                <li><?php echo htmlspecialchars($error); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>
                
                <div class="api-help">
                    <h3><i class="fas fa-info-circle"></i> Getting Your YouTube API Key</h3>
                    <ol>
                        <li>Go to <a href="https://console.cloud.google.com/" target="_blank">Google Cloud Console</a></li>
                        <li>Create a new project or select existing one</li>
                        <li>Enable the YouTube Data API v3</li>
                        <li>Create credentials (API Key)</li>
                        <li>Restrict the API key to your domain (recommended)</li>
                    </ol>
                </div>
                
                <form method="POST" action="">
                    <div class="form-group">
                        <label for="youtube_api_key">
                            YouTube API Key <span class="required">*</span>
                        </label>
                        <input type="text" 
                               id="youtube_api_key" 
                               name="youtube_api_key" 
                               value="<?php echo htmlspecialchars($_POST['youtube_api_key'] ?? ''); ?>"
                               placeholder="AIzaSyBxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx"
                               required>
                        <div class="help-text">
                            Required for fetching trending music and videos
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="google_analytics_id">Google Analytics ID</label>
                        <input type="text" 
                               id="google_analytics_id" 
                               name="google_analytics_id" 
                               value="<?php echo htmlspecialchars($_POST['google_analytics_id'] ?? ''); ?>"
                               placeholder="GA_MEASUREMENT_ID">
                        <div class="help-text">
                            Optional: For tracking website analytics
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="site_url">
                            Site URL <span class="required">*</span>
                        </label>
                        <input type="url" 
                               id="site_url" 
                               name="site_url" 
                               value="<?php echo htmlspecialchars($_POST['site_url'] ?? 'https://timeline.co.zw'); ?>"
                               placeholder="https://timeline.co.zw"
                               required>
                        <div class="help-text">
                            Your website's full URL
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="site_name">Site Name</label>
                        <input type="text" 
                               id="site_name" 
                               name="site_name" 
                               value="<?php echo htmlspecialchars($_POST['site_name'] ?? 'Timeline.co.zw'); ?>"
                               placeholder="Timeline.co.zw">
                        <div class="help-text">
                            Display name for your website
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="admin_email">Admin Email</label>
                        <input type="email" 
                               id="admin_email" 
                               name="admin_email" 
                               value="<?php echo htmlspecialchars($_POST['admin_email'] ?? ''); ?>"
                               placeholder="admin@timeline.co.zw">
                        <div class="help-text">
                            Optional: For system notifications
                        </div>
                    </div>
                    
                    <button type="submit" class="btn">
                        <i class="fas fa-cog"></i>
                        Install Timeline.co.zw
                    </button>
                </form>
            <?php endif; ?>
        </div>
    </div>
    
    <script>
        // Auto-fill site URL if not provided
        document.addEventListener('DOMContentLoaded', function() {
            const siteUrlInput = document.getElementById('site_url');
            if (!siteUrlInput.value) {
                siteUrlInput.value = window.location.origin;
            }
        });
        
        // Form validation
        document.querySelector('form').addEventListener('submit', function(e) {
            const youtubeKey = document.getElementById('youtube_api_key').value.trim();
            const siteUrl = document.getElementById('site_url').value.trim();
            
            if (!youtubeKey) {
                alert('YouTube API Key is required');
                e.preventDefault();
                return;
            }
            
            if (!siteUrl) {
                alert('Site URL is required');
                e.preventDefault();
                return;
            }
            
            // Show loading state
            const btn = document.querySelector('.btn');
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Installing...';
            btn.disabled = true;
        });
    </script>
</body>
</html>
