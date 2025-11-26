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
            <div style="max-width: 900px; margin: 0 auto;">
                <h1 class="chart-title" style="font-size: 2.5rem; margin-bottom: 1rem;"><?php echo htmlspecialchars($blog['title']); ?></h1>
                <div style="display: flex; gap: 1rem; align-items: center; color: #666; font-size: 0.9rem; margin-bottom: 2rem;">
                    <span><i class="fas fa-calendar"></i> <?php echo date('F j, Y', strtotime($blog['created_at'])); ?></span>
                    <span><i class="fas fa-user"></i> Admin</span>
                </div>
            </div>
        </div>
    </section>

    <section class="chart-content">
        <div class="container">
            <div style="max-width: 900px; margin: 0 auto;">
                <?php if ($blog['featured_image']): ?>
                    <div style="margin-bottom: 2rem;">
                        <img src="<?php echo htmlspecialchars($blog['featured_image']); ?>" alt="<?php echo htmlspecialchars($blog['title']); ?>" style="width: 100%; border-radius: 10px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                    </div>
                <?php endif; ?>
                
                <div style="background: white; border-radius: 10px; padding: 2rem; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                    <div style="line-height: 1.8; color: #333; font-size: 1.1rem;">
                        <?php echo nl2br(htmlspecialchars($blog['content'])); ?>
                    </div>
                </div>
                
                <!-- Related Blog Posts -->
                <div style="margin-top: 3rem;">
                    <h2 style="margin-bottom: 1.5rem; color: #333;">Related Posts</h2>
                    <?php
                    $related = $db->fetchAll(
                        "SELECT * FROM blogs WHERE id != :id AND status = 'published' ORDER BY created_at DESC LIMIT 3",
                        ['id' => $blog['id']]
                    );
                    ?>
                    <?php if (!empty($related)): ?>
                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem;">
                            <?php foreach ($related as $rel): ?>
                                <div style="background: white; border-radius: 10px; padding: 1.5rem; box-shadow: 0 2px 4px rgba(0,0,0,0.1); cursor: pointer; transition: transform 0.2s;" onclick="window.location.href='/blog/<?php echo $rel['slug']; ?>'">
                                    <?php if ($rel['featured_image']): ?>
                                        <img src="<?php echo htmlspecialchars($rel['featured_image']); ?>" alt="<?php echo htmlspecialchars($rel['title']); ?>" style="width: 100%; height: 150px; object-fit: cover; border-radius: 8px; margin-bottom: 1rem;">
                                    <?php endif; ?>
                                    <h3 style="font-size: 1.1rem; margin-bottom: 0.5rem; color: #333;"><?php echo htmlspecialchars($rel['title']); ?></h3>
                                    <p style="color: #666; font-size: 0.9rem;"><?php echo htmlspecialchars($rel['excerpt'] ?: substr(strip_tags($rel['content']), 0, 100)); ?>...</p>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>

<?php include __DIR__ . '/includes/footer.php'; ?>

