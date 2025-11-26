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
                        <select id="countrySelector" onchange="window.location.href='?country=' + this.value + '&date=<?php echo $date; ?>'" style="padding: 0.5rem 1rem; border: 1px solid #ddd; border-radius: 5px; background: white;">
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
        <div class="container">
            <?php if (empty($videos)): ?>
                <div style="text-align: center; padding: 4rem;">
                    <h2>No video data available</h2>
                    <p>Videos will be available once data is fetched from YouTube API.</p>
                </div>
            <?php else: ?>
                <div class="chart-list">
                    <?php foreach ($videos as $video): ?>
                        <div class="chart-item">
                            <div class="chart-rank"><?php echo $video['rank']; ?></div>
                            <div class="chart-artwork">
                                <img src="<?php echo htmlspecialchars($video['thumbnail_url'] ?: 'https://via.placeholder.com/80x80/00d4aa/ffffff?text=MV'); ?>" alt="<?php echo htmlspecialchars($video['title']); ?>">
                            </div>
                            <div class="chart-info">
                                <h3><?php echo htmlspecialchars($video['title']); ?></h3>
                                <p><?php echo htmlspecialchars($video['artist']); ?></p>
                                <div class="chart-meta">
                                    <span><?php echo number_format($video['views']); ?> views</span>
                                    <span><?php echo number_format($video['likes']); ?> likes</span>
                                </div>
                            </div>
                            <div class="chart-stats">
                                <div class="stat">
                                    <span class="stat-label">Views</span>
                                    <span class="stat-value"><?php echo number_format($video['views'] / 1000000, 1); ?>M</span>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </section>

<?php include __DIR__ . '/includes/footer.php'; ?>

