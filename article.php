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
            <div style="max-width: 900px; margin: 0 auto;">
                <h1 class="chart-title" style="font-size: 2.5rem; margin-bottom: 1rem;"><?php echo htmlspecialchars($article['title']); ?></h1>
                <div style="display: flex; gap: 1rem; align-items: center; color: #666; font-size: 0.9rem; margin-bottom: 2rem;">
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
            <div style="max-width: 900px; margin: 0 auto;">
                <?php if ($article['image_url']): ?>
                    <div style="margin-bottom: 2rem;">
                        <img src="<?php echo htmlspecialchars($article['image_url']); ?>" alt="<?php echo htmlspecialchars($article['title']); ?>" style="width: 100%; border-radius: 10px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                    </div>
                <?php endif; ?>
                
                <?php if ($article['is_from_api'] && $article['source_url']): ?>
                    <!-- External article - load in iframe to keep user on site -->
                    <div style="background: white; border-radius: 10px; padding: 2rem; box-shadow: 0 4px 6px rgba(0,0,0,0.1); margin-bottom: 2rem;">
                        <div style="margin-bottom: 1rem; padding: 1rem; background: rgba(0, 212, 170, 0.1); border-radius: 8px; border-left: 4px solid #00d4aa;">
                            <p style="color: #666; font-size: 0.9rem; margin: 0;">
                                <i class="fas fa-info-circle"></i> 
                                This article is from <strong><?php echo htmlspecialchars($article['source']); ?></strong> and is displayed here for your convenience.
                            </p>
                        </div>
                        <div id="articleContent" style="min-height: 600px; position: relative;">
                            <iframe 
                                id="articleFrame"
                                src="<?php echo htmlspecialchars($article['source_url']); ?>" 
                                style="width: 100%; height: 800px; border: 2px solid #e0e0e0; border-radius: 8px; background: #f9f9f9;"
                                allow="fullscreen"
                                loading="lazy">
                            </iframe>
                            <div id="iframeLoading" style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); text-align: center; color: #666;">
                                <i class="fas fa-spinner fa-spin" style="font-size: 2rem; margin-bottom: 1rem;"></i>
                                <p>Loading article...</p>
                            </div>
                        </div>
                        <div style="margin-top: 1rem; padding: 1rem; background: #f9f9f9; border-radius: 8px; text-align: center;">
                            <p style="color: #666; font-size: 0.85rem; margin: 0;">
                                <i class="fas fa-shield-alt"></i> 
                                You're viewing this article on <?php echo APP_NAME; ?>. 
                                <a href="<?php echo htmlspecialchars($article['source_url']); ?>" target="_blank" style="color: #00d4aa; text-decoration: none;">
                                    View original article <i class="fas fa-external-link-alt"></i>
                                </a>
                            </p>
                        </div>
                    </div>
                <?php else: ?>
                    <!-- Admin-written article -->
                    <div style="background: white; border-radius: 10px; padding: 2rem; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                        <div style="line-height: 1.8; color: #333; font-size: 1.1rem;">
                            <?php echo nl2br(htmlspecialchars($article['content'])); ?>
                        </div>
                    </div>
                <?php endif; ?>
                
                <!-- Related Articles -->
                <div style="margin-top: 3rem;">
                    <h2 style="margin-bottom: 1.5rem; color: #333;">Related Articles</h2>
                    <?php
                    $related = $db->fetchAll(
                        "SELECT * FROM news_articles WHERE id != :id AND (category = :category OR source = :source) ORDER BY published_at DESC LIMIT 3",
                        ['id' => $article['id'], 'category' => $article['category'] ?? '', 'source' => $article['source'] ?? '']
                    );
                    ?>
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem;">
                        <?php foreach ($related as $rel): ?>
                            <div style="background: white; border-radius: 10px; padding: 1.5rem; box-shadow: 0 2px 4px rgba(0,0,0,0.1); cursor: pointer; transition: transform 0.2s;" onclick="window.location.href='/article.php?slug=<?php echo $rel['slug']; ?>'">
                                <?php if ($rel['image_url']): ?>
                                    <img src="<?php echo htmlspecialchars($rel['image_url']); ?>" alt="<?php echo htmlspecialchars($rel['title']); ?>" style="width: 100%; height: 150px; object-fit: cover; border-radius: 8px; margin-bottom: 1rem;">
                                <?php endif; ?>
                                <h3 style="font-size: 1.1rem; margin-bottom: 0.5rem; color: #333;"><?php echo htmlspecialchars($rel['title']); ?></h3>
                                <p style="color: #666; font-size: 0.9rem;"><?php echo htmlspecialchars($rel['excerpt'] ?: substr(strip_tags($rel['content']), 0, 100)); ?>...</p>
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

