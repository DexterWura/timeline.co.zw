<?php
require_once __DIR__ . '/bootstrap.php';

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
            <p style="text-align: center; color: #666; margin-top: 1rem;">Music industry insights, features, and exclusive content</p>
        </div>
    </section>

    <section class="chart-content">
        <div class="container">
            <?php if (empty($blogs)): ?>
                <div style="text-align: center; padding: 4rem;">
                    <h2>No blog posts available</h2>
                    <p>Blog posts will appear here once they are published.</p>
                </div>
            <?php else: ?>
                <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 2rem; margin-top: 2rem;">
                    <?php foreach ($blogs as $blog): ?>
                        <div class="info-card" style="padding: 0; background: rgba(255, 255, 255, 0.9); border-radius: 10px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); overflow: hidden; cursor: pointer; transition: transform 0.2s;" 
                             onclick="window.location.href='/blog/<?php echo $blog['slug']; ?>'"
                             onmouseover="this.style.transform='translateY(-5px)'" 
                             onmouseout="this.style.transform='translateY(0)'">
                            <?php if ($blog['featured_image']): ?>
                                <div style="width: 100%; height: 200px; overflow: hidden;">
                                    <img src="<?php echo htmlspecialchars($blog['featured_image']); ?>" 
                                         alt="<?php echo htmlspecialchars($blog['title']); ?>" 
                                         style="width: 100%; height: 100%; object-fit: cover;">
                                </div>
                            <?php endif; ?>
                            <div style="padding: 1.5rem;">
                                <div style="display: flex; gap: 0.5rem; margin-bottom: 0.5rem; font-size: 0.85rem; color: #666;">
                                    <span><i class="fas fa-calendar"></i> <?php echo date('M j, Y', strtotime($blog['created_at'])); ?></span>
                                </div>
                                <h3 style="font-size: 1.2rem; margin-bottom: 0.5rem; color: #333; line-height: 1.4;">
                                    <?php echo htmlspecialchars($blog['title']); ?>
                                </h3>
                                <p style="color: #666; font-size: 0.9rem; line-height: 1.6; margin-bottom: 1rem;">
                                    <?php echo htmlspecialchars($blog['excerpt'] ?: substr(strip_tags($blog['content']), 0, 120)); ?>...
                                </p>
                                <div style="display: flex; justify-content: space-between; align-items: center; font-size: 0.85rem; color: #999;">
                                    <span><i class="fas fa-user"></i> Admin</span>
                                    <span style="color: #00d4aa; font-weight: 600;">Read More →</span>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <!-- Pagination -->
                <?php if ($totalPages > 1): ?>
                    <div style="display: flex; justify-content: center; gap: 0.5rem; margin-top: 3rem;">
                        <?php if ($page > 1): ?>
                            <a href="/blog.php?page=<?php echo $page - 1; ?>" 
                               style="padding: 0.75rem 1.5rem; background: #00d4aa; color: white; border-radius: 5px; text-decoration: none;">
                                Previous
                            </a>
                        <?php endif; ?>
                        
                        <?php for ($i = max(1, $page - 2); $i <= min($totalPages, $page + 2); $i++): ?>
                            <a href="/blog.php?page=<?php echo $i; ?>" 
                               style="padding: 0.75rem 1.5rem; background: <?php echo $i === $page ? '#00d4aa' : '#f0f0f0'; ?>; color: <?php echo $i === $page ? 'white' : '#333'; ?>; border-radius: 5px; text-decoration: none;">
                                <?php echo $i; ?>
                            </a>
                        <?php endfor; ?>
                        
                        <?php if ($page < $totalPages): ?>
                            <a href="/blog.php?page=<?php echo $page + 1; ?>" 
                               style="padding: 0.75rem 1.5rem; background: #00d4aa; color: white; border-radius: 5px; text-decoration: none;">
                                Next
                            </a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </section>

<?php include __DIR__ . '/includes/footer.php'; ?>

