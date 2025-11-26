<?php
require_once __DIR__ . '/bootstrap.php';

session_start();

$db = Database::getInstance();
$slug = $_GET['slug'] ?? '';

if (empty($slug)) {
    header('Location: /blog.php');
    exit;
}

$blog = $db->fetchOne(
    "SELECT * FROM blogs WHERE slug = :slug AND status = 'published'",
    ['slug' => $slug]
);

if (!$blog) {
    header('Location: /blog.php');
    exit;
}

$seo = new SEO();
$seo->setTitle($blog['seo_title'] ?: $blog['title'])
    ->setDescription($blog['meta_description'] ?: $blog['excerpt'] ?: substr(strip_tags($blog['content']), 0, 160))
    ->setKeywords(explode(',', $blog['meta_keywords'] ?? 'blog, music'))
    ->setType('article')
    ->setImage($blog['featured_image'] ?? '');

// Mark as viewed
if (!isset($_SESSION['viewed_blogs'])) {
    $_SESSION['viewed_blogs'] = [];
}
if (!in_array($blog['id'], $_SESSION['viewed_blogs'])) {
    $_SESSION['viewed_blogs'][] = $blog['id'];
}

include __DIR__ . '/includes/header.php';
?>

    <section class="chart-header">
        <div class="container">
            <div class="article-container">
                <h1 class="chart-title" style="font-size: 2.5rem; margin-bottom: 1rem;"><?php echo htmlspecialchars($blog['title']); ?></h1>
                <div class="article-meta">
                    <span><i class="fas fa-calendar"></i> <?php echo date('F j, Y', strtotime($blog['created_at'])); ?></span>
                    <span><i class="fas fa-user"></i> Admin</span>
                </div>
            </div>
        </div>
    </section>

    <section class="chart-content">
        <div class="container">
            <div class="article-container">
                <?php if ($blog['featured_image']): ?>
                    <div class="article-featured-image">
                        <img src="<?php echo htmlspecialchars($blog['featured_image']); ?>" alt="<?php echo htmlspecialchars($blog['title']); ?>">
                    </div>
                <?php endif; ?>
                
                <div class="article-content">
                    <?php echo nl2br(htmlspecialchars($blog['content'])); ?>
                </div>
                
                <!-- Related Blog Posts -->
                <div class="related-section">
                    <h2>Related Posts</h2>
                    <?php
                    $related = $db->fetchAll(
                        "SELECT * FROM blogs WHERE id != :id AND status = 'published' ORDER BY created_at DESC LIMIT 3",
                        ['id' => $blog['id']]
                    );
                    ?>
                    <?php if (!empty($related)): ?>
                        <div class="related-grid">
                            <?php foreach ($related as $rel): ?>
                                <div class="related-card" onclick="window.location.href='/blog/<?php echo $rel['slug']; ?>'">
                                    <?php if ($rel['featured_image']): ?>
                                        <img src="<?php echo htmlspecialchars($rel['featured_image']); ?>" alt="<?php echo htmlspecialchars($rel['title']); ?>">
                                    <?php endif; ?>
                                    <h3><?php echo htmlspecialchars($rel['title']); ?></h3>
                                    <p><?php echo htmlspecialchars($rel['excerpt'] ?: substr(strip_tags($rel['content']), 0, 100)); ?>...</p>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>

<?php include __DIR__ . '/includes/footer.php'; ?>

