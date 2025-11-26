<?php
require_once __DIR__ . '/../bootstrap.php';

$pageTitle = 'Page Content Management';
$db = Database::getInstance();
$auth = new Auth();
$auth->requireAdmin();

$pageContent = new PageContent();
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $security = Security::getInstance();
    $security->requireCSRF();
    
    $action = $_POST['action'] ?? '';
    
    if ($action === 'save') {
        $pageKey = trim($_POST['page_key'] ?? '');
        $pageTitle = trim($_POST['page_title'] ?? '');
        $content = $_POST['content'] ?? '';
        $metaDescription = trim($_POST['meta_description'] ?? '');
        $metaKeywords = trim($_POST['meta_keywords'] ?? '');
        $isActive = isset($_POST['is_active']) ? 1 : 0;
        
        if (empty($pageKey) || empty($pageTitle)) {
            $error = 'Page key and title are required';
        } else {
            try {
                $pageContent->createOrUpdate($pageKey, $pageTitle, $content, $metaDescription, $metaKeywords, $auth->getUserId());
                // Update active status
                $pageContent->toggleActive($pageKey, $isActive);
                $success = 'Page content saved successfully!';
            } catch (Exception $e) {
                $error = 'Error: ' . $e->getMessage();
            }
        }
    } elseif ($action === 'toggle_active') {
        $pageKey = $_POST['page_key'] ?? '';
        $isActive = isset($_POST['is_active']) ? 1 : 0;
        $pageContent->toggleActive($pageKey, $isActive);
        $success = 'Page status updated!';
    } elseif ($action === 'delete') {
        $pageKey = $_POST['page_key'] ?? '';
        $pageContent->delete($pageKey);
        $success = 'Page deleted successfully!';
    }
}

$pages = $pageContent->getAllPages();
$editingPage = null;
if (isset($_GET['edit'])) {
    if ($_GET['edit'] === 'new') {
        $editingPage = ['page_key' => 'new', 'page_title' => '', 'content' => '', 'meta_description' => '', 'meta_keywords' => '', 'is_active' => 1];
    } else {
        // Get page without active check for editing
        $editingPage = $db->fetchOne(
            "SELECT * FROM page_content WHERE page_key = :key",
            ['key' => $_GET['edit']]
        );
        if (!$editingPage) {
            $editingPage = ['page_key' => $_GET['edit'], 'page_title' => '', 'content' => '', 'meta_description' => '', 'meta_keywords' => '', 'is_active' => 1];
        }
    }
}

include __DIR__ . '/includes/header.php';
?>

