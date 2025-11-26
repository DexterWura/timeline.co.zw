<?php
require_once __DIR__ . '/../bootstrap.php';

$pageTitle = 'Database Migrations';
$migration = new Migration();
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'run_migrations') {
    try {
        $migration->runMigrations();
        $success = 'Migrations executed successfully!';
    } catch (Exception $e) {
        $error = 'Migration error: ' . $e->getMessage();
    }
}

$db = Database::getInstance();
$executedMigrations = $db->fetchAll("SELECT * FROM migrations ORDER BY version DESC");
$migrationFiles = $migration->getMigrationFiles();

// Get pending migrations
$executedVersions = array_column($executedMigrations, 'version');
$pendingMigrations = [];
foreach ($migrationFiles as $file) {
    $version = basename($file, '.php');
    if (!in_array($version, $executedVersions)) {
        $pendingMigrations[] = $version;
    }
}

include __DIR__ . '/includes/header.php';
?>

    <!-- Main Content -->
    <main class="main-content">
        <header class="top-bar">
            <div class="top-bar-left">
                <button class="menu-toggle" id="menuToggle">
                    <i class="fa-solid fa-bars"></i>
                </button>
                <h2 class="page-title">Database Migrations</h2>
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
            <a href="/admin/dashboard.php">Dashboard</a>
            <span class="breadcrumb-separator">
                <i class="fa-solid fa-chevron-right"></i>
            </span>
            <span class="breadcrumb-current">Migrations</span>
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

            <section class="stats-section">
                <div class="stat-card">
                    <div class="stat-card-header">
                        <h3>Total Migrations</h3>
                        <i class="fa-solid fa-database stat-icon"></i>
                    </div>
                    <div class="stat-card-body">
                        <p class="stat-value"><?php echo count($migrationFiles); ?></p>
                        <p class="stat-change positive">Available</p>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-card-header">
                        <h3>Executed</h3>
                        <i class="fa-solid fa-check-circle stat-icon"></i>
                    </div>
                    <div class="stat-card-body">
                        <p class="stat-value"><?php echo count($executedMigrations); ?></p>
                        <p class="stat-change positive">Completed</p>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-card-header">
                        <h3>Pending</h3>
                        <i class="fa-solid fa-clock stat-icon"></i>
                    </div>
                    <div class="stat-card-body">
                        <p class="stat-value"><?php echo count($pendingMigrations); ?></p>
                        <p class="stat-change <?php echo count($pendingMigrations) > 0 ? 'negative' : 'positive'; ?>">
                            <?php echo count($pendingMigrations) > 0 ? 'Need attention' : 'Up to date'; ?>
                        </p>
                    </div>
                </div>
            </section>

            <section class="additional-cards">
                <div class="info-card">
                    <div class="card-header">
                        <h3>Migration Status</h3>
                        <?php if (count($pendingMigrations) > 0): ?>
                            <form method="POST" style="display: inline;">
                                <input type="hidden" name="action" value="run_migrations">
                                <button type="submit" style="padding: 0.5rem 1rem; background: var(--primary-color); color: white; border: none; border-radius: 5px; cursor: pointer;">
                                    <i class="fa-solid fa-play"></i> Run Pending Migrations
                                </button>
                            </form>
                        <?php endif; ?>
                    </div>
                    <div class="card-body">
                        <div style="overflow-x: auto;">
                            <table style="width: 100%; border-collapse: collapse;">
                                <thead>
                                    <tr style="background: rgba(0, 0, 0, 0.05);">
                                        <th style="padding: 1rem; text-align: left; border-bottom: 1px solid var(--glass-border);">Version</th>
                                        <th style="padding: 1rem; text-align: left; border-bottom: 1px solid var(--glass-border);">Description</th>
                                        <th style="padding: 1rem; text-align: left; border-bottom: 1px solid var(--glass-border);">Status</th>
                                        <th style="padding: 1rem; text-align: left; border-bottom: 1px solid var(--glass-border);">Executed At</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($migrationFiles as $file): ?>
                                        <?php
                                        $version = basename($file, '.php');
                                        $executed = in_array($version, $executedVersions);
                                        $migrationInfo = null;
                                        if ($executed) {
                                            foreach ($executedMigrations as $m) {
                                                if ($m['version'] === $version) {
                                                    $migrationInfo = $m;
                                                    break;
                                                }
                                            }
                                        }
                                        ?>
                                        <tr>
                                            <td style="padding: 1rem; border-bottom: 1px solid var(--glass-border);">
                                                <code><?php echo htmlspecialchars($version); ?></code>
                                            </td>
                                            <td style="padding: 1rem; border-bottom: 1px solid var(--glass-border);">
                                                <?php echo htmlspecialchars($migrationInfo['description'] ?? 'Migration'); ?>
                                            </td>
                                            <td style="padding: 1rem; border-bottom: 1px solid var(--glass-border);">
                                                <?php if ($executed): ?>
                                                    <span style="color: #0a5; font-weight: 600;">
                                                        <i class="fa-solid fa-check-circle"></i> Executed
                                                    </span>
                                                <?php else: ?>
                                                    <span style="color: #f90; font-weight: 600;">
                                                        <i class="fa-solid fa-clock"></i> Pending
                                                    </span>
                                                <?php endif; ?>
                                            </td>
                                            <td style="padding: 1rem; border-bottom: 1px solid var(--glass-border);">
                                                <?php echo $migrationInfo ? date('Y-m-d H:i:s', strtotime($migrationInfo['executed_at'])) : '-'; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </main>

<?php include __DIR__ . '/includes/footer.php'; ?>

