<?php
require_once __DIR__ . '/bootstrap.php';

$pageContent = new PageContent();
$page = $pageContent->getPage('about');

// Fallback to default content if page doesn't exist
if (!$page) {
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
                <p><?php echo APP_NAME; ?> is a leading platform for music charts, entertainment news, and industry insights.</p>
            </div>
        </div>
    </main>
    <?php include __DIR__ . '/includes/footer.php';
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
        </div>
        
        <div class="content-section">
            <?php echo $page['content']; ?>
        </div>
    </div>
</main>

<?php include __DIR__ . '/includes/footer.php'; ?>