<main class="main-content">
    <header class="top-bar">
        <div class="top-bar-left">
            <button class="menu-toggle" id="menuToggle">
                <i class="fa-solid fa-bars"></i>
            </button>
            <h2 class="page-title">Page Content</h2>
        </div>
        <div class="top-bar-right">
            <?php if (!$editingPage): ?>
                <a href="?edit=new" class="notification-btn btn-primary">
                    <i class="fa-solid fa-plus"></i> New Page
                </a>
            <?php endif; ?>
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
        <span class="breadcrumb-current">Page Content</span>
    </nav>

    <div class="dashboard-content">
        <?php if ($error): ?>
            <div class="alert alert-danger">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="alert alert-success">
                <?php echo htmlspecialchars($success); ?>
            </div>
        <?php endif; ?>

        <?php if ($editingPage): ?>
            <!-- Edit Page Form -->
            <section class="additional-cards">
                <div class="info-card">
                    <div class="card-header">
                        <h3><?php echo $editingPage['page_key'] === 'new' ? 'Create New Page' : 'Edit Page: ' . htmlspecialchars($editingPage['page_title']); ?></h3>
                        <a href="?" class="btn-outline-secondary btn-sm">
                            <i class="fa-solid fa-arrow-left"></i> Back
                        </a>
                    </div>
                    <div class="card-body">
                        <form method="POST">
                            <input type="hidden" name="action" value="save">
                            <input type="hidden" name="csrf_token" value="<?php echo Security::getInstance()->generateCSRFToken(); ?>">
                            
                            <div style="display: grid; gap: 1.5rem;">
                                <div>
                                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Page Key *</label>
                                    <input type="text" name="page_key" value="<?php echo htmlspecialchars($editingPage['page_key']); ?>" required 
                                           pattern="[a-z0-9-]+" placeholder="e.g., terms, privacy, about"
                                           <?php echo $editingPage['page_key'] !== 'new' ? 'readonly' : ''; ?>
                                           style="width: 100%; padding: 0.75rem; border: 1px solid var(--glass-border); border-radius: 8px;">
                                    <small style="color: var(--text-tertiary); font-size: 0.85rem;">URL-friendly identifier (lowercase, numbers, hyphens only)</small>
                                </div>
                                
                                <div>
                                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Page Title *</label>
                                    <input type="text" name="page_title" value="<?php echo htmlspecialchars($editingPage['page_title']); ?>" required
                                           style="width: 100%; padding: 0.75rem; border: 1px solid var(--glass-border); border-radius: 8px;">
                                </div>
                                
                                <div>
                                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Meta Description</label>
                                    <textarea name="meta_description" rows="2"
                                              style="width: 100%; padding: 0.75rem; border: 1px solid var(--glass-border); border-radius: 8px;"><?php echo htmlspecialchars($editingPage['meta_description'] ?? ''); ?></textarea>
                                </div>
                                
                                <div>
                                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Meta Keywords</label>
                                    <input type="text" name="meta_keywords" value="<?php echo htmlspecialchars($editingPage['meta_keywords'] ?? ''); ?>"
                                           placeholder="keyword1, keyword2, keyword3"
                                           style="width: 100%; padding: 0.75rem; border: 1px solid var(--glass-border); border-radius: 8px;">
                                </div>
                                
                                <div>
                                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Content *</label>
                                    <textarea name="content" rows="20" required id="pageContentEditor"
                                              style="width: 100%; padding: 0.75rem; border: 1px solid var(--glass-border); border-radius: 8px; font-family: monospace;"><?php echo htmlspecialchars($editingPage['content'] ?? ''); ?></textarea>
                                    <small style="color: var(--text-tertiary); font-size: 0.85rem;">HTML content is allowed. Use &lt;h2&gt; for headings, &lt;p&gt; for paragraphs, &lt;ul&gt; and &lt;li&gt; for lists.</small>
                                </div>
                                
                                <?php if (isset($editingPage['is_active'])): ?>
                                <div>
                                    <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                                        <input type="checkbox" name="is_active" value="1" <?php echo ($editingPage['is_active'] ?? 1) ? 'checked' : ''; ?>>
                                        <span>Page is active (visible on frontend)</span>
                                    </label>
                                </div>
                                <?php endif; ?>
                                
                                <div style="display: flex; gap: 1rem;">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fa-solid fa-save"></i> Save Page
                                    </button>
                                    <a href="?" class="btn btn-secondary">Cancel</a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </section>
        <?php else: ?>
            <!-- Pages List -->
            <section class="additional-cards">
                <div class="info-card">
                    <div class="card-header">
                        <h3>Manage Pages</h3>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="data-table">
                                <thead>
                                    <tr>
                                        <th>Page Key</th>
                                        <th>Title</th>
                                        <th>Status</th>
                                        <th>Last Updated</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($pages as $page): ?>
                                        <tr>
                                            <td><code><?php echo htmlspecialchars($page['page_key']); ?></code></td>
                                            <td><?php echo htmlspecialchars($page['page_title']); ?></td>
                                            <td>
                                                <form method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to <?php echo $page['is_active'] ? 'disable' : 'enable'; ?> this page?');">
                                                    <input type="hidden" name="action" value="toggle_active">
                                                    <input type="hidden" name="csrf_token" value="<?php echo Security::getInstance()->generateCSRFToken(); ?>">
                                                    <input type="hidden" name="page_key" value="<?php echo $page['page_key']; ?>">
                                                    <input type="hidden" name="is_active" value="<?php echo $page['is_active'] ? 0 : 1; ?>">
                                                    <button type="submit" class="btn-outline-<?php echo $page['is_active'] ? 'success' : 'danger'; ?> btn-sm" title="<?php echo $page['is_active'] ? 'Click to disable page' : 'Click to enable page'; ?>">
                                                        <i class="fa-solid fa-<?php echo $page['is_active'] ? 'check-circle' : 'times-circle'; ?>"></i>
                                                        <?php echo $page['is_active'] ? 'Enabled' : 'Disabled'; ?>
                                                    </button>
                                                </form>
                                            </td>
                                            <td><?php echo $page['updated_at'] ? date('M d, Y', strtotime($page['updated_at'])) : 'Never'; ?></td>
                                            <td>
                                                <a href="?edit=<?php echo urlencode($page['page_key']); ?>" class="btn-outline-primary btn-sm">
                                                    <i class="fa-solid fa-edit"></i> Edit
                                                </a>
                                                <form method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this page?');">
                                                    <input type="hidden" name="action" value="delete">
                                                    <input type="hidden" name="csrf_token" value="<?php echo Security::getInstance()->generateCSRFToken(); ?>">
                                                    <input type="hidden" name="page_key" value="<?php echo $page['page_key']; ?>">
                                                    <button type="submit" class="btn-outline-danger btn-sm">
                                                        <i class="fa-solid fa-trash"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                    
                                    <?php if (empty($pages)): ?>
                                        <tr>
                                            <td colspan="5" class="text-center">No pages found. <a href="?edit=new">Create a new page</a></td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </section>
        <?php endif; ?>
    </div>
</main>

<?php include __DIR__ . '/includes/footer.php'; ?>

