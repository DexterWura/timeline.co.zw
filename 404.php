<?php
http_response_code(404);
$page_title = 'Page Not Found - Timeline.co.zw';
$page_description = 'The page you are looking for could not be found.';
$canonical_url = 'https://timeline.co.zw/404';
$body_class = 'error-page';

include 'includes/head.php';
?>

    <div class="error-container">
        <div class="container">
            <div class="error-content">
                <div class="error-icon">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <h1 class="error-title">404 - Page Not Found</h1>
                <p class="error-message">
                    Sorry, the page you are looking for could not be found. 
                    It may have been moved, deleted, or the URL might be incorrect.
                </p>
                <div class="error-actions">
                    <a href="/" class="btn btn-primary">
                        <i class="fas fa-home"></i>
                        Go Home
                    </a>
                    <a href="javascript:history.back()" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i>
                        Go Back
                    </a>
                </div>
                <div class="error-suggestions">
                    <h3>Popular Pages:</h3>
                    <ul>
                        <li><a href="/charts.php">Music Charts</a></li>
                        <li><a href="/videos.php">Top Videos</a></li>
                        <li><a href="/richest.php">Richest People</a></li>
                        <li><a href="/awards.php">Music Awards</a></li>
                        <li><a href="/business.php">Business Charts</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <style>
    .error-container {
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }
    
    .error-content {
        text-align: center;
        max-width: 600px;
        padding: 2rem;
    }
    
    .error-icon {
        font-size: 4rem;
        margin-bottom: 1rem;
        opacity: 0.8;
    }
    
    .error-title {
        font-size: 3rem;
        margin-bottom: 1rem;
        font-weight: 700;
    }
    
    .error-message {
        font-size: 1.2rem;
        margin-bottom: 2rem;
        opacity: 0.9;
    }
    
    .error-actions {
        margin-bottom: 2rem;
    }
    
    .btn {
        display: inline-block;
        padding: 12px 24px;
        margin: 0 10px;
        text-decoration: none;
        border-radius: 5px;
        font-weight: 500;
        transition: all 0.3s ease;
    }
    
    .btn-primary {
        background: #ff6b6b;
        color: white;
    }
    
    .btn-primary:hover {
        background: #ff5252;
        transform: translateY(-2px);
    }
    
    .btn-secondary {
        background: rgba(255, 255, 255, 0.2);
        color: white;
        border: 1px solid rgba(255, 255, 255, 0.3);
    }
    
    .btn-secondary:hover {
        background: rgba(255, 255, 255, 0.3);
        transform: translateY(-2px);
    }
    
    .error-suggestions {
        text-align: left;
        background: rgba(255, 255, 255, 0.1);
        padding: 1.5rem;
        border-radius: 10px;
        backdrop-filter: blur(10px);
    }
    
    .error-suggestions h3 {
        margin-bottom: 1rem;
        text-align: center;
    }
    
    .error-suggestions ul {
        list-style: none;
        padding: 0;
    }
    
    .error-suggestions li {
        margin-bottom: 0.5rem;
    }
    
    .error-suggestions a {
        color: white;
        text-decoration: none;
        opacity: 0.9;
        transition: opacity 0.3s ease;
    }
    
    .error-suggestions a:hover {
        opacity: 1;
        text-decoration: underline;
    }
    </style>

<?php include 'includes/footer.php'; ?>
