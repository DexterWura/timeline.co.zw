<?php
require_once __DIR__ . '/bootstrap.php';

session_start();

$db = Database::getInstance();
$geo = new Geolocation();

// Detect or get country
$countryCode = $_GET['country'] ?? $geo->detectCountry();
$countryName = $geo->getCountryName($countryCode);
$isAfrican = $geo->isAfricanCountry($countryCode);

$seo = new SEO();
$seo->setTitle("Top 100 Music Videos - {$countryName}")
    ->setDescription("Watch the top 100 trending music videos for {$countryName}. Most viewed and liked music videos ranked by popularity, views, and engagement.")
    ->setKeywords(['music videos', 'top videos', 'youtube videos', strtolower($countryName), 'trending videos', 'music video charts'])
    ->setType('website');

$date = $_GET['date'] ?? date('Y-m-d');
$videos = $db->fetchAll(
    "SELECT * FROM videos WHERE chart_date = :date AND country_code = :country ORDER BY rank ASC",
    ['date' => $date, 'country' => $countryCode]
);

if (empty($videos)) {
    $latestDate = $db->fetchOne(
        "SELECT MAX(chart_date) as date FROM videos WHERE country_code = :country",
        ['country' => $countryCode]
    );
    if ($latestDate && $latestDate['date']) {
        $date = $latestDate['date'];
        $videos = $db->fetchAll(
            "SELECT * FROM videos WHERE chart_date = :date AND country_code = :country ORDER BY rank ASC",
            ['date' => $date, 'country' => $countryCode]
        );
    }
    
    // Fallback to global
    if (empty($videos)) {
        $videos = $db->fetchAll(
            "SELECT * FROM videos WHERE chart_date = :date ORDER BY rank ASC LIMIT 100",
            ['date' => $date]
        );
    }
}

// Get available countries
$availableCountries = $db->fetchAll(
    "SELECT DISTINCT c.country_code, c.country_name, c.is_african 
     FROM countries c 
     INNER JOIN videos v ON c.country_code = v.country_code 
     WHERE v.chart_date = :date 
     ORDER BY c.is_african DESC, c.priority DESC, c.country_name ASC",
    ['date' => $date]
);

include __DIR__ . '/includes/header.php';
?>

    <section class="chart-header">
        <div class="container">
            <div class="chart-title-section">
                <h1 class="chart-title">TOP 100 MUSIC VIDEOS - <?php echo strtoupper($countryName); ?></h1>
                <div class="chart-controls">
                    <div class="date-selector">
                        <button class="date-btn">
                            <i class="fas fa-calendar"></i>
                            WEEK OF <?php echo strtoupper(date('F j, Y', strtotime($date))); ?>
                        </button>
                    </div>
                    <div style="margin-top: 1rem;">
                        <select id="countrySelector" class="select-control" onchange="window.location.href='?country=' + this.value + '&date=<?php echo $date; ?>'">
                            <option value="">Select Country</option>
                            <?php foreach ($availableCountries as $country): ?>
                                <option value="<?php echo htmlspecialchars($country['country_code']); ?>" 
                                    <?php echo $country['country_code'] === $countryCode ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($country['country_name']); ?>
                                    <?php if ($country['is_african']): ?> 🇿🇼<?php endif; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <?php if ($isAfrican): ?>
                            <span style="margin-left: 1rem; color: #00d4aa; font-weight: 600;">
                                <i class="fas fa-star"></i> African Country - Enhanced Coverage
                            </span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="chart-content">
        <div class="main-container">
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
                    <a href="/charts.php" class="sidebar-section">
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
                    <a href="/music.php" class="sidebar-section">
                        <span>Top Songs</span>
                        <i class="fas fa-chevron-right"></i>
                    </a>
                    <a href="/videos.php" class="sidebar-section active">
                        <span>Top Videos</span>
                        <i class="fas fa-chevron-right"></i>
                    </a>
                </div>
                <div class="sidebar-group">
                    <div class="sidebar-group-title">More</div>
                    <a href="/awards.php" class="sidebar-section">
                        <span>Awards</span>
                        <i class="fas fa-chevron-right"></i>
                    </a>
                    <a href="/hall-of-fame.php" class="sidebar-section">
                        <span>Hall of Fame</span>
                        <i class="fas fa-chevron-right"></i>
                    </a>
                    <a href="/richest.php" class="sidebar-section">
                        <span>Richest People</span>
                        <i class="fas fa-chevron-right"></i>
                    </a>
                    <a href="/business.php" class="sidebar-section">
                        <span>Business</span>
                        <i class="fas fa-chevron-right"></i>
                    </a>
                </div>
            </aside>
            <div class="content">
                <div class="container">
                    <?php if (empty($videos)): ?>
                <div class="empty-state">
                    <h2>No video data available</h2>
                    <p>Videos will be available once data is fetched from YouTube API.</p>
                </div>
            <?php else: ?>
                <div class="chart-list">
                    <?php foreach ($videos as $video): ?>
                        <div class="chart-item">
                            <div class="rank-container">
                                <div class="rank-number"><?php echo $video['rank']; ?></div>
                                <div class="rank-indicator">
                                    <?php 
                                    $prevRank = $video['previous_rank'] ?? null;
                                    if ($prevRank) {
                                        if ($video['rank'] < $prevRank) {
                                            echo '<span class="arrow-up"><i class="fas fa-arrow-up"></i></span>';
                                        } elseif ($video['rank'] > $prevRank) {
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
                                <img src="<?php echo htmlspecialchars($video['thumbnail_url'] ?: 'https://via.placeholder.com/80x80/1a1a1a/ffffff?text=MV'); ?>" alt="<?php echo htmlspecialchars($video['title']); ?>">
                            </div>
                            <div class="song-info">
                                <div class="song-title"><?php echo htmlspecialchars($video['title']); ?></div>
                                <div class="artist-name"><?php echo htmlspecialchars($video['artist']); ?></div>
                            </div>
                            <div class="chart-stats">
                                <div class="stat-item">
                                    <div class="stat-label">Views</div>
                                    <div class="stat-value"><?php echo number_format($video['views'] / 1000000, 1); ?>M</div>
                                </div>
                                <div class="stat-item">
                                    <div class="stat-label">Likes</div>
                                    <div class="stat-value"><?php echo number_format($video['likes'] / 1000, 1); ?>K</div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
                </div>
            </div>
        </div>
    </section>

<?php include __DIR__ . '/includes/footer.php'; ?>

