<?php
require_once __DIR__ . '/bootstrap.php';

$seo = new SEO();
$seo->setTitle('Contact Us - ' . APP_NAME);
$seo->setDescription('Get in touch with ' . APP_NAME . ' - We\'d love to hear from you.');
include __DIR__ . '/includes/header.php';
?>

<main class="main-content">
    <div class="container">
        <div class="page-header">
            <h1>Contact Us</h1>
            <p>We'd love to hear from you</p>
        </div>
        
        <div class="content-section">
            <h2>Get in Touch</h2>
            <p>Have a question, suggestion, or feedback? We're here to help!</p>
            
            <div class="contact-info">
                <div class="contact-item">
                    <i class="fas fa-envelope"></i>
                    <div>
                        <strong>Email</strong>
                        <p>contact@timeline.co.zw</p>
                    </div>
                </div>
                <div class="contact-item">
                    <i class="fas fa-globe"></i>
                    <div>
                        <strong>Website</strong>
                        <p>www.timeline.co.zw</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<?php include __DIR__ . '/includes/footer.php'; ?>

