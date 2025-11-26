<?php
require_once __DIR__ . '/bootstrap.php';

$seo = new SEO();
$seo->setTitle('Careers - ' . APP_NAME);
$seo->setDescription('Join the ' . APP_NAME . ' team');
include __DIR__ . '/includes/header.php';
?>

<main class="main-content">
    <div class="container">
        <div class="page-header">
            <h1>Careers</h1>
            <p>Join our team</p>
        </div>
        
        <div class="content-section">
            <h2>Work With Us</h2>
            <p>We're always looking for talented individuals to join our team. If you're passionate about music, technology, and creating great content, we'd love to hear from you.</p>
            
            <h2>Open Positions</h2>
            <p>Currently, we don't have any open positions, but we're always accepting applications for future opportunities.</p>
            
            <h2>How to Apply</h2>
            <p>Send your resume and cover letter to careers@timeline.co.zw</p>
        </div>
    </div>
</main>

<?php include __DIR__ . '/includes/footer.php'; ?>

