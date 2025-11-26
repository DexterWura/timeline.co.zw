<?php
require_once __DIR__ . '/../bootstrap.php';

$pageTitle = 'Analytics';
$includeCharts = true;

$db = Database::getInstance();

// Get analytics data
$totalCharts = $db->fetchOne("SELECT COUNT(*) as count FROM music_charts")['count'] ?? 0;
$totalVideos = $db->fetchOne("SELECT COUNT(*) as count FROM videos")['count'] ?? 0;
$totalCountries = $db->fetchOne("SELECT COUNT(DISTINCT country_code) as count FROM music_charts")['count'] ?? 0;
$totalAwards = $db->fetchOne("SELECT COUNT(*) as count FROM awards")['count'] ?? 0;

// Charts by country
$chartsByCountry = $db->fetchAll(
    "SELECT c.country_name, COUNT(mc.id) as count 
     FROM countries c 
     LEFT JOIN music_charts mc ON c.country_code = mc.country_code 
     GROUP BY c.country_code, c.country_name 
     ORDER BY count DESC 
     LIMIT 10"
);

// Recent activity
$recentActivity = $db->fetchAll(
    "SELECT 'music' as type, COUNT(*) as count, MAX(created_at) as last_update FROM music_charts
     UNION ALL
     SELECT 'videos', COUNT(*), MAX(created_at) FROM videos
     UNION ALL
     SELECT 'awards', COUNT(*), MAX(created_at) FROM awards
     UNION ALL
     SELECT 'richest', COUNT(*), MAX(created_at) FROM richest_people"
);

include __DIR__ . '/includes/header.php';
?>

    <main class="main-content">
        <header class="top-bar">
            <div class="top-bar-left">
                <button class="menu-toggle" id="menuToggle">
                    <i class="fa-solid fa-bars"></i>
                </button>
                <h2 class="page-title">Analytics</h2>
            </div>
            <div class="top-bar-right">
                <div class="user-profile">
                    <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($currentUser['email']); ?>&background=random" alt="User" class="profile-img">
                </div>
            </div>
        </header>

        <nav class="breadcrumbs">
            <a href="/admin/dashboard.php">
                <i class="fa-solid fa-house"></i>
                <span>Home</span>
            </a>
            <span class="breadcrumb-separator">
                <i class="fa-solid fa-chevron-right"></i>
            </span>
            <span class="breadcrumb-current">Analytics</span>
        </nav>

        <div class="dashboard-content">
            <section class="stats-section">
                <div class="stat-card">
                    <div class="stat-card-header">
                        <h3>Total Charts</h3>
                        <i class="fa-solid fa-music stat-icon"></i>
                    </div>
                    <div class="stat-card-body">
                        <p class="stat-value"><?php echo number_format($totalCharts); ?></p>
                        <p class="stat-change positive">Music charts</p>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-card-header">
                        <h3>Total Videos</h3>
                        <i class="fa-solid fa-video stat-icon"></i>
                    </div>
                    <div class="stat-card-body">
                        <p class="stat-value"><?php echo number_format($totalVideos); ?></p>
                        <p class="stat-change positive">Video charts</p>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-card-header">
                        <h3>Countries</h3>
                        <i class="fa-solid fa-globe stat-icon"></i>
                    </div>
                    <div class="stat-card-body">
                        <p class="stat-value"><?php echo number_format($totalCountries); ?></p>
                        <p class="stat-change positive">Active countries</p>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-card-header">
                        <h3>Awards</h3>
                        <i class="fa-solid fa-trophy stat-icon"></i>
                    </div>
                    <div class="stat-card-body">
                        <p class="stat-value"><?php echo number_format($totalAwards); ?></p>
                        <p class="stat-change positive">Award entries</p>
                    </div>
                </div>
            </section>

            <section class="additional-cards">
                <div class="info-card">
                    <div class="card-header">
                        <h3>Charts by Country</h3>
                    </div>
                    <div class="card-body">
                        <div style="overflow-x: auto;">
                            <table style="width: 100%; border-collapse: collapse;">
                                <thead>
                                    <tr style="background: rgba(0, 0, 0, 0.05);">
                                        <th style="padding: 1rem; text-align: left;">Country</th>
                                        <th style="padding: 1rem; text-align: left;">Chart Count</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($chartsByCountry as $country): ?>
                                        <tr>
                                            <td style="padding: 1rem; border-bottom: 1px solid var(--glass-border);">
                                                <?php echo htmlspecialchars($country['country_name']); ?>
                                            </td>
                                            <td style="padding: 1rem; border-bottom: 1px solid var(--glass-border);">
                                                <?php echo number_format($country['count']); ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="info-card">
                    <div class="card-header">
                        <h3>Data Status</h3>
                    </div>
                    <div class="card-body">
                        <?php foreach ($recentActivity as $activity): ?>
                            <div class="transaction-item">
                                <div class="transaction-info">
                                    <div class="transaction-avatar"><?php echo strtoupper(substr($activity['type'], 0, 2)); ?></div>
                                    <div>
                                        <p class="transaction-name"><?php echo ucfirst($activity['type']); ?> Data</p>
                                        <p class="transaction-date">Last updated: <?php echo $activity['last_update'] ? date('Y-m-d H:i', strtotime($activity['last_update'])) : 'Never'; ?></p>
                                    </div>
                                </div>
                                <div class="transaction-amount positive"><?php echo number_format($activity['count']); ?> entries</div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </section>
        </div>
    </main>

<?php include __DIR__ . '/includes/footer.php'; ?>

