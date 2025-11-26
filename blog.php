<?php
require_once __DIR__ . '/bootstrap.php';

// Check if page is enabled
$settings = new Settings();
if (!$settings->get('page_enabled_blog', 1)) {
    header('HTTP/1.0 404 Not Found');
    include __DIR__ . '/404.php';
    exit;
}

session_start();

$db = Database::getInstance();
$geo = new Geolocation();

$seo = new SEO();
$seo->setTitle('Blog - Music Industry Insights')
    ->setDescription('Read our blog for music industry insights, artist features, chart analysis, and exclusive content.')
    ->setKeywords(['blog', 'music blog', 'industry insights', 'music articles', 'features'])
    ->setType('website');

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 12;
$offset = ($page - 1) * $limit;

$blogs = $db->fetchAll(
    "SELECT * FROM blogs WHERE status = 'published' ORDER BY created_at DESC LIMIT :limit OFFSET :offset",
    ['limit' => $limit, 'offset' => $offset]
);

$totalBlogs = $db->fetchOne("SELECT COUNT(*) as count FROM blogs WHERE status = 'published'")['count'] ?? 0;
$totalPages = ceil($totalBlogs / $limit);

include __DIR__ . '/includes/header.php';
?>

    <section class="chart-header">
        <div class="container">
            <h1 class="chart-title">BLOG</h1>
            <p class="section-subtitle">Music industry insights, features, and exclusive content</p>
        </div>
    </section>

    <section class="chart-content">
        <div class="container">
            <?php if (empty($blogs)): ?>
                <div class="empty-state">
                    <h2>No blog posts available</h2>
                    <p>Blog posts will appear here once they are published.</p>
                </div>
            <?php else: ?>
                <div class="card-grid">
                    <?php foreach ($blogs as $blog): ?>
                        <div class="card-item" onclick="window.location.href='/blog/<?php echo $blog['slug']; ?>'">
                            <?php if ($blog['featured_image']): ?>
                                <div class="card-image">
                                    <img src="<?php echo htmlspecialchars($blog['featured_image']); ?>" 
                                         alt="<?php echo htmlspecialchars($blog['title']); ?>">
                                </div>
                            <?php endif; ?>
                            <div class="card-body">
                                <div class="card-meta">
                                    <span><i class="fas fa-calendar"></i> <?php echo date('M j, Y', strtotime($blog['created_at'])); ?></span>
                                </div>
                                <h3 class="card-title">
                                    <?php echo htmlspecialchars($blog['title']); ?>
                                </h3>
                                <p class="card-excerpt">
                                    <?php echo htmlspecialchars($blog['excerpt'] ?: substr(strip_tags($blog['content']), 0, 120)); ?>...
                                </p>
                                <div class="card-footer">
                                    <span><i class="fas fa-user"></i> Admin</span>
                                    <span class="card-link">Read More →</span>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <!-- Pagination -->
                <?php if ($totalPages > 1): ?>
                    <div class="pagination">
                        <?php if ($page > 1): ?>
                            <a href="/blog.php?page=<?php echo $page - 1; ?>">Previous</a>
                        <?php endif; ?>
                        
                        <?php for ($i = max(1, $page - 2); $i <= min($totalPages, $page + 2); $i++): ?>
                            <?php if ($i === $page): ?>
                                <span class="current"><?php echo $i; ?></span>
                            <?php else: ?>
                                <a href="/blog.php?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                            <?php endif; ?>
                        <?php endfor; ?>
                        
                        <?php if ($page < $totalPages): ?>
                            <a href="/blog.php?page=<?php echo $page + 1; ?>">Next</a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </section>

<?php include __DIR__ . '/includes/footer.php'; ?>

