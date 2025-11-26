<?php
require_once __DIR__ . '/bootstrap.php';

session_start();

$db = Database::getInstance();
$geo = new Geolocation();

$seo = new SEO();
$seo->setTitle('News & Articles')
    ->setDescription('Stay updated with the latest music news, industry updates, awards, and entertainment articles.')
    ->setKeywords(['news', 'articles', 'music news', 'entertainment', 'industry updates'])
    ->setType('website');

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 12;
$offset = ($page - 1) * $limit;
$category = $_GET['category'] ?? '';

$sql = "SELECT * FROM news_articles WHERE 1=1";
$params = [];

if ($category) {
    $category = htmlspecialchars(trim($category), ENT_QUOTES, 'UTF-8');
    $sql .= " AND category = :category";
    $params['category'] = $category;
}

$sql .= " ORDER BY published_at DESC, created_at DESC LIMIT :limit OFFSET :offset";

$params['limit'] = $limit;
$params['offset'] = $offset;

$articles = $db->fetchAll($sql, $params);

$totalArticles = $db->fetchOne(
    "SELECT COUNT(*) as count FROM news_articles" . ($category ? " WHERE category = :category" : ""),
    $category ? ['category' => $category] : []
)['count'] ?? 0;

$totalPages = ceil($totalArticles / $limit);

$categories = $db->fetchAll("SELECT DISTINCT category FROM news_articles WHERE category IS NOT NULL AND category != ''");

include __DIR__ . '/includes/header.php';
?>

    <section class="chart-header">
        <div class="container">
            <h1 class="chart-title">NEWS & ARTICLES</h1>
            <p style="text-align: center; color: #666; margin-top: 1rem;">Stay updated with the latest music industry news</p>
            <?php if (count($categories) > 0): ?>
                <div style="margin-top: 1.5rem; display: flex; gap: 0.5rem; justify-content: center; flex-wrap: wrap;">
                    <a href="/news.php" style="padding: 0.5rem 1rem; background: <?php echo $category ? '#f0f0f0' : '#00d4aa'; ?>; color: <?php echo $category ? '#333' : 'white'; ?>; border-radius: 20px; text-decoration: none; font-size: 0.9rem;">
                        All
                    </a>
                    <?php foreach ($categories as $cat): ?>
                        <a href="/news.php?category=<?php echo urlencode($cat['category']); ?>" 
                           style="padding: 0.5rem 1rem; background: <?php echo $category === $cat['category'] ? '#00d4aa' : '#f0f0f0'; ?>; color: <?php echo $category === $cat['category'] ? 'white' : '#333'; ?>; border-radius: 20px; text-decoration: none; font-size: 0.9rem;">
                            <?php echo htmlspecialchars($cat['category']); ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <section class="chart-content">
        <div class="container">
            <?php if (empty($articles)): ?>
                <div style="text-align: center; padding: 4rem;">
                    <h2>No articles available</h2>
                    <p>Articles will appear here once they are published.</p>
                </div>
            <?php else: ?>
                <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 2rem; margin-top: 2rem;">
                    <?php foreach ($articles as $article): ?>
                        <div class="info-card" style="padding: 0; background: rgba(255, 255, 255, 0.9); border-radius: 10px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); overflow: hidden; cursor: pointer; transition: transform 0.2s;" 
                             onclick="window.location.href='/article.php?slug=<?php echo $article['slug']; ?>'"
                             onmouseover="this.style.transform='translateY(-5px)'" 
                             onmouseout="this.style.transform='translateY(0)'">
                            <?php if ($article['image_url']): ?>
                                <div style="width: 100%; height: 200px; overflow: hidden;">
                                    <img src="<?php echo htmlspecialchars($article['image_url']); ?>" 
                                         alt="<?php echo htmlspecialchars($article['title']); ?>" 
                                         style="width: 100%; height: 100%; object-fit: cover;">
                                </div>
                            <?php endif; ?>
                            <div style="padding: 1.5rem;">
                                <div style="display: flex; gap: 0.5rem; margin-bottom: 0.5rem; font-size: 0.85rem; color: #666;">
                                    <?php if ($article['source']): ?>
                                        <span><i class="fas fa-newspaper"></i> <?php echo htmlspecialchars($article['source']); ?></span>
                                    <?php endif; ?>
                                    <?php if ($article['category']): ?>
                                        <span><i class="fas fa-tag"></i> <?php echo htmlspecialchars($article['category']); ?></span>
                                    <?php endif; ?>
                                </div>
                                <h3 style="font-size: 1.2rem; margin-bottom: 0.5rem; color: #333; line-height: 1.4;">
                                    <?php echo htmlspecialchars($article['title']); ?>
                                </h3>
                                <p style="color: #666; font-size: 0.9rem; line-height: 1.6; margin-bottom: 1rem;">
                                    <?php echo htmlspecialchars($article['excerpt'] ?: substr(strip_tags($article['content']), 0, 120)); ?>...
                                </p>
                                <div style="display: flex; justify-content: space-between; align-items: center; font-size: 0.85rem; color: #999;">
                                    <span><i class="fas fa-calendar"></i> <?php echo date('M j, Y', strtotime($article['published_at'] ?: $article['created_at'])); ?></span>
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
                            <a href="/news.php?page=<?php echo $page - 1; ?><?php echo $category ? '&category=' . urlencode($category) : ''; ?>" 
                               style="padding: 0.75rem 1.5rem; background: #00d4aa; color: white; border-radius: 5px; text-decoration: none;">
                                Previous
                            </a>
                        <?php endif; ?>
                        
                        <?php for ($i = max(1, $page - 2); $i <= min($totalPages, $page + 2); $i++): ?>
                            <a href="/news.php?page=<?php echo $i; ?><?php echo $category ? '&category=' . urlencode($category) : ''; ?>" 
                               style="padding: 0.75rem 1.5rem; background: <?php echo $i === $page ? '#00d4aa' : '#f0f0f0'; ?>; color: <?php echo $i === $page ? 'white' : '#333'; ?>; border-radius: 5px; text-decoration: none;">
                                <?php echo $i; ?>
                            </a>
                        <?php endfor; ?>
                        
                        <?php if ($page < $totalPages): ?>
                            <a href="/news.php?page=<?php echo $page + 1; ?><?php echo $category ? '&category=' . urlencode($category) : ''; ?>" 
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

