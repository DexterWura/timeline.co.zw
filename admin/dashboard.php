<?php
require_once __DIR__ . '/../bootstrap.php';

$pageTitle = 'Dashboard';
$includeCharts = true;

$db = Database::getInstance();

// Get stats
$totalSongs = $db->fetchOne("SELECT COUNT(*) as count FROM music_charts")['count'] ?? 0;
$totalVideos = $db->fetchOne("SELECT COUNT(*) as count FROM videos")['count'] ?? 0;
$totalBlogs = $db->fetchOne("SELECT COUNT(*) as count FROM blogs WHERE status = 'published'")['count'] ?? 0;
$totalNews = $db->fetchOne("SELECT COUNT(*) as count FROM news_articles")['count'] ?? 0;

// Recent activity
$recentCharts = $db->fetchAll("SELECT * FROM music_charts ORDER BY created_at DESC LIMIT 5");
$recentVideos = $db->fetchAll("SELECT * FROM videos ORDER BY created_at DESC LIMIT 5");

include __DIR__ . '/includes/header.php';
?>

    <!-- Main Content -->
    <main class="main-content">
        <!-- Top Bar -->
        <header class="top-bar">
            <div class="top-bar-left">
                <button class="menu-toggle" id="menuToggle">
                    <i class="fa-solid fa-bars"></i>
                </button>
                <h2 class="page-title">Dashboard</h2>
            </div>
            <div class="top-bar-right">
                <button class="search-bar-toggle" aria-label="Toggle search">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </button>
                <div class="search-box">
                    <i class="fa-solid fa-magnifying-glass search-icon"></i>
                    <input type="text" placeholder="Search...">
                </div>
                <button class="notification-btn">
                    <i class="fa-solid fa-bell"></i>
                    <span class="notification-badge">3</span>
                </button>
                <div class="user-profile">
                    <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($currentUser['email']); ?>&background=random" alt="User" class="profile-img">
                </div>
            </div>
        </header>

        <!-- Breadcrumbs -->
        <nav class="breadcrumbs">
            <a href="/admin/dashboard.php">
                <i class="fa-solid fa-house"></i>
                <span>Home</span>
            </a>
            <span class="breadcrumb-separator">
                <i class="fa-solid fa-chevron-right"></i>
            </span>
            <span class="breadcrumb-current">Dashboard</span>
        </nav>

        <!-- Dashboard Content -->
        <div class="dashboard-content">
            <!-- Stats Cards -->
            <section class="stats-section">
                <div class="stat-card">
                    <div class="stat-card-header">
                        <h3>Total Songs</h3>
                        <i class="fa-solid fa-music stat-icon"></i>
                    </div>
                    <div class="stat-card-body">
                        <p class="stat-value"><?php echo number_format($totalSongs); ?></p>
                        <p class="stat-change positive">In database</p>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-card-header">
                        <h3>Total Videos</h3>
                        <i class="fa-solid fa-video stat-icon"></i>
                    </div>
                    <div class="stat-card-body">
                        <p class="stat-value"><?php echo number_format($totalVideos); ?></p>
                        <p class="stat-change positive">In database</p>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-card-header">
                        <h3>Published Blogs</h3>
                        <i class="fa-solid fa-blog stat-icon"></i>
                    </div>
                    <div class="stat-card-body">
                        <p class="stat-value"><?php echo number_format($totalBlogs); ?></p>
                        <p class="stat-change positive">Active posts</p>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-card-header">
                        <h3>News Articles</h3>
                        <i class="fa-solid fa-newspaper stat-icon"></i>
                    </div>
                    <div class="stat-card-body">
                        <p class="stat-value"><?php echo number_format($totalNews); ?></p>
                        <p class="stat-change positive">Total articles</p>
                    </div>
                </div>
            </section>

            <!-- Recent Activity -->
            <section class="additional-cards">
                <div class="info-card">
                    <div class="card-header">
                        <h3>Recent Music Charts</h3>
                        <a href="/admin/music-charts.php" class="view-all">View All</a>
                    </div>
                    <div class="card-body">
                        <?php if (empty($recentCharts)): ?>
                            <p style="text-align: center; color: var(--text-secondary); padding: 2rem;">No charts data yet. <a href="/admin/settings.php">Configure API keys</a> to fetch data.</p>
                        <?php else: ?>
                            <?php foreach ($recentCharts as $chart): ?>
                                <div class="transaction-item">
                                    <div class="transaction-info">
                                        <div class="transaction-avatar"><?php echo strtoupper(substr($chart['artist'], 0, 2)); ?></div>
                                        <div>
                                            <p class="transaction-name"><?php echo htmlspecialchars($chart['title']); ?></p>
                                            <p class="transaction-date"><?php echo htmlspecialchars($chart['artist']); ?> • Rank #<?php echo $chart['rank']; ?></p>
                                        </div>
                                    </div>
                                    <div class="transaction-amount positive"><?php echo number_format($chart['streams']); ?> streams</div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="info-card">
                    <div class="card-header">
                        <h3>Recent Videos</h3>
                        <a href="/admin/videos.php" class="view-all">View All</a>
                    </div>
                    <div class="card-body">
                        <?php if (empty($recentVideos)): ?>
                            <p style="text-align: center; color: var(--text-secondary); padding: 2rem;">No videos data yet. <a href="/admin/settings.php">Configure YouTube API</a> to fetch data.</p>
                        <?php else: ?>
                            <?php foreach ($recentVideos as $video): ?>
                                <div class="transaction-item">
                                    <div class="transaction-info">
                                        <div class="transaction-avatar"><?php echo strtoupper(substr($video['artist'], 0, 2)); ?></div>
                                        <div>
                                            <p class="transaction-name"><?php echo htmlspecialchars($video['title']); ?></p>
                                            <p class="transaction-date"><?php echo htmlspecialchars($video['artist']); ?> • Rank #<?php echo $video['rank']; ?></p>
                                        </div>
                                    </div>
                                    <div class="transaction-amount positive"><?php echo number_format($video['views']); ?> views</div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="info-card">
                    <div class="card-header">
                        <h3>Quick Actions</h3>
                    </div>
                    <div class="card-body quick-action-grid">
                        <a href="/admin/settings.php" class="quick-action-btn quick-action-btn-primary">
                            <i class="fa-solid fa-key"></i>
                            <span class="quick-action-label">API Settings</span>
                        </a>
                        <a href="/admin/blog.php" class="quick-action-btn quick-action-btn-purple">
                            <i class="fa-solid fa-blog"></i>
                            <span class="quick-action-label">New Blog Post</span>
                        </a>
                        <a href="/api/fetch-music.php" class="quick-action-btn quick-action-btn-success" onclick="event.preventDefault(); fetchMusic(); return false;">
                            <i class="fa-solid fa-sync"></i>
                            <span class="quick-action-label">Fetch Music</span>
                        </a>
                        <a href="/api/fetch-videos.php" class="quick-action-btn quick-action-btn-danger" onclick="event.preventDefault(); fetchVideos(); return false;">
                            <i class="fa-solid fa-video"></i>
                            <span class="quick-action-label">Fetch Videos</span>
                        </a>
                        <a href="/api/fetch-awards.php" class="quick-action-btn" style="background: #f39c12;" onclick="event.preventDefault(); fetchAwards(); return false;">
                            <i class="fa-solid fa-trophy"></i>
                            <span class="quick-action-label">Fetch Awards</span>
                        </a>
                        <a href="/api/fetch-richest.php" class="quick-action-btn" style="background: #27ae60;" onclick="event.preventDefault(); fetchRichest(); return false;">
                            <i class="fa-solid fa-dollar-sign"></i>
                            <span class="quick-action-label">Fetch Richest</span>
                        </a>
                    </div>
                </div>
            </section>
        </div>
    </main>

<script>
function fetchMusic() {
    fetch('/api/fetch-music.php')
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                alert('Music charts fetched successfully!');
                location.reload();
            } else {
                alert('Error: ' + (data.error || 'Failed to fetch music'));
            }
        });
}

function fetchVideos() {
    fetch('/api/fetch-videos.php')
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                alert('Videos fetched successfully!');
                location.reload();
            } else {
                alert('Error: ' + (data.error || 'Failed to fetch videos'));
            }
        });
}

function fetchAwards() {
    fetch('/api/fetch-awards.php')
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                alert('Awards data fetched successfully!');
                location.reload();
            } else {
                alert('Error: ' + (data.error || 'Failed to fetch awards'));
            }
        });
}

function fetchRichest() {
    fetch('/api/fetch-richest.php')
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                alert('Richest people data fetched successfully!');
                location.reload();
            } else {
                alert('Error: ' + (data.error || 'Failed to fetch richest people'));
            }
        });
}
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>

