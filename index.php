<?php
require_once __DIR__ . '/bootstrap.php';

$db = Database::getInstance();
$seo = new SEO();
$seo->setTitle('Music Charts & Entertainment')
    ->setDescription('Discover the hottest music charts, trending videos, and entertainment news. Real-time Billboard-style charts with top 100 songs, videos, and industry insights.')
    ->setKeywords(['music charts', 'billboard', 'hot 100', 'trending music', 'music videos', 'entertainment news'])
    ->setType('website');

// Detect user location
session_start();
$geo = new Geolocation();
$userCountry = $geo->detectCountry();
$countryName = $geo->getCountryName($userCountry);
$isAfrican = $geo->isAfricanCountry($userCountry);

// Get trending data for user's country
$trendingMusic = $db->fetchAll(
    "SELECT * FROM music_charts WHERE chart_date = CURDATE() AND country_code = :country ORDER BY rank ASC LIMIT 10",
    ['country' => $userCountry]
);

// Fallback to any country if no data for user's country
if (empty($trendingMusic)) {
    $trendingMusic = $db->fetchAll("SELECT * FROM music_charts WHERE chart_date = CURDATE() ORDER BY rank ASC LIMIT 10");
}

$trendingVideos = $db->fetchAll(
    "SELECT * FROM videos WHERE chart_date = CURDATE() AND country_code = :country ORDER BY rank ASC LIMIT 10",
    ['country' => $userCountry]
);

if (empty($trendingVideos)) {
    $trendingVideos = $db->fetchAll("SELECT * FROM videos WHERE chart_date = CURDATE() ORDER BY rank ASC LIMIT 10");
}

$recentNews = $db->fetchAll("SELECT * FROM news_articles ORDER BY published_at DESC LIMIT 3");

// Stats
$totalSongs = $db->fetchOne("SELECT COUNT(*) as count FROM music_charts")['count'] ?? 0;
$totalVideos = $db->fetchOne("SELECT COUNT(*) as count FROM videos")['count'] ?? 0;

include __DIR__ . '/includes/header.php';

