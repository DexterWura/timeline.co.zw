<?php
require_once __DIR__ . '/../bootstrap.php';

$pageTitle = 'Hall of Fame Management';
$db = Database::getInstance();
$auth = new Auth();
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'create') {
        $artistName = $_POST['artist_name'] ?? '';
        $category = $_POST['category'] ?? '';
        $yearInducted = $_POST['year_inducted'] ?? date('Y');
        $description = $_POST['description'] ?? '';
        $achievements = $_POST['achievements'] ?? '';
        $countryCode = $_POST['country_code'] ?? '';
        
        if (empty($artistName)) {
            $error = 'Artist name is required';
        } else {
            $db->insert('hall_of_fame', [
                'artist_name' => $artistName,
                'category' => $category,
                'year_inducted' => $yearInducted,
                'description' => $description,
                'achievements' => $achievements,
                'country_code' => $countryCode ?: null
            ]);
            $success = 'Hall of Fame entry created successfully!';
        }
    } elseif ($action === 'delete') {
        $id = $_POST['id'] ?? 0;
        $db->delete('hall_of_fame', 'id = :id', ['id' => $id]);
        $success = 'Entry deleted successfully!';
    }
}

$hallOfFame = $db->fetchAll("SELECT * FROM hall_of_fame ORDER BY year_inducted DESC, artist_name ASC");
$countries = $db->fetchAll("SELECT country_code, country_name FROM countries ORDER BY country_name");

include __DIR__ . '/includes/header.php';
?>

    <main class="main-content">
        <header class="top-bar">
            <div class="top-bar-left">
                <button class="menu-toggle" id="menuToggle">
                    <i class="fa-solid fa-bars"></i>
                </button>
                <h2 class="page-title">Hall of Fame</h2>
            </div>
            <div class="top-bar-right">
                <button class="notification-btn btn-primary" onclick="document.getElementById('newEntryModal').style.display='block'">
                    <i class="fa-solid fa-plus"></i> New Entry
                </button>
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
            <span class="breadcrumb-current">Hall of Fame</span>
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
                        <h3>Hall of Fame Entries</h3>
                    </div>
                    <div class="card-body">
                        <div style="overflow-x: auto;">
                            <table style="width: 100%; border-collapse: collapse;">
                                <thead>
                                    <tr style="background: rgba(0, 0, 0, 0.05);">
                                        <th style="padding: 1rem; text-align: left;">Artist</th>
                                        <th style="padding: 1rem; text-align: left;">Category</th>
                                        <th style="padding: 1rem; text-align: left;">Year</th>
                                        <th style="padding: 1rem; text-align: left;">Country</th>
                                        <th style="padding: 1rem; text-align: left;">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($hallOfFame as $entry): ?>
                                        <tr>
                                            <td style="padding: 1rem; border-bottom: 1px solid var(--glass-border);">
                                                <strong><?php echo htmlspecialchars($entry['artist_name']); ?></strong>
                                            </td>
                                            <td style="padding: 1rem; border-bottom: 1px solid var(--glass-border);">
                                                <?php echo htmlspecialchars($entry['category'] ?: 'General'); ?>
                                            </td>
                                            <td style="padding: 1rem; border-bottom: 1px solid var(--glass-border);">
                                                <?php echo $entry['year_inducted'] ?: '-'; ?>
                                            </td>
                                            <td style="padding: 1rem; border-bottom: 1px solid var(--glass-border);">
                                                <?php 
                                                if ($entry['country_code']) {
                                                    $geo = new Geolocation();
                                                    echo htmlspecialchars($geo->getCountryName($entry['country_code']));
                                                } else {
                                                    echo 'Global';
                                                }
                                                ?>
                                            </td>
                                            <td style="padding: 1rem; border-bottom: 1px solid var(--glass-border);">
                                                <form method="POST" style="display: inline;" onsubmit="return confirm('Delete this entry?');">
                                                    <input type="hidden" name="action" value="delete">
                                                    <input type="hidden" name="id" value="<?php echo $entry['id']; ?>">
                                                    <button type="submit" class="btn-outline-danger btn-sm">
                                                        <i class="fa-solid fa-trash"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                    
                                    <?php if (empty($hallOfFame)): ?>
                                        <tr>
                                            <td colspan="5" style="text-align: center; padding: 3rem; color: var(--text-secondary);">
                                                No Hall of Fame entries yet. Click "New Entry" to add one.
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

    <!-- New Entry Modal -->
    <div id="newEntryModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; padding: 2rem;">
        <div style="background: white; max-width: 600px; margin: 0 auto; border-radius: 10px; padding: 2rem; max-height: 90vh; overflow-y: auto;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
                <h2>New Hall of Fame Entry</h2>
                <button onclick="document.getElementById('newEntryModal').style.display='none'" style="background: none; border: none; font-size: 1.5rem; cursor: pointer;">&times;</button>
            </div>
            <form method="POST">
                <input type="hidden" name="action" value="create">
                <div style="margin-bottom: 1.5rem;">
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Artist Name *</label>
                    <input type="text" name="artist_name" required style="width: 100%; padding: 0.75rem; border: 1px solid #ddd; border-radius: 5px;">
                </div>
                <div style="margin-bottom: 1.5rem;">
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Category</label>
                    <input type="text" name="category" placeholder="e.g., Rock, Pop, Hip-Hop" style="width: 100%; padding: 0.75rem; border: 1px solid #ddd; border-radius: 5px;">
                </div>
                <div style="margin-bottom: 1.5rem;">
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Year Inducted</label>
                    <input type="number" name="year_inducted" value="<?php echo date('Y'); ?>" min="1900" max="<?php echo date('Y'); ?>" style="width: 100%; padding: 0.75rem; border: 1px solid #ddd; border-radius: 5px;">
                </div>
                <div style="margin-bottom: 1.5rem;">
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Country</label>
                    <select name="country_code" style="width: 100%; padding: 0.75rem; border: 1px solid #ddd; border-radius: 5px;">
                        <option value="">Global</option>
                        <?php foreach ($countries as $country): ?>
                            <option value="<?php echo htmlspecialchars($country['country_code']); ?>">
                                <?php echo htmlspecialchars($country['country_name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div style="margin-bottom: 1.5rem;">
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Description</label>
                    <textarea name="description" rows="4" style="width: 100%; padding: 0.75rem; border: 1px solid #ddd; border-radius: 5px;"></textarea>
                </div>
                <div style="margin-bottom: 1.5rem;">
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Achievements</label>
                    <textarea name="achievements" rows="4" style="width: 100%; padding: 0.75rem; border: 1px solid #ddd; border-radius: 5px;"></textarea>
                </div>
                <div style="display: flex; gap: 1rem;">
                    <button type="submit" style="padding: 0.75rem 2rem; background: var(--primary-color); color: white; border: none; border-radius: 5px; cursor: pointer;">Create Entry</button>
                    <button type="button" onclick="document.getElementById('newEntryModal').style.display='none'" style="padding: 0.75rem 2rem; background: #ccc; color: white; border: none; border-radius: 5px; cursor: pointer;">Cancel</button>
                </div>
            </form>
        </div>
    </div>

<?php include __DIR__ . '/includes/footer.php'; ?>

