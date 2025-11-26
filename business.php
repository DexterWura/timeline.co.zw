<?php
require_once __DIR__ . '/bootstrap.php';

// Check if page is enabled
$settings = new Settings();
if (!$settings->get('page_enabled_business', 1)) {
    header('HTTP/1.0 404 Not Found');
    include __DIR__ . '/404.php';
    exit;
}

$seo = new SEO();
$seo->setTitle('Music Industry Business Charts')
    ->setDescription('Music industry business insights, revenue charts, market share data, and industry trends. Track the business side of the music industry.')
    ->setKeywords(['music business', 'music industry', 'revenue charts', 'market share', 'music industry trends'])
    ->setType('website');

include __DIR__ . '/includes/header.php';
?>

    <section class="chart-header">
        <div class="container">
            <h1 class="chart-title">BUSINESS CHARTS</h1>
            <p class="section-subtitle">Music industry business insights</p>
        </div>
    </section>

    <section class="chart-content">
        <div class="container">
            <div class="empty-state">
                <h2>Coming Soon</h2>
                <p>Business charts and analytics will be available soon.</p>
            </div>
        </div>
    </section>

<?php include __DIR__ . '/includes/footer.php'; ?>

