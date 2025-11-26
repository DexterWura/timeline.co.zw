<?php
require_once __DIR__ . '/../bootstrap.php';

$pageTitle = 'Richest People Management';
$db = Database::getInstance();
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $security = Security::getInstance();
    $security->requireCSRF();
    
    $action = $_POST['action'] ?? '';
    
    if ($action === 'fetch_richest') {
        $countryCode = $_POST['country'] ?? 'US';
        try {
            $richestService = new RichestApiService();
            $richestService->fetchData($countryCode);
            $geo = new Geolocation();
            $success = 'Richest people data fetched for ' . $geo->getCountryName($countryCode) . '!';
        } catch (Exception $e) {
            $error = 'Error: ' . $e->getMessage();
        }
    } elseif ($action === 'delete') {
        $id = (int)($_POST['id'] ?? 0);
        $db->delete('richest_people', 'id = :id', ['id' => $id]);
        $success = 'Entry deleted successfully!';
    }
}

$date = $_GET['date'] ?? date('Y-m-d');
$country = $_GET['country'] ?? 'US';

$richest = $db->fetchAll(
    "SELECT * FROM richest_people WHERE chart_date = :date AND country_code = :country ORDER BY rank ASC",
    ['date' => $date, 'country' => $country]
);

$countries = $db->fetchAll("SELECT country_code, country_name FROM countries ORDER BY country_name ASC");

include __DIR__ . '/includes/header.php';
?>

    <main class="main-content">
        <header class="top-bar">
            <div class="top-bar-left">
                <button class="menu-toggle" id="menuToggle">
                    <i class="fa-solid fa-bars"></i>
                </button>
                <h2 class="page-title">Richest People</h2>
            </div>
            <div class="top-bar-right">
                <form method="POST" style="display: inline;">
                    <input type="hidden" name="action" value="fetch_richest">
                    <input type="hidden" name="csrf_token" value="<?php echo Security::getInstance()->generateCSRFToken(); ?>">
                    <select name="country" style="padding: 0.5rem; border: 1px solid #ddd; border-radius: 5px; margin-right: 0.5rem;">
                        <?php foreach ($countries as $c): ?>
                            <option value="<?php echo htmlspecialchars($c['country_code']); ?>">
                                <?php echo htmlspecialchars($c['country_name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <button type="submit" class="notification-btn btn-primary">
                        <i class="fa-solid fa-sync"></i> Fetch
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
            <span class="breadcrumb-current">Richest People</span>
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
                        <h3>Richest People</h3>
                        <div style="display: flex; gap: 1rem;">
                            <select onchange="window.location.href='?country=' + this.value + '&date=<?php echo $date; ?>'" style="padding: 0.5rem; border: 1px solid #ddd; border-radius: 5px;">
                                <?php foreach ($countries as $c): ?>
                                    <option value="<?php echo htmlspecialchars($c['country_code']); ?>" <?php echo $c['country_code'] === $country ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($c['country_name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <input type="date" value="<?php echo $date; ?>" onchange="window.location.href='?country=<?php echo $country; ?>&date=' + this.value" style="padding: 0.5rem; border: 1px solid #ddd; border-radius: 5px;">
                        </div>
                    </div>
                    <div class="card-body">
                        <div style="overflow-x: auto;">
                            <table style="width: 100%; border-collapse: collapse;">
                                <thead>
                                    <tr style="background: rgba(0, 0, 0, 0.05);">
                                        <th style="padding: 1rem; text-align: left;">Rank</th>
                                        <th style="padding: 1rem; text-align: left;">Name</th>
                                        <th style="padding: 1rem; text-align: left;">Net Worth</th>
                                        <th style="padding: 1rem; text-align: left;">Source</th>
                                        <th style="padding: 1rem; text-align: left;">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($richest as $person): ?>
                                        <tr>
                                            <td style="padding: 1rem; border-bottom: 1px solid var(--glass-border);"><?php echo $person['rank']; ?></td>
                                            <td style="padding: 1rem; border-bottom: 1px solid var(--glass-border);"><?php echo htmlspecialchars($person['name']); ?></td>
                                            <td style="padding: 1rem; border-bottom: 1px solid var(--glass-border);">
                                                $<?php 
                                                $netWorth = $person['net_worth'];
                                                if ($netWorth >= 1000000000) {
                                                    echo number_format($netWorth / 1000000000, 1) . 'B';
                                                } else {
                                                    echo number_format($netWorth / 1000000, 1) . 'M';
                                                }
                                                ?>
                                            </td>
                                            <td style="padding: 1rem; border-bottom: 1px solid var(--glass-border);"><?php echo htmlspecialchars($person['source']); ?></td>
                                            <td style="padding: 1rem; border-bottom: 1px solid var(--glass-border);">
                                                <form method="POST" style="display: inline;" onsubmit="return confirm('Delete this entry?');">
                                                    <input type="hidden" name="action" value="delete">
                                                    <input type="hidden" name="csrf_token" value="<?php echo Security::getInstance()->generateCSRFToken(); ?>">
                                                    <input type="hidden" name="id" value="<?php echo $person['id']; ?>">
                                                    <button type="submit" class="btn-outline-danger btn-sm">
                                                        <i class="fa-solid fa-trash"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                    
                                    <?php if (empty($richest)): ?>
                                        <tr>
                                            <td colspan="5" style="text-align: center; padding: 3rem; color: var(--text-secondary);">
                                                No data for this country/date. Use the form above to fetch data.
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

