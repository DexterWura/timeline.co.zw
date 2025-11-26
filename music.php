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
        <div class="container">
            <?php if (empty($charts)): ?>
                <div style="text-align: center; padding: 4rem;">
                    <h2>No music data available</h2>
                    <p>Music charts will be available once data is fetched from APIs.</p>
                </div>
            <?php else: ?>
                <div class="chart-list">
                    <?php foreach ($charts as $song): ?>
                        <div class="chart-item">
                            <div class="chart-rank"><?php echo $song['rank']; ?></div>
                            <div class="chart-artwork">
                                <img src="<?php echo htmlspecialchars($song['artwork_url'] ?: 'https://via.placeholder.com/80x80/00d4aa/ffffff?text=' . substr($song['title'], 0, 2)); ?>" alt="<?php echo htmlspecialchars($song['title']); ?>">
                            </div>
                            <div class="chart-info">
                                <h3><?php echo htmlspecialchars($song['title']); ?></h3>
                                <p><?php echo htmlspecialchars($song['artist']); ?></p>
                                <div class="chart-meta">
                                    <span><?php echo $song['weeks_on_chart']; ?> weeks</span>
                                    <span>Peak: #<?php echo $song['peak_position']; ?></span>
                                </div>
                            </div>
                            <div class="chart-stats">
                                <div class="stat">
                                    <span class="stat-label">Streams</span>
                                    <span class="stat-value"><?php echo number_format($song['streams'] / 1000000, 1); ?>M</span>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </section>

<?php include __DIR__ . '/includes/footer.php'; ?>

