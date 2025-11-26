<?php
require_once __DIR__ . '/bootstrap.php';

// Check if page is enabled
$settings = new Settings();
if (!$settings->get('page_enabled_richest', 1)) {
    header('HTTP/1.0 404 Not Found');
    include __DIR__ . '/404.php';
    exit;
}

session_start();

$db = Database::getInstance();
$geo = new Geolocation();

// Detect or get country
$countryCode = $_GET['country'] ?? 'US'; // Default to global (US)
$countryName = $countryCode === 'US' ? 'World' : $geo->getCountryName($countryCode);

$seo = new SEO();
$seo->setTitle("Top 100 Richest People - {$countryName}")
    ->setDescription("Discover the world's richest people in {$countryName}. Top 100 billionaires ranked by net worth, including tech moguls, entertainment industry leaders, and business tycoons.")
    ->setKeywords(['richest people', 'billionaires', 'wealth', 'net worth', strtolower($countryName)])
    ->setType('website');

$date = $_GET['date'] ?? date('Y-m-d');
$richest = $db->fetchAll(
    "SELECT * FROM richest_people WHERE chart_date = :date AND country_code = :country ORDER BY rank ASC",
    ['date' => $date, 'country' => $countryCode]
);

if (empty($richest)) {
    $latestDate = $db->fetchOne(
        "SELECT MAX(chart_date) as date FROM richest_people WHERE country_code = :country",
        ['country' => $countryCode]
    );
    if ($latestDate && $latestDate['date']) {
        $date = $latestDate['date'];
        $richest = $db->fetchAll(
            "SELECT * FROM richest_people WHERE chart_date = :date AND country_code = :country ORDER BY rank ASC",
            ['date' => $date, 'country' => $countryCode]
        );
    }
}

// Get available countries
$availableCountries = $db->fetchAll(
    "SELECT DISTINCT c.country_code, c.country_name, COUNT(rp.id) as count 
     FROM countries c 
     INNER JOIN richest_people rp ON c.country_code = rp.country_code 
     WHERE rp.chart_date = :date 
     GROUP BY c.country_code, c.country_name
     ORDER BY count DESC, c.country_name ASC",
    ['date' => $date]
);

include __DIR__ . '/includes/header.php';
?>

    <section class="chart-header">
        <div class="container">
            <h1 class="chart-title">TOP 100 RICHEST PEOPLE - <?php echo strtoupper($countryName); ?></h1>
            <p class="section-subtitle">Ranked by net worth and wealth</p>
            <div style="margin-top: 1rem; text-align: center;">
                <select id="countrySelector" class="select-control" onchange="window.location.href='?country=' + this.value + '&date=<?php echo $date; ?>'">
                    <option value="US" <?php echo $countryCode === 'US' ? 'selected' : ''; ?>>World (Global)</option>
                    <?php foreach ($availableCountries as $country): ?>
                        <option value="<?php echo htmlspecialchars($country['country_code']); ?>" 
                            <?php echo $country['country_code'] === $countryCode ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($country['country_name']); ?> (<?php echo $country['count']; ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
    </section>

    <section class="chart-content">
        <div class="container">
            <?php if (empty($richest)): ?>
                <div class="empty-state">
                    <h2>No Data Available</h2>
                    <p>Richest people rankings will be available once data is fetched from APIs.</p>
                </div>
            <?php else: ?>
                <div class="chart-list">
                    <?php foreach ($richest as $person): ?>
                        <div class="chart-item">
                            <div class="chart-rank"><?php echo $person['rank']; ?></div>
                            <div class="chart-artwork">
                                <div style="width: 80px; height: 80px; border-radius: 50%; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); display: flex; align-items: center; justify-content: center; color: white; font-size: 1.5rem; font-weight: bold;">
                                    <?php echo strtoupper(substr($person['name'], 0, 2)); ?>
                                </div>
                            </div>
                            <div class="chart-info">
                                <h3><?php echo htmlspecialchars($person['name']); ?></h3>
                                <p><?php echo htmlspecialchars($person['source'] ?: 'Various'); ?></p>
                                <div class="chart-meta">
                                    <span>Net Worth</span>
                                </div>
                            </div>
                            <div class="chart-stats">
                                <div class="stat">
                                    <span class="stat-label">Net Worth</span>
                                    <span class="stat-value">$<?php 
                                        $netWorth = $person['net_worth'];
                                        if ($netWorth >= 1000000000) {
                                            echo number_format($netWorth / 1000000000, 1) . 'B';
                                        } else {
                                            echo number_format($netWorth / 1000000, 1) . 'M';
                                        }
                                    ?></span>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </section>

<?php include __DIR__ . '/includes/footer.php'; ?>

