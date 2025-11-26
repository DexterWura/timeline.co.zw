<?php
require_once __DIR__ . '/bootstrap.php';

// Check if page is enabled
$settings = new Settings();
if (!$settings->get('page_enabled_news', 1)) {
    header('HTTP/1.0 404 Not Found');
    include __DIR__ . '/404.php';
    exit;
}

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
            <p class="section-subtitle">Stay updated with the latest music industry news</p>
            <?php if (count($categories) > 0): ?>
                <div class="category-filters">
                    <a href="/news.php" class="category-filter <?php echo !$category ? 'active' : ''; ?>">All</a>
                    <?php foreach ($categories as $cat): ?>
                        <a href="/news.php?category=<?php echo urlencode($cat['category']); ?>" 
                           class="category-filter <?php echo $category === $cat['category'] ? 'active' : ''; ?>">
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
                <div class="empty-state">
                    <h2>No articles available</h2>
                    <p>Articles will appear here once they are published.</p>
                </div>
            <?php else: ?>
                <div class="card-grid">
                    <?php foreach ($articles as $article): ?>
                        <div class="card-item" onclick="window.location.href='/article.php?slug=<?php echo $article['slug']; ?>'">
                            <?php if ($article['image_url']): ?>
                                <div class="card-image">
                                    <img src="<?php echo htmlspecialchars($article['image_url']); ?>" 
                                         alt="<?php echo htmlspecialchars($article['title']); ?>">
                                </div>
                            <?php endif; ?>
                            <div class="card-body">
                                <div class="card-meta">
                                    <?php if ($article['source']): ?>
                                        <span><i class="fas fa-newspaper"></i> <?php echo htmlspecialchars($article['source']); ?></span>
                                    <?php endif; ?>
                                    <?php if ($article['category']): ?>
                                        <span><i class="fas fa-tag"></i> <?php echo htmlspecialchars($article['category']); ?></span>
                                    <?php endif; ?>
                                </div>
                                <h3 class="card-title">
                                    <?php echo htmlspecialchars($article['title']); ?>
                                </h3>
                                <p class="card-excerpt">
                                    <?php echo htmlspecialchars($article['excerpt'] ?: substr(strip_tags($article['content']), 0, 120)); ?>...
                                </p>
                                <div class="card-footer">
                                    <span><i class="fas fa-calendar"></i> <?php echo date('M j, Y', strtotime($article['published_at'] ?: $article['created_at'])); ?></span>
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
                            <a href="/news.php?page=<?php echo $page - 1; ?><?php echo $category ? '&category=' . urlencode($category) : ''; ?>">Previous</a>
                        <?php endif; ?>
                        
                        <?php for ($i = max(1, $page - 2); $i <= min($totalPages, $page + 2); $i++): ?>
                            <?php if ($i === $page): ?>
                                <span class="current"><?php echo $i; ?></span>
                            <?php else: ?>
                                <a href="/news.php?page=<?php echo $i; ?><?php echo $category ? '&category=' . urlencode($category) : ''; ?>"><?php echo $i; ?></a>
                            <?php endif; ?>
                        <?php endfor; ?>
                        
                        <?php if ($page < $totalPages): ?>
                            <a href="/news.php?page=<?php echo $page + 1; ?><?php echo $category ? '&category=' . urlencode($category) : ''; ?>">Next</a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </section>

<?php include __DIR__ . '/includes/footer.php'; ?>