// Structured data
echo SEO::generateStructuredData('WebSite', [
    'name' => APP_NAME,
    'url' => APP_URL,
    'description' => 'Music charts and entertainment platform'
]);
?>

    <!-- Hero Section -->
    <section class="hero">
        <div class="hero-background">
            <div class="hero-particles"></div>
        </div>
        <div class="container">
            <div class="hero-content">
                <h1 class="hero-title">
                    <span class="title-line">Discover the</span>
                    <span class="title-line highlight">Hottest Music</span>
                    <span class="title-line">Charts</span>
                </h1>
                <p class="hero-subtitle">Real-time music charts, trending videos, and entertainment news from around the world</p>
                <?php if ($isAfrican): ?>
                    <p style="margin-top: 1rem; color: #00d4aa; font-weight: 600;">
                        <i class="fas fa-map-marker-alt"></i> Showing charts for <?php echo htmlspecialchars($countryName); ?> - Enhanced African coverage
                    </p>
                <?php endif; ?>
                <div class="hero-stats">
                    <div class="stat-item">
                        <div class="stat-number" data-target="<?php echo number_format($totalSongs * 20); ?>">0</div>
                        <div class="stat-label">Monthly Listeners</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-number" data-target="<?php echo number_format($totalSongs); ?>">0</div>
                        <div class="stat-label">Songs Tracked</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-number" data-target="200">0</div>
                        <div class="stat-label">Countries</div>
                    </div>
                </div>
                <div class="hero-actions">
                    <a href="/charts.php" class="btn btn-primary">Explore Charts</a>
                    <a href="/videos.php" class="btn btn-secondary">Watch Videos</a>
                </div>
            </div>
        </div>
    </section>

    <!-- Featured Charts Section -->
    <section class="featured-charts">
        <div class="container">
            <h2 class="section-title">Featured Charts</h2>
            <div class="charts-grid">
                <div class="chart-card">
                    <div class="chart-icon">
                        <i class="fas fa-music"></i>
                    </div>
                    <h3>Hot 100</h3>
                    <p>Top 100 songs trending worldwide</p>
                    <a href="/charts.php" class="chart-link">View Chart <i class="fas fa-arrow-right"></i></a>
                </div>
                <div class="chart-card">
                    <div class="chart-icon">
                        <i class="fas fa-video"></i>
                    </div>
                    <h3>Top Videos</h3>
                    <p>Most watched music videos</p>
                    <a href="/videos.php" class="chart-link">View Chart <i class="fas fa-arrow-right"></i></a>
                </div>
                <div class="chart-card">
                    <div class="chart-icon">
                        <i class="fas fa-trophy"></i>
                    </div>
                    <h3>Awards</h3>
                    <p>Music industry awards and recognition</p>
                    <a href="/awards.php" class="chart-link">View Awards <i class="fas fa-arrow-right"></i></a>
                </div>
                <div class="chart-card">
                    <div class="chart-icon">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <h3>Business</h3>
                    <p>Music industry business insights</p>
                    <a href="/business.php" class="chart-link">View Charts <i class="fas fa-arrow-right"></i></a>
                </div>
            </div>
        </div>
    </section>

    <!-- Trending Now Section -->
    <section class="trending-now">
        <div class="container">
            <h2 class="section-title">Trending Now</h2>
            
            <!-- Trending Categories Tabs -->
            <div class="trending-tabs">
                <button class="trending-tab active" data-category="music">Music</button>
                <button class="trending-tab" data-category="videos">Videos</button>
                <button class="trending-tab" data-category="news">News</button>
            </div>

            <!-- Trending Music -->
            <div class="trending-content active" id="trending-music">
                <div class="trending-grid">
                    <?php if (empty($trendingMusic)): ?>
                        <p style="grid-column: 1 / -1; text-align: center; padding: 2rem; color: #666;">No music data available yet. Check back soon!</p>
                    <?php else: ?>
                        <?php foreach (array_slice($trendingMusic, 0, 3) as $song): ?>
                            <div class="trending-item">
                                <div class="trending-rank"><?php echo $song['rank']; ?></div>
                                <div class="trending-artwork">
                                    <img src="<?php echo htmlspecialchars($song['artwork_url'] ?: 'https://via.placeholder.com/60x60/00d4aa/ffffff?text=' . substr($song['title'], 0, 2)); ?>" alt="<?php echo htmlspecialchars($song['title']); ?>" class="artwork-img">
                                </div>
                                <div class="trending-info">
                                    <h4><?php echo htmlspecialchars($song['title']); ?></h4>
                                    <p><?php echo htmlspecialchars($song['artist']); ?></p>
                                    <div class="trending-stats">
                                        <span class="weeks"><?php echo $song['weeks_on_chart']; ?> weeks</span>
                                        <span class="peak">Peak: #<?php echo $song['peak_position']; ?></span>
                                    </div>
                                </div>
                                <div class="trending-actions">
                                    <button class="play-btn"><i class="fas fa-play"></i></button>
                                    <button class="add-btn"><i class="fas fa-plus"></i></button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Trending Videos -->
            <div class="trending-content" id="trending-videos">
                <div class="trending-grid">
                    <?php if (empty($trendingVideos)): ?>
                        <p style="grid-column: 1 / -1; text-align: center; padding: 2rem; color: #666;">No video data available yet. Check back soon!</p>
                    <?php else: ?>
                        <?php foreach (array_slice($trendingVideos, 0, 3) as $video): ?>
                            <div class="trending-item">
                                <div class="trending-rank"><?php echo $video['rank']; ?></div>
                                <div class="trending-artwork">
                                    <img src="<?php echo htmlspecialchars($video['thumbnail_url'] ?: 'https://via.placeholder.com/60x60/f093fb/ffffff?text=MV'); ?>" alt="<?php echo htmlspecialchars($video['title']); ?>" class="artwork-img">
                                </div>
                                <div class="trending-info">
                                    <h4><?php echo htmlspecialchars($video['title']); ?></h4>
                                    <p><?php echo htmlspecialchars($video['artist']); ?></p>
                                    <div class="trending-stats">
                                        <span class="views"><?php echo number_format($video['views'] / 1000000, 1); ?>M views</span>
                                        <span class="time"><?php echo $video['upload_date'] ?: 'Recently'; ?></span>
                                    </div>
                                </div>
                                <div class="trending-actions">
                                    <button class="play-btn"><i class="fas fa-play"></i></button>
                                    <button class="add-btn"><i class="fas fa-share"></i></button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Trending News -->
            <div class="trending-content" id="trending-news">
                <div class="trending-grid">
                    <?php if (empty($recentNews)): ?>
                        <p style="grid-column: 1 / -1; text-align: center; padding: 2rem; color: #666;">No news available yet. Check back soon!</p>
                    <?php else: ?>
                        <?php foreach ($recentNews as $index => $news): ?>
                            <div class="trending-item">
                                <div class="trending-rank"><?php echo $index + 1; ?></div>
                                <div class="trending-artwork">
                                    <img src="<?php echo htmlspecialchars($news['image_url'] ?: 'https://via.placeholder.com/60x60/00f2fe/ffffff?text=NEWS'); ?>" alt="<?php echo htmlspecialchars($news['title']); ?>" class="artwork-img">
                                </div>
                                <div class="trending-info">
                                    <h4><?php echo htmlspecialchars($news['title']); ?></h4>
                                    <p><?php echo htmlspecialchars($news['source'] ?: 'Music Industry News'); ?></p>
                                    <div class="trending-stats">
                                        <span class="time"><?php echo date('M j, Y', strtotime($news['published_at'])); ?></span>
                                        <span class="category"><?php echo htmlspecialchars($news['category'] ?: 'News'); ?></span>
                                    </div>
                                </div>
                                <div class="trending-actions">
                                    <a href="/news/<?php echo htmlspecialchars($news['slug']); ?>" class="play-btn"><i class="fas fa-external-link-alt"></i></a>
                                    <button class="add-btn"><i class="fas fa-bookmark"></i></button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>

<?php include __DIR__ . '/includes/footer.php'; ?>

