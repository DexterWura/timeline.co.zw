<?php
require_once __DIR__ . '/bootstrap.php';

session_start();

$db = Database::getInstance();
$slug = $_GET['slug'] ?? '';

if (empty($slug)) {
    header('Location: /news.php');
    exit;
}

$article = $db->fetchOne(
    "SELECT * FROM news_articles WHERE slug = :slug",
    ['slug' => $slug]
);

if (!$article) {
    header('Location: /news.php');
    exit;
}

$seo = new SEO();
$seo->setTitle($article['title'])
    ->setDescription($article['excerpt'] ?: substr(strip_tags($article['content']), 0, 160))
    ->setKeywords(explode(',', $article['category'] ?? 'news, article'))
    ->setType('article')
    ->setImage($article['image_url'] ?? '');

// Mark as viewed (for analytics)
if (!isset($_SESSION['viewed_articles'])) {
    $_SESSION['viewed_articles'] = [];
}
if (!in_array($article['id'], $_SESSION['viewed_articles'])) {
    $_SESSION['viewed_articles'][] = $article['id'];
}

include __DIR__ . '/includes/header.php';
?>

    <section class="chart-header">
        <div class="container">
            <div class="article-container">
                <h1 class="chart-title" style="font-size: 2.5rem; margin-bottom: 1rem;"><?php echo htmlspecialchars($article['title']); ?></h1>
                <div class="article-meta">
                    <span><i class="fas fa-calendar"></i> <?php echo date('F j, Y', strtotime($article['published_at'] ?: $article['created_at'])); ?></span>
                    <?php if ($article['source']): ?>
                        <span><i class="fas fa-newspaper"></i> <?php echo htmlspecialchars($article['source']); ?></span>
                    <?php endif; ?>
                    <?php if ($article['category']): ?>
                        <span><i class="fas fa-tag"></i> <?php echo htmlspecialchars($article['category']); ?></span>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>

    <section class="chart-content">
        <div class="container">
            <div class="article-container">
                <?php if ($article['image_url']): ?>
                    <div class="article-featured-image">
                        <img src="<?php echo htmlspecialchars($article['image_url']); ?>" alt="<?php echo htmlspecialchars($article['title']); ?>">
                    </div>
                <?php endif; ?>
                
                <?php if ($article['is_from_api'] && $article['source_url']): ?>
                    <!-- External article - load in iframe to keep user on site -->
                    <div class="iframe-container">
                        <div class="iframe-info">
                            <p>
                                <i class="fas fa-info-circle"></i> 
                                This article is from <strong><?php echo htmlspecialchars($article['source']); ?></strong> and is displayed here for your convenience.
                            </p>
                        </div>
                        <div id="articleContent" style="min-height: 600px; position: relative;">
                            <iframe 
                                id="articleFrame"
                                class="article-frame"
                                src="<?php echo htmlspecialchars($article['source_url']); ?>" 
                                allow="fullscreen"
                                loading="lazy">
                            </iframe>
                            <div id="iframeLoading" class="iframe-loading">
                                <i class="fas fa-spinner fa-spin" style="font-size: 2rem; margin-bottom: 1rem;"></i>
                                <p>Loading article...</p>
                            </div>
                        </div>
                        <div class="iframe-footer">
                            <p>
                                <i class="fas fa-shield-alt"></i> 
                                You're viewing this article on <?php echo APP_NAME; ?>. 
                                <a href="<?php echo htmlspecialchars($article['source_url']); ?>" target="_blank">
                                    View original article <i class="fas fa-external-link-alt"></i>
                                </a>
                            </p>
                        </div>
                    </div>
                <?php else: ?>
                    <!-- Admin-written article -->
                    <div class="article-content">
                        <?php echo nl2br(htmlspecialchars($article['content'])); ?>
                    </div>
                <?php endif; ?>
                
                <!-- Related Articles -->
                <div class="related-section">
                    <h2>Related Articles</h2>
                    <?php
                    $related = $db->fetchAll(
                        "SELECT * FROM news_articles WHERE id != :id AND (category = :category OR source = :source) ORDER BY published_at DESC LIMIT 3",
                        ['id' => $article['id'], 'category' => $article['category'] ?? '', 'source' => $article['source'] ?? '']
                    );
                    ?>
                    <div class="related-grid">
                        <?php foreach ($related as $rel): ?>
                            <div class="related-card" onclick="window.location.href='/article.php?slug=<?php echo $rel['slug']; ?>'">
                                <?php if ($rel['image_url']): ?>
                                    <img src="<?php echo htmlspecialchars($rel['image_url']); ?>" alt="<?php echo htmlspecialchars($rel['title']); ?>">
                                <?php endif; ?>
                                <h3><?php echo htmlspecialchars($rel['title']); ?></h3>
                                <p><?php echo htmlspecialchars($rel['excerpt'] ?: substr(strip_tags($rel['content']), 0, 100)); ?>...</p>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const iframe = document.getElementById('articleFrame');
    const loading = document.getElementById('iframeLoading');
    
    if (iframe) {
        iframe.onload = function() {
            if (loading) {
                loading.style.display = 'none';
            }
            // Try to adjust height (may be blocked by CORS)
            try {
                const iframeDoc = iframe.contentDocument || iframe.contentWindow.document;
                iframe.style.height = iframeDoc.body.scrollHeight + 'px';
            } catch (e) {
                // CORS blocked - keep default height
                console.log('Cannot adjust iframe height due to CORS restrictions');
            }
        };
        
        // Hide loading after timeout
        setTimeout(function() {
            if (loading) {
                loading.style.display = 'none';
            }
        }, 5000);
    }
});
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>

