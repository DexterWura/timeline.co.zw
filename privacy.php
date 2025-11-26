<?php
require_once __DIR__ . '/bootstrap.php';

$pageContent = new PageContent();
$page = $pageContent->getPage('privacy');

if (!$page) {
    header('HTTP/1.0 404 Not Found');
    include __DIR__ . '/404.php';
    exit;
}

$seo = new SEO();
$seo->setTitle($page['page_title'] . ' - ' . APP_NAME);
if ($page['meta_description']) {
    $seo->setDescription($page['meta_description']);
}
if ($page['meta_keywords']) {
    $seo->setKeywords(explode(',', $page['meta_keywords']));
}
include __DIR__ . '/includes/header.php';
?>

<main class="main-content">
    <div class="container">
        <div class="page-header">
            <h1><?php echo htmlspecialchars($page['page_title']); ?></h1>
            <p>Last updated: <?php echo $page['updated_at'] ? date('F d, Y', strtotime($page['updated_at'])) : date('F d, Y'); ?></p>
        </div>
        
        <div class="content-section">
            <?php echo $page['content']; ?>
        </div>
    </div>
</main>

<?php include __DIR__ . '/includes/footer.php'; ?>

