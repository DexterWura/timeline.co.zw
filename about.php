<?php
require_once __DIR__ . '/bootstrap.php';

$seo = new SEO();
$seo->setTitle('About Us - ' . APP_NAME);
$seo->setDescription('Learn more about ' . APP_NAME . ' - Your source for music charts, entertainment news, and industry insights.');
include __DIR__ . '/includes/header.php';
?>

<main class="main-content">
    <div class="container">
        <div class="page-header">
            <h1>About Us</h1>
            <p>Your trusted source for music charts and entertainment</p>
        </div>
        
        <div class="content-section">
            <h2>Who We Are</h2>
            <p><?php echo APP_NAME; ?> is a leading platform for music charts, entertainment news, and industry insights. We provide real-time data on the hottest songs, trending videos, and the latest news from the music industry.</p>
            
            <h2>Our Mission</h2>
            <p>To deliver accurate, up-to-date music chart data and entertainment content to music lovers, industry professionals, and fans worldwide.</p>
            
            <h2>What We Offer</h2>
            <ul>
                <li>Real-time music charts (Hot 100, Global 200, Artist 100)</li>
                <li>Top trending music videos</li>
                <li>Entertainment news and articles</li>
                <li>Music awards and industry recognition</li>
                <li>Business insights and analytics</li>
            </ul>
        </div>
    </div>
</main>

<?php include __DIR__ . '/includes/footer.php'; ?>

