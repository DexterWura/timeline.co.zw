<?php
require_once __DIR__ . '/bootstrap.php';

$seo = new SEO();
$seo->setTitle('Privacy Policy - ' . APP_NAME);
$seo->setDescription('Privacy Policy for ' . APP_NAME);
include __DIR__ . '/includes/header.php';
?>

<main class="main-content">
    <div class="container">
        <div class="page-header">
            <h1>Privacy Policy</h1>
            <p>Last updated: <?php echo date('F d, Y'); ?></p>
        </div>
        
        <div class="content-section">
            <h2>Information We Collect</h2>
            <p>We collect information that you provide directly to us, such as when you create an account, subscribe to our newsletter, or contact us.</p>
            
            <h2>How We Use Your Information</h2>
            <p>We use the information we collect to provide, maintain, and improve our services, process transactions, and send you updates.</p>
            
            <h2>Data Security</h2>
            <p>We implement appropriate security measures to protect your personal information against unauthorized access, alteration, disclosure, or destruction.</p>
            
            <h2>Cookies</h2>
            <p>We use cookies to enhance your experience on our website. You can choose to disable cookies through your browser settings.</p>
            
            <h2>Contact Us</h2>
            <p>If you have questions about this Privacy Policy, please contact us at contact@timeline.co.zw</p>
        </div>
    </div>
</main>

<?php include __DIR__ . '/includes/footer.php'; ?>

