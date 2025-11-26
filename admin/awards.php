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
    
    if ($action === 'create') {
        $awardName = trim($_POST['award_name'] ?? '');
        $category = trim($_POST['category'] ?? '');
        $year = (int)($_POST['year'] ?? date('Y'));
        $winner = trim($_POST['winner'] ?? '');
        $nominees = $_POST['nominees'] ?? '';
        $description = trim($_POST['description'] ?? '');
        $source = trim($_POST['source'] ?? '');
        $sourceUrl = trim($_POST['source_url'] ?? '');
        
        if (empty($awardName)) {
            $error = 'Award name is required';
        } elseif (empty($year) || $year < 1900 || $year > date('Y')) {
            $error = 'Valid year is required';
        } else {
            // Parse nominees if it's a textarea (comma or newline separated)
            $nomineesArray = [];
            if (!empty($nominees)) {
                $nomineesList = preg_split('/[,\n\r]+/', $nominees);
                $nomineesArray = array_map('trim', array_filter($nomineesList));
            }
            
            $db->insert('awards', [
                'award_name' => $awardName,
                'category' => $category ?: null,
                'year' => $year,
                'winner' => $winner ?: null,
                'nominees' => !empty($nomineesArray) ? json_encode($nomineesArray) : null,
                'description' => $description ?: null,
                'source' => $source ?: null,
                'source_url' => $sourceUrl ?: null
            ]);
            $success = 'Award created successfully!';
        }
    } elseif ($action === 'fetch_awards') {
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
    } elseif ($action === 'edit') {
        $id = (int)($_POST['id'] ?? 0);
        $awardName = trim($_POST['award_name'] ?? '');
        $category = trim($_POST['category'] ?? '');
        $year = (int)($_POST['year'] ?? date('Y'));
        $winner = trim($_POST['winner'] ?? '');
        $nominees = $_POST['nominees'] ?? '';
        $description = trim($_POST['description'] ?? '');
        $source = trim($_POST['source'] ?? '');
        $sourceUrl = trim($_POST['source_url'] ?? '');
        
        if (empty($awardName)) {
            $error = 'Award name is required';
        } elseif (empty($year) || $year < 1900 || $year > date('Y')) {
            $error = 'Valid year is required';
        } else {
            $nomineesArray = [];
            if (!empty($nominees)) {
                $nomineesList = preg_split('/[,\n\r]+/', $nominees);
                $nomineesArray = array_map('trim', array_filter($nomineesList));
            }
            
            $db->update('awards', [
                'award_name' => $awardName,
                'category' => $category ?: null,
                'year' => $year,
                'winner' => $winner ?: null,
                'nominees' => !empty($nomineesArray) ? json_encode($nomineesArray) : null,
                'description' => $description ?: null,
                'source' => $source ?: null,
                'source_url' => $sourceUrl ?: null
            ], 'id = :id', ['id' => $id]);
            $success = 'Award updated successfully!';
        }
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
                <button class="notification-btn btn-primary" onclick="document.getElementById('newAwardModal').style.display='block'" style="margin-right: 1rem;">
                    <i class="fa-solid fa-plus"></i> New Award
                </button>
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
                                                <div style="display: flex; gap: 0.5rem;">
                                                    <button onclick="editAward(<?php echo htmlspecialchars(json_encode($award)); ?>)" class="btn-outline-primary btn-sm" style="padding: 0.5rem; background: rgba(0, 123, 255, 0.1); border: 1px solid rgba(0, 123, 255, 0.3); border-radius: 4px; cursor: pointer;">
                                                        <i class="fa-solid fa-edit"></i>
                                                    </button>
                                                    <form method="POST" style="display: inline;" onsubmit="return confirm('Delete this award?');">
                                                        <input type="hidden" name="action" value="delete">
                                                        <input type="hidden" name="csrf_token" value="<?php echo Security::getInstance()->generateCSRFToken(); ?>">
                                                        <input type="hidden" name="id" value="<?php echo $award['id']; ?>">
                                                        <button type="submit" class="btn-outline-danger btn-sm" style="padding: 0.5rem; background: rgba(220, 53, 69, 0.1); border: 1px solid rgba(220, 53, 69, 0.3); border-radius: 4px; cursor: pointer;">
                                                            <i class="fa-solid fa-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
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

    <!-- New Award Modal -->
    <div id="newAwardModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; padding: 2rem; overflow-y: auto;">
        <div style="background: white; max-width: 700px; margin: 2rem auto; border-radius: 10px; padding: 2rem; max-height: 90vh; overflow-y: auto;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
                <h2 id="modalTitle">New Award</h2>
                <button onclick="closeModal()" style="background: none; border: none; font-size: 1.5rem; cursor: pointer; color: #666;">&times;</button>
            </div>
            <form method="POST" id="awardForm">
                <input type="hidden" name="action" id="formAction" value="create">
                <input type="hidden" name="csrf_token" value="<?php echo Security::getInstance()->generateCSRFToken(); ?>">
                <input type="hidden" name="id" id="awardId">
                
                <div style="display: grid; gap: 1.5rem;">
                    <div>
                        <label style="display: block; margin-bottom: 0.5rem; font-weight: 500; color: var(--text-primary);">
                            Award Name <span style="color: #e74c3c;">*</span>
                        </label>
                        <input type="text" name="award_name" id="award_name" required placeholder="e.g., Grammy Awards, MTV Music Awards" style="width: 100%; padding: 0.75rem; border: 1px solid #ddd; border-radius: 5px; font-size: 0.95rem;">
                    </div>
                    
                    <div>
                        <label style="display: block; margin-bottom: 0.5rem; font-weight: 500; color: var(--text-primary);">
                            Category
                        </label>
                        <input type="text" name="category" id="category" placeholder="e.g., Best Album, Song of the Year, Artist of the Year" style="width: 100%; padding: 0.75rem; border: 1px solid #ddd; border-radius: 5px; font-size: 0.95rem;">
                    </div>
                    
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                        <div>
                            <label style="display: block; margin-bottom: 0.5rem; font-weight: 500; color: var(--text-primary);">
                                Year <span style="color: #e74c3c;">*</span>
                            </label>
                            <input type="number" name="year" id="year" required min="1900" max="<?php echo date('Y'); ?>" value="<?php echo date('Y'); ?>" style="width: 100%; padding: 0.75rem; border: 1px solid #ddd; border-radius: 5px; font-size: 0.95rem;">
                        </div>
                        
                        <div>
                            <label style="display: block; margin-bottom: 0.5rem; font-weight: 500; color: var(--text-primary);">
                                Winner
                            </label>
                            <input type="text" name="winner" id="winner" placeholder="Winner name" style="width: 100%; padding: 0.75rem; border: 1px solid #ddd; border-radius: 5px; font-size: 0.95rem;">
                        </div>
                    </div>
                    
                    <div>
                        <label style="display: block; margin-bottom: 0.5rem; font-weight: 500; color: var(--text-primary);">
                            Nominees (one per line or comma-separated)
                        </label>
                        <textarea name="nominees" id="nominees" rows="4" placeholder="Enter nominees, one per line or separated by commas" style="width: 100%; padding: 0.75rem; border: 1px solid #ddd; border-radius: 5px; font-size: 0.95rem; resize: vertical;"></textarea>
                    </div>
                    
                    <div>
                        <label style="display: block; margin-bottom: 0.5rem; font-weight: 500; color: var(--text-primary);">
                            Description
                        </label>
                        <textarea name="description" id="description" rows="3" placeholder="Award description or additional details" style="width: 100%; padding: 0.75rem; border: 1px solid #ddd; border-radius: 5px; font-size: 0.95rem; resize: vertical;"></textarea>
                    </div>
                    
                    <div>
                        <label style="display: block; margin-bottom: 0.5rem; font-weight: 500; color: var(--text-primary);">
                            Source
                        </label>
                        <input type="text" name="source" id="source" placeholder="e.g., Grammy.com, MTV" style="width: 100%; padding: 0.75rem; border: 1px solid #ddd; border-radius: 5px; font-size: 0.95rem;">
                    </div>
                    
                    <div>
                        <label style="display: block; margin-bottom: 0.5rem; font-weight: 500; color: var(--text-primary);">
                            Source URL
                        </label>
                        <input type="url" name="source_url" id="source_url" placeholder="https://..." style="width: 100%; padding: 0.75rem; border: 1px solid #ddd; border-radius: 5px; font-size: 0.95rem;">
                    </div>
                    
                    <div style="display: flex; gap: 1rem; margin-top: 1rem;">
                        <button type="submit" style="flex: 1; padding: 0.875rem 2rem; background: var(--primary-color); color: white; border: none; border-radius: 5px; cursor: pointer; font-weight: 500;">
                            <span id="submitText">Create Award</span>
                        </button>
                        <button type="button" onclick="closeModal()" style="padding: 0.875rem 2rem; background: #ccc; color: white; border: none; border-radius: 5px; cursor: pointer; font-weight: 500;">
                            Cancel
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

<script>
function closeModal() {
    document.getElementById('newAwardModal').style.display = 'none';
    document.getElementById('awardForm').reset();
    document.getElementById('formAction').value = 'create';
    document.getElementById('awardId').value = '';
    document.getElementById('modalTitle').textContent = 'New Award';
    document.getElementById('submitText').textContent = 'Create Award';
}

function editAward(award) {
    document.getElementById('newAwardModal').style.display = 'block';
    document.getElementById('formAction').value = 'edit';
    document.getElementById('awardId').value = award.id;
    document.getElementById('award_name').value = award.award_name || '';
    document.getElementById('category').value = award.category || '';
    document.getElementById('year').value = award.year || '';
    document.getElementById('winner').value = award.winner || '';
    document.getElementById('description').value = award.description || '';
    document.getElementById('source').value = award.source || '';
    document.getElementById('source_url').value = award.source_url || '';
    
    // Parse nominees from JSON
    let nominees = '';
    if (award.nominees) {
        try {
            const nomineesArray = JSON.parse(award.nominees);
            if (Array.isArray(nomineesArray)) {
                nominees = nomineesArray.join('\n');
            }
        } catch (e) {
            nominees = award.nominees;
        }
    }
    document.getElementById('nominees').value = nominees;
    
    document.getElementById('modalTitle').textContent = 'Edit Award';
    document.getElementById('submitText').textContent = 'Update Award';
}

// Close modal when clicking outside
window.onclick = function(event) {
    const modal = document.getElementById('newAwardModal');
    if (event.target == modal) {
        closeModal();
    }
}
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>

