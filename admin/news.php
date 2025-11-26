<?php
require_once __DIR__ . '/../bootstrap.php';

$pageTitle = 'News Management';
$db = Database::getInstance();
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'fetch_news') {
    try {
        $newsService = new NewsService();
        $newsService->fetchData();
        $success = 'News articles fetched successfully!';
    } catch (Exception $e) {
        $error = 'Error fetching news: ' . $e->getMessage();
    }
}

$news = $db->fetchAll("SELECT * FROM news_articles ORDER BY published_at DESC LIMIT 50");
$totalNews = count($news);

include __DIR__ . '/includes/header.php';
?>

    <main class="main-content">
        <header class="top-bar">
            <div class="top-bar-left">
                <button class="menu-toggle" id="menuToggle">
                    <i class="fa-solid fa-bars"></i>
                </button>
                <h2 class="page-title">News</h2>
            </div>
            <div class="top-bar-right">
                <form method="POST" style="display: inline;">
                    <input type="hidden" name="action" value="fetch_news">
                    <button type="submit" class="notification-btn btn-primary">
                        <i class="fa-solid fa-sync"></i> Fetch News
                    </button>
                </form>
                <div class="user-profile">
                    <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($currentUser['email']); ?>&background=random" alt="User" class="profile-img">
                </div>
            </div>
        </header>

        <nav class="breadcrumbs">
            <a href="/admin/dashboard.php">
                <i class="fa-solid fa-house"></i>
                <span>Home</span>
            </a>
            <span class="breadcrumb-separator">
                <i class="fa-solid fa-chevron-right"></i>
            </span>
            <span class="breadcrumb-current">News</span>
        </nav>

        <div class="dashboard-content">
            <?php if ($error): ?>
                <div style="background: rgba(255, 0, 0, 0.1); color: #c33; padding: 1rem; border-radius: 8px; margin-bottom: 2rem;">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div style="background: rgba(0, 255, 0, 0.1); color: #0a5; padding: 1rem; border-radius: 8px; margin-bottom: 2rem;">
                    <?php echo htmlspecialchars($success); ?>
                </div>
            <?php endif; ?>

            <section class="stats-section">
                <div class="stat-card">
                    <div class="stat-card-header">
                        <h3>Total News</h3>
                        <i class="fa-solid fa-newspaper stat-icon"></i>
                    </div>
                    <div class="stat-card-body">
                        <p class="stat-value"><?php echo number_format($totalNews); ?></p>
                        <p class="stat-change positive">Articles</p>
                    </div>
                </div>
            </section>

            <section class="additional-cards">
                <div class="info-card">
                    <div class="card-header">
                        <h3>Recent News Articles</h3>
                    </div>
                    <div class="card-body">
                        <?php if (empty($news)): ?>
                            <p style="text-align: center; color: var(--text-secondary); padding: 2rem;">No news articles yet. Click "Fetch News" to fetch articles from News API.</p>
                        <?php else: ?>
                            <?php foreach ($news as $article): ?>
                                <div class="transaction-item">
                                    <div class="transaction-info">
                                        <div class="transaction-avatar"><?php echo strtoupper(substr($article['title'], 0, 2)); ?></div>
                                        <div>
                                            <p class="transaction-name"><?php echo htmlspecialchars($article['title']); ?></p>
                                            <p class="transaction-date">
                                                <?php echo htmlspecialchars($article['source'] ?: 'Unknown'); ?> • 
                                                <?php echo date('M j, Y', strtotime($article['published_at'])); ?>
                                            </p>
                                        </div>
                                    </div>
                                    <div class="transaction-amount">
                                        <a href="/article.php?slug=<?php echo htmlspecialchars($article['slug']); ?>" style="color: var(--primary-color);">
                                            <i class="fa-solid fa-eye"></i> View
                                        </a>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </section>
        </div>
    </main>

<?php include __DIR__ . '/includes/footer.php'; ?>

