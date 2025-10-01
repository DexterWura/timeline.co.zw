<?php
http_response_code(500);
$page_title = 'Server Error - Timeline.co.zw';
$page_description = 'We are experiencing technical difficulties. Please try again later.';
$canonical_url = 'https://timeline.co.zw/500';
$body_class = 'error-page';

include 'includes/head.php';
?>

    <div class="error-container">
        <div class="container">
            <div class="error-content">
                <div class="error-icon">
                    <i class="fas fa-server"></i>
                </div>
                <h1 class="error-title">500 - Server Error</h1>
                <p class="error-message">
                    We are experiencing technical difficulties. Our team has been notified 
                    and is working to resolve the issue. Please try again later.
                </p>
                <div class="error-actions">
                    <a href="/" class="btn btn-primary">
                        <i class="fas fa-home"></i>
                        Go Home
                    </a>
                    <button onclick="location.reload()" class="btn btn-secondary">
                        <i class="fas fa-redo"></i>
                        Try Again
                    </button>
                </div>
                <div class="error-info">
                    <h3>What you can do:</h3>
                    <ul>
                        <li>Refresh the page in a few minutes</li>
                        <li>Check our social media for updates</li>
                        <li>Contact support if the problem persists</li>
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
        background: linear-gradient(135deg, #ff6b6b 0%, #ee5a24 100%);
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
        border: none;
        cursor: pointer;
    }
    
    .btn-primary {
        background: #2c3e50;
        color: white;
    }
    
    .btn-primary:hover {
        background: #34495e;
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
    
    .error-info {
        text-align: left;
        background: rgba(255, 255, 255, 0.1);
        padding: 1.5rem;
        border-radius: 10px;
        backdrop-filter: blur(10px);
    }
    
    .error-info h3 {
        margin-bottom: 1rem;
        text-align: center;
    }
    
    .error-info ul {
        list-style: none;
        padding: 0;
    }
    
    .error-info li {
        margin-bottom: 0.5rem;
        padding-left: 1.5rem;
        position: relative;
    }
    
    .error-info li:before {
        content: "•";
        position: absolute;
        left: 0;
        color: #ff6b6b;
    }
    </style>

<?php include 'includes/footer.php'; ?>
