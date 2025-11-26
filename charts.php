<?php
require_once __DIR__ . '/bootstrap.php';

session_start();

$db = Database::getInstance();
$geo = new Geolocation();

// Detect or get country
$countryCode = $_GET['country'] ?? $geo->detectCountry();
$countryName = $geo->getCountryName($countryCode);
$isAfrican = $geo->isAfricanCountry($countryCode);

// Store in session
$_SESSION['user_country'] = $countryCode;

$seo = new SEO();
$seo->setTitle("Timeline Hot 100 - {$countryName} Music Charts")
    ->setDescription("View the latest Timeline Hot 100 music charts for {$countryName}. Top 100 songs ranked by streams, plays, and popularity. Updated weekly with the hottest tracks.")
    ->setKeywords(['hot 100', 'music charts', 'billboard', 'top songs', strtolower($countryName), 'music rankings', 'trending music'])
    ->setType('website');

$date = $_GET['date'] ?? date('Y-m-d');
$charts = $db->fetchAll(
    "SELECT * FROM music_charts WHERE chart_date = :date AND country_code = :country ORDER BY rank ASC",
    ['date' => $date, 'country' => $countryCode]
);

if (empty($charts)) {
    // Try to get latest available date for this country
    $latestDate = $db->fetchOne(
        "SELECT MAX(chart_date) as date FROM music_charts WHERE country_code = :country",
        ['country' => $countryCode]
    );
    if ($latestDate && $latestDate['date']) {
        $date = $latestDate['date'];
        $charts = $db->fetchAll(
            "SELECT * FROM music_charts WHERE chart_date = :date AND country_code = :country ORDER BY rank ASC",
            ['date' => $date, 'country' => $countryCode]
        );
    }
    
    // If still empty, try global charts
    if (empty($charts)) {
        $charts = $db->fetchAll(
            "SELECT * FROM music_charts WHERE chart_date = :date ORDER BY rank ASC LIMIT 100",
            ['date' => $date]
        );
    }
}

// Get available countries for selector
$availableCountries = $db->fetchAll(
    "SELECT DISTINCT c.country_code, c.country_name, c.is_african 
     FROM countries c 
     INNER JOIN music_charts mc ON c.country_code = mc.country_code 
     WHERE mc.chart_date = :date 
     ORDER BY c.is_african DESC, c.priority DESC, c.country_name ASC",
    ['date' => $date]
);

include __DIR__ . '/includes/header.php';
?>

    <!-- Chart Header -->
    <section class="chart-header">
        <div class="container">
            <div class="chart-title-section">
                <h1 class="chart-title">TIMELINE HOT 100™ - <?php echo strtoupper($countryName); ?></h1>
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

    <!-- Chart Content -->
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
                    <a href="/charts.php" class="sidebar-section active">
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
                    <a href="/videos.php" class="sidebar-section">
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
            <?php if (empty($charts)): ?>
                <div class="empty-state">
                    <h2>No chart data available</h2>
                    <p>Charts will be available once data is fetched from APIs.</p>
                </div>
            <?php else: ?>
                <div class="chart-list">
                    <?php foreach ($charts as $song): ?>
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
                </div>
            <?php endif; ?>
                </div>
            </div>
        </div>
    </section>

<?php include __DIR__ . '/includes/footer.php'; ?>

