<?php
require_once __DIR__ . '/bootstrap.php';

$seo = new SEO();
$seo->setTitle('Terms and Conditions - ' . APP_NAME);
$seo->setDescription('Terms and Conditions for ' . APP_NAME);
include __DIR__ . '/includes/header.php';
?>

<main class="main-content">
    <div class="container">
        <div class="page-header">
            <h1>Terms and Conditions</h1>
            <p>Last updated: <?php echo date('F d, Y'); ?></p>
        </div>
        
        <div class="content-section">
            <h2>Acceptance of Terms</h2>
            <p>By accessing and using <?php echo APP_NAME; ?>, you accept and agree to be bound by the terms and provision of this agreement.</p>
            
            <h2>Use License</h2>
            <p>Permission is granted to temporarily access the materials on <?php echo APP_NAME; ?> for personal, non-commercial transitory viewing only.</p>
            
            <h2>User Accounts</h2>
            <p>You are responsible for maintaining the confidentiality of your account and password. You agree to accept responsibility for all activities that occur under your account.</p>
            
            <h2>Content</h2>
            <p>All content on this website is for informational purposes only. We strive to provide accurate information but make no warranties about the completeness or accuracy of the information.</p>
            
            <h2>Contact Us</h2>
            <p>If you have questions about these Terms, please contact us at contact@timeline.co.zw</p>
        </div>
    </div>
</main>

<?php include __DIR__ . '/includes/footer.php'; ?>

