<?php
require_once __DIR__ . '/../bootstrap.php';

$pageTitle = 'Awards Management';
$db = Database::getInstance();
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $security = Security::getInstance();
    $security->requireCSRF();
    
    $action = $_POST['action'] ?? '';
    
    if ($action === 'fetch_awards') {
        try {
            $awardsService = new AwardsApiService();
            $awardsService->fetchData();
            $success = 'Awards data fetched successfully!';
        } catch (Exception $e) {
            $error = 'Error: ' . $e->getMessage();
        }
    } elseif ($action === 'delete') {
        $id = (int)($_POST['id'] ?? 0);
        $db->delete('awards', 'id = :id', ['id' => $id]);
        $success = 'Award deleted successfully!';
    }
}

$year = $_GET['year'] ?? date('Y');
$awards = $db->fetchAll(
    "SELECT * FROM awards WHERE year = :year ORDER BY award_name, category",
    ['year' => $year]
);

$availableYears = $db->fetchAll("SELECT DISTINCT year FROM awards ORDER BY year DESC");

include __DIR__ . '/includes/header.php';
?>

    <main class="main-content">
        <header class="top-bar">
            <div class="top-bar-left">
                <button class="menu-toggle" id="menuToggle">
                    <i class="fa-solid fa-bars"></i>
                </button>
                <h2 class="page-title">Awards</h2>
            </div>
            <div class="top-bar-right">
                <form method="POST" style="display: inline;">
                    <input type="hidden" name="action" value="fetch_awards">
                    <input type="hidden" name="csrf_token" value="<?php echo Security::getInstance()->generateCSRFToken(); ?>">
                    <button type="submit" class="notification-btn btn-primary">
                        <i class="fa-solid fa-sync"></i> Fetch Awards
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
            <span class="breadcrumb-current">Awards</span>
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
                        <h3>Awards (<?php echo $year; ?>)</h3>
                        <select onchange="window.location.href='?year=' + this.value" style="padding: 0.5rem; border: 1px solid #ddd; border-radius: 5px;">
                            <?php foreach ($availableYears as $y): ?>
                                <option value="<?php echo $y['year']; ?>" <?php echo $y['year'] == $year ? 'selected' : ''; ?>>
                                    <?php echo $y['year']; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="card-body">
                        <div style="overflow-x: auto;">
                            <table style="width: 100%; border-collapse: collapse;">
                                <thead>
                                    <tr style="background: rgba(0, 0, 0, 0.05);">
                                        <th style="padding: 1rem; text-align: left;">Award</th>
                                        <th style="padding: 1rem; text-align: left;">Category</th>
                                        <th style="padding: 1rem; text-align: left;">Winner</th>
                                        <th style="padding: 1rem; text-align: left;">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($awards as $award): ?>
                                        <tr>
                                            <td style="padding: 1rem; border-bottom: 1px solid var(--glass-border);"><?php echo htmlspecialchars($award['award_name']); ?></td>
                                            <td style="padding: 1rem; border-bottom: 1px solid var(--glass-border);"><?php echo htmlspecialchars($award['category']); ?></td>
                                            <td style="padding: 1rem; border-bottom: 1px solid var(--glass-border);"><?php echo htmlspecialchars($award['winner']); ?></td>
                                            <td style="padding: 1rem; border-bottom: 1px solid var(--glass-border);">
                                                <form method="POST" style="display: inline;" onsubmit="return confirm('Delete this award?');">
                                                    <input type="hidden" name="action" value="delete">
                                                    <input type="hidden" name="csrf_token" value="<?php echo Security::getInstance()->generateCSRFToken(); ?>">
                                                    <input type="hidden" name="id" value="<?php echo $award['id']; ?>">
                                                    <button type="submit" class="btn-outline-danger btn-sm">
                                                        <i class="fa-solid fa-trash"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                    
                                    <?php if (empty($awards)): ?>
                                        <tr>
                                            <td colspan="4" style="text-align: center; padding: 3rem; color: var(--text-secondary);">
                                                No awards data for <?php echo $year; ?>. Click "Fetch Awards" to fetch data.
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

