<?php
require_once __DIR__ . '/bootstrap.php';

$db = Database::getInstance();
$seo = new SEO();
$seo->setTitle('Hot 100 Music Charts')
    ->setDescription('Discover the hottest music tracks. Top 100 songs ranked by streams, plays, and popularity across all genres.')
    ->setKeywords(['hot 100', 'music', 'songs', 'charts', 'top music', 'trending songs'])
    ->setType('website');

$date = $_GET['date'] ?? date('Y-m-d');
$charts = $db->fetchAll(
    "SELECT * FROM music_charts WHERE chart_date = :date ORDER BY rank ASC",
    ['date' => $date]
);

if (empty($charts)) {
    $latestDate = $db->fetchOne("SELECT MAX(chart_date) as date FROM music_charts");
    if ($latestDate && $latestDate['date']) {
        $date = $latestDate['date'];
        $charts = $db->fetchAll(
            "SELECT * FROM music_charts WHERE chart_date = :date ORDER BY rank ASC",
            ['date' => $date]
        );
    }
}

include __DIR__ . '/includes/header.php';
?>

    <section class="music-header">
        <div class="container">
            <div class="music-title-section">
                <h1 class="music-title">HOT 100 MUSIC</h1>
                <div class="music-controls">
                    <div class="date-selector">
                        <button class="date-btn">
                            <i class="fas fa-calendar"></i>
                            AS OF <?php echo strtoupper(date('F Y', strtotime($date))); ?>
                        </button>
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
                    <a href="/music.php" class="sidebar-section active">
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
                    <h2>No music data available</h2>
                    <p>Music charts will be available once data is fetched from APIs.</p>
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

