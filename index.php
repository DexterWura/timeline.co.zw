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

    <!-- Billboard-Style Hot 100 Chart List -->
    <section class="main-container">
        <button class="sidebar-toggle-mobile" id="sidebarToggleMobile" aria-label="Toggle sidebar">
            <i class="fas fa-bars"></i>
            <span>Charts</span>
        </button>
        <aside class="sidebar" id="frontendSidebar">
            <div class="sidebar-header">
                <h3>Charts</h3>
                <button class="sidebar-close-mobile" id="sidebarCloseMobile" aria-label="Close sidebar">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="sidebar-group">
                <div class="sidebar-group-title">Hot Charts</div>
                <a href="/charts.php" class="sidebar-section <?php echo $currentPage == 'charts' ? 'active' : ''; ?>">
                    <span>Hot 100</span>
                    <i class="fas fa-chevron-right"></i>
                </a>
                <a href="/charts.php?chart=200" class="sidebar-section">
                    <span>Timeline 200</span>
                    <i class="fas fa-chevron-right"></i>
                </a>
                <a href="/charts.php?chart=global" class="sidebar-section">
                    <span>Global 200</span>
                    <i class="fas fa-chevron-right"></i>
                </a>
                <a href="/charts.php?chart=artist" class="sidebar-section">
                    <span>Artist 100</span>
                    <i class="fas fa-chevron-right"></i>
                </a>
            </div>
            <div class="sidebar-group">
                <div class="sidebar-group-title">Music</div>
                <a href="/music.php" class="sidebar-section <?php echo $currentPage == 'music' ? 'active' : ''; ?>">
                    <span>Top Songs</span>
                    <i class="fas fa-chevron-right"></i>
                </a>
                <a href="/videos.php" class="sidebar-section <?php echo $currentPage == 'videos' ? 'active' : ''; ?>">
                    <span>Top Videos</span>
                    <i class="fas fa-chevron-right"></i>
                </a>
            </div>
            <div class="sidebar-group">
                <div class="sidebar-group-title">More</div>
                <a href="/awards.php" class="sidebar-section <?php echo $currentPage == 'awards' ? 'active' : ''; ?>">
                    <span>Awards</span>
                    <i class="fas fa-chevron-right"></i>
                </a>
                <a href="/hall-of-fame.php" class="sidebar-section <?php echo $currentPage == 'hall-of-fame' ? 'active' : ''; ?>">
                    <span>Hall of Fame</span>
                    <i class="fas fa-chevron-right"></i>
                </a>
                <a href="/richest.php" class="sidebar-section <?php echo $currentPage == 'richest' ? 'active' : ''; ?>">
                    <span>Richest People</span>
                    <i class="fas fa-chevron-right"></i>
                </a>
                <a href="/business.php" class="sidebar-section <?php echo $currentPage == 'business' ? 'active' : ''; ?>">
                    <span>Business</span>
                    <i class="fas fa-chevron-right"></i>
                </a>
            </div>
        </aside>
        <div class="content">
            <!-- Billboard-Style Top 5 Charts Slider -->
            <div class="hero-section">
                <div class="slider-wrapper">
                    <div class="slider" id="topChartsSlider">
                        <?php 
                        // Get top 5 charts
                        $top5Charts = $db->fetchAll(
                            "SELECT * FROM music_charts WHERE chart_date = CURDATE() AND country_code = :country ORDER BY rank ASC LIMIT 5",
                            ['country' => $userCountry]
                        );
                        
                        if (empty($top5Charts)) {
                            $top5Charts = $db->fetchAll("SELECT * FROM music_charts WHERE chart_date = CURDATE() ORDER BY rank ASC LIMIT 5");
                        }
                        
                        foreach ($top5Charts as $index => $chart):
                        ?>
                        <div class="slide">
                            <div class="slide-image" style="background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%);">
                                <?php if ($chart['artwork_url']): ?>
                                    <img src="<?php echo htmlspecialchars($chart['artwork_url']); ?>" alt="<?php echo htmlspecialchars($chart['title']); ?>" class="slide-image">
                                <?php endif; ?>
                            </div>
                            <div class="slide-content">
                                <div class="slide-rank">#<?php echo $chart['rank']; ?></div>
                                <h2><?php echo htmlspecialchars($chart['title']); ?></h2>
                                <p><?php echo htmlspecialchars($chart['artist']); ?></p>
                                <div class="slide-stats">
                                    <span><i class="fas fa-chart-line"></i> <?php echo number_format($chart['streams']); ?> streams</span>
                                    <span><i class="fas fa-clock"></i> <?php echo $chart['weeks_on_chart']; ?> weeks</span>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="slider-nav">
                        <button id="prev-slide"><i class="fas fa-chevron-left"></i></button>
                        <button id="next-slide"><i class="fas fa-chevron-right"></i></button>
                    </div>
                </div>
            </div>
            
            <div class="chart-header-billboard">
                <h1 class="billboard-title">TIMELINE HOT 100™</h1>
                <p class="billboard-subtitle">The week's most popular songs ranked by streaming activity, radio play, and sales</p>
                <?php if ($isAfrican): ?>
                    <p class="location-badge">
                        <i class="fas fa-map-marker-alt"></i> Showing charts for <?php echo htmlspecialchars($countryName); ?> - Enhanced African coverage
                    </p>
                <?php endif; ?>
            </div>
            
            <div class="chart-list">
                <?php 
                // Get top 20 for homepage
                $homepageCharts = $db->fetchAll(
                    "SELECT * FROM music_charts WHERE chart_date = CURDATE() AND country_code = :country ORDER BY rank ASC LIMIT 20",
                    ['country' => $userCountry]
                );
                
                if (empty($homepageCharts)) {
                    $homepageCharts = $db->fetchAll("SELECT * FROM music_charts WHERE chart_date = CURDATE() ORDER BY rank ASC LIMIT 20");
                }
                
                if (empty($homepageCharts)): ?>
                    <div class="empty-state">
                        <p>No chart data available yet. Check back soon!</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($homepageCharts as $song): ?>
                        <div class="chart-item">
                            <div class="rank-container">
                                <div class="rank-number"><?php echo $song['rank']; ?></div>
                                <div class="rank-indicator">
                                    <?php 
                                    $prevRank = $song['previous_rank'] ?? null;
                                    if ($prevRank) {
                                        if ($song['rank'] < $prevRank) {
                                            echo '<span class="arrow-up"><i class="fas fa-arrow-up"></i></span>';
                                        } elseif ($song['rank'] > $prevRank) {
                                            echo '<span class="arrow-down"><i class="fas fa-arrow-down"></i></span>';
                                        } else {
                                            echo '<span class="arrow-same">—</span>';
                                        }
                                    } else {
                                        echo '<span class="badge new">NEW</span>';
                                    }
                                    ?>
                                </div>
                            </div>
                            <div class="album-cover">
                                <img src="<?php echo htmlspecialchars($song['artwork_url'] ?: 'https://via.placeholder.com/80x80/1a1a1a/ffffff?text=' . substr($song['title'], 0, 2)); ?>" alt="<?php echo htmlspecialchars($song['title']); ?>">
                            </div>
                            <div class="song-info">
                                <div class="song-title"><?php echo htmlspecialchars($song['title']); ?></div>
                                <div class="artist-name"><?php echo htmlspecialchars($song['artist']); ?></div>
                            </div>
                            <div class="chart-stats">
                                <div class="stat-item">
                                    <div class="stat-label">Weeks</div>
                                    <div class="stat-value"><?php echo $song['weeks_on_chart']; ?></div>
                                </div>
                                <div class="stat-item">
                                    <div class="stat-label">Peak</div>
                                    <div class="stat-value">#<?php echo $song['peak_position']; ?></div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                    
                    <div style="text-align: center; margin-top: 2rem;">
                        <a href="/charts.php" class="btn btn-primary">View Full Hot 100 Chart →</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>

<?php include __DIR__ . '/includes/footer.php'; ?>

