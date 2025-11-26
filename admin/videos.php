<?php
require_once __DIR__ . '/../bootstrap.php';

$pageTitle = 'Videos Management';
$db = Database::getInstance();
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $security = Security::getInstance();
    $security->requireCSRF();
    
    $action = $_POST['action'] ?? '';
    
    if ($action === 'fetch_all') {
        try {
            $videoService = new VideoApiService();
            $countries = $db->fetchAll("SELECT country_code FROM countries WHERE is_african = 1 ORDER BY priority DESC");
            
            foreach ($countries as $country) {
                $videoService->fetchDataForCountry($country['country_code']);
            }
            $success = 'Videos fetched for all African countries!';
        } catch (Exception $e) {
            $error = 'Error: ' . $e->getMessage();
        }
    }
}

$date = $_GET['date'] ?? date('Y-m-d');
$country = $_GET['country'] ?? 'ZW';

$videos = $db->fetchAll(
    "SELECT * FROM videos WHERE chart_date = :date AND country_code = :country ORDER BY rank ASC",
    ['date' => $date, 'country' => $country]
);

$countries = $db->fetchAll("SELECT country_code, country_name FROM countries ORDER BY is_african DESC, country_name ASC");

include __DIR__ . '/includes/header.php';
?>

    <main class="main-content">
        <header class="top-bar">
            <div class="top-bar-left">
                <button class="menu-toggle" id="menuToggle">
                    <i class="fa-solid fa-bars"></i>
                </button>
                <h2 class="page-title">Videos</h2>
            </div>
            <div class="top-bar-right">
                <form method="POST" style="display: inline;">
                    <input type="hidden" name="action" value="fetch_all">
                    <input type="hidden" name="csrf_token" value="<?php echo Security::getInstance()->generateCSRFToken(); ?>">
                    <button type="submit" class="notification-btn btn-primary">
                        <i class="fa-solid fa-sync"></i> Fetch All Countries
                    </button>
                </form>
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
            <span class="breadcrumb-current">Videos</span>
        </nav>

        <div class="dashboard-content">
            <?php if ($error): ?>
                <div style="background: rgba(255, 0, 0, 0.1); color: #c33; padding: 1rem; border-radius: 8px; margin-bottom: 2rem;">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div style="background: rgba(0, 255, 0, 0.1); color: #0a5; padding: 1rem; border-radius: 8px; margin-bottom: 2rem;">
                    <?php echo htmlspecialchars($success); ?>
                </div>
            <?php endif; ?>

            <section class="additional-cards">
                <div class="info-card">
                    <div class="card-header">
                        <h3>Video Charts</h3>
                        <div style="display: flex; gap: 1rem;">
                            <select id="countryFilter" onchange="window.location.href='?country=' + this.value + '&date=<?php echo $date; ?>'" style="padding: 0.5rem; border: 1px solid #ddd; border-radius: 5px;">
                                <?php foreach ($countries as $c): ?>
                                    <option value="<?php echo htmlspecialchars($c['country_code']); ?>" <?php echo $c['country_code'] === $country ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($c['country_name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <input type="date" id="dateFilter" value="<?php echo $date; ?>" onchange="window.location.href='?country=<?php echo $country; ?>&date=' + this.value" style="padding: 0.5rem; border: 1px solid #ddd; border-radius: 5px;">
                        </div>
                    </div>
                    <div class="card-body">
                        <div style="overflow-x: auto;">
                            <table style="width: 100%; border-collapse: collapse;">
                                <thead>
                                    <tr style="background: rgba(0, 0, 0, 0.05);">
                                        <th style="padding: 1rem; text-align: left;">Rank</th>
                                        <th style="padding: 1rem; text-align: left;">Title</th>
                                        <th style="padding: 1rem; text-align: left;">Artist</th>
                                        <th style="padding: 1rem; text-align: left;">Views</th>
                                        <th style="padding: 1rem; text-align: left;">Likes</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($videos as $video): ?>
                                        <tr>
                                            <td style="padding: 1rem; border-bottom: 1px solid var(--glass-border);"><?php echo $video['rank']; ?></td>
                                            <td style="padding: 1rem; border-bottom: 1px solid var(--glass-border);"><?php echo htmlspecialchars($video['title']); ?></td>
                                            <td style="padding: 1rem; border-bottom: 1px solid var(--glass-border);"><?php echo htmlspecialchars($video['artist']); ?></td>
                                            <td style="padding: 1rem; border-bottom: 1px solid var(--glass-border);"><?php echo number_format($video['views']); ?></td>
                                            <td style="padding: 1rem; border-bottom: 1px solid var(--glass-border);"><?php echo number_format($video['likes']); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                    
                                    <?php if (empty($videos)): ?>
                                        <tr>
                                            <td colspan="5" style="text-align: center; padding: 3rem; color: var(--text-secondary);">
                                                No video data for this country/date. Click "Fetch All Countries" to fetch data.
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </main>

<?php include __DIR__ . '/includes/footer.php'; ?>

