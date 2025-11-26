<?php
require_once __DIR__ . '/bootstrap.php';

session_start();

$db = Database::getInstance();
$geo = new Geolocation();
$countryCode = $_GET['country'] ?? $geo->detectCountry();
$countryName = $geo->getCountryName($countryCode);

$seo = new SEO();
$seo->setTitle("Music Hall of Fame - {$countryName}")
    ->setDescription("Explore the Music Hall of Fame. Celebrating legendary artists, musicians, and industry icons who have made significant contributions to music.")
    ->setKeywords(['hall of fame', 'music legends', 'famous musicians', 'music icons', strtolower($countryName)])
    ->setType('website');

// Get Hall of Fame entries
$hallOfFame = $db->fetchAll(
    "SELECT * FROM hall_of_fame WHERE country_code = :country OR country_code IS NULL ORDER BY year_inducted DESC, artist_name ASC",
    ['country' => $countryCode]
);

// Get available countries
$availableCountries = $db->fetchAll(
    "SELECT DISTINCT country_code, COUNT(*) as count 
     FROM hall_of_fame 
     WHERE country_code IS NOT NULL 
     GROUP BY country_code 
     ORDER BY count DESC"
);

include __DIR__ . '/includes/header.php';
?>

    <section class="chart-header">
        <div class="container">
            <h1 class="chart-title">MUSIC HALL OF FAME</h1>
            <p class="section-subtitle">Celebrating Music Legends and Icons</p>
            <?php if (count($availableCountries) > 0): ?>
                <div style="margin-top: 1rem; text-align: center;">
                    <select id="countrySelector" class="select-control" onchange="window.location.href='?country=' + this.value">
                        <option value="">All Countries</option>
                        <?php foreach ($availableCountries as $country): ?>
                            <option value="<?php echo htmlspecialchars($country['country_code']); ?>" 
                                <?php echo $country['country_code'] === $countryCode ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($geo->getCountryName($country['country_code'])); ?> (<?php echo $country['count']; ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <section class="chart-content">
        <div class="container">
            <?php if (empty($hallOfFame)): ?>
                <div class="empty-state">
                    <h2>Hall of Fame Coming Soon</h2>
                    <p>We're building our Hall of Fame with legendary artists and musicians.</p>
                </div>
            <?php else: ?>
                <div class="card-grid">
                    <?php foreach ($hallOfFame as $inductee): ?>
                        <div class="info-card">
                            <div style="text-align: center; margin-bottom: 1rem;">
                                <?php if ($inductee['image_url']): ?>
                                    <img src="<?php echo htmlspecialchars($inductee['image_url']); ?>" 
                                         alt="<?php echo htmlspecialchars($inductee['artist_name']); ?>" 
                                         style="width: 150px; height: 150px; border-radius: 50%; object-fit: cover; border: 4px solid #00d4aa;">
                                <?php else: ?>
                                    <div style="width: 150px; height: 150px; border-radius: 50%; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); margin: 0 auto; display: flex; align-items: center; justify-content: center; font-size: 3rem; color: white; border: 4px solid #00d4aa;">
                                        <?php echo strtoupper(substr($inductee['artist_name'], 0, 2)); ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <h3 style="text-align: center; margin-bottom: 0.5rem; color: #333;"><?php echo htmlspecialchars($inductee['artist_name']); ?></h3>
                            <?php if ($inductee['category']): ?>
                                <p style="text-align: center; color: #00d4aa; font-weight: 600; margin-bottom: 0.5rem;"><?php echo htmlspecialchars($inductee['category']); ?></p>
                            <?php endif; ?>
                            <?php if ($inductee['year_inducted']): ?>
                                <p style="text-align: center; color: #666; font-size: 0.9rem; margin-bottom: 1rem;">Inducted <?php echo $inductee['year_inducted']; ?></p>
                            <?php endif; ?>
                            <?php if ($inductee['description']): ?>
                                <p style="color: #666; font-size: 0.9rem; line-height: 1.6;"><?php echo htmlspecialchars(substr($inductee['description'], 0, 150)); ?>...</p>
                            <?php endif; ?>
                            <?php if ($inductee['achievements']): ?>
                                <div style="margin-top: 1rem; padding-top: 1rem; border-top: 1px solid #eee;">
                                    <strong style="color: #333;">Achievements:</strong>
                                    <p style="color: #666; font-size: 0.85rem; margin-top: 0.5rem;"><?php echo htmlspecialchars(substr($inductee['achievements'], 0, 200)); ?></p>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </section>

<?php include __DIR__ . '/includes/footer.php'; ?>

