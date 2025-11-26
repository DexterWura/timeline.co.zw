<?php
require_once __DIR__ . '/../bootstrap.php';

$pageTitle = 'Blog Management';
$blog = new Blog();
$auth = new Auth();
$error = '';
$success = '';

$security = Security::getInstance();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $security->requireCSRF();
    $action = $_POST['action'] ?? '';
    
    if ($action === 'create') {
        $title = $_POST['title'] ?? '';
        $content = $_POST['content'] ?? '';
        $status = $_POST['status'] ?? 'draft';
        
        if (empty($title) || empty($content)) {
            $error = 'Title and content are required';
        } else {
            $blog->create($title, $content, $auth->getUserId(), $status);
            $success = 'Blog post created successfully!';
        }
    } elseif ($action === 'delete') {
        $id = $_POST['id'] ?? 0;
        $blog->delete($id);
        $success = 'Blog post deleted successfully!';
    } elseif ($action === 'update_status') {
        $id = $_POST['id'] ?? 0;
        $status = $_POST['status'] ?? 'draft';
        $data = ['status' => $status];
        if ($status === 'published') {
            $data['published_at'] = date('Y-m-d H:i:s');
        }
        $blog->update($id, $data);
        $success = 'Status updated successfully!';
    }
}

$blogs = $blog->getAll();
$totalPosts = count($blogs);
$publishedPosts = count(array_filter($blogs, fn($b) => $b['status'] === 'published'));
$draftPosts = count(array_filter($blogs, fn($b) => $b['status'] === 'draft'));

include __DIR__ . '/includes/header.php';
?>

    <!-- Main Content -->
    <main class="main-content">
        <header class="top-bar">
            <div class="top-bar-left">
                <button class="menu-toggle" id="menuToggle">
                    <i class="fa-solid fa-bars"></i>
                </button>
                <h2 class="page-title">Blog</h2>
            </div>
            <div class="top-bar-right">
                <button class="search-bar-toggle" aria-label="Toggle search">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </button>
                <div class="search-box">
                    <i class="fa-solid fa-magnifying-glass search-icon"></i>
                    <input type="text" placeholder="Search articles...">
                </div>
                <button class="notification-btn btn-primary" onclick="document.getElementById('newPostModal').style.display='block'">
                    <i class="fa-solid fa-plus"></i> New Post
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
            <a href="/admin/dashboard.php">Dashboard</a>
            <span class="breadcrumb-separator">
                <i class="fa-solid fa-chevron-right"></i>
            </span>
            <span class="breadcrumb-current">Blog</span>
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
                        <h3>Total Posts</h3>
                        <i class="fa-solid fa-file-lines stat-icon"></i>
                    </div>
                    <div class="stat-card-body">
                        <p class="stat-value"><?php echo $totalPosts; ?></p>
                        <p class="stat-change positive">All posts</p>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-card-header">
                        <h3>Published</h3>
                        <i class="fa-solid fa-check-circle stat-icon"></i>
                    </div>
                    <div class="stat-card-body">
                        <p class="stat-value"><?php echo $publishedPosts; ?></p>
                        <p class="stat-change positive">Active posts</p>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-card-header">
                        <h3>Drafts</h3>
                        <i class="fa-solid fa-edit stat-icon"></i>
                    </div>
                    <div class="stat-card-body">
                        <p class="stat-value"><?php echo $draftPosts; ?></p>
                        <p class="stat-change negative">In progress</p>
                    </div>
                </div>
            </section>

            <section class="additional-cards">
                <div class="info-card">
                    <div class="card-header">
                        <h3>Recent Posts</h3>
                    </div>
                    <div class="card-body">
                        <div class="blog-posts-grid">
                            <?php foreach ($blogs as $post): ?>
                                <article class="blog-post-card">
                                    <div class="blog-post-image">
                                        <span class="blog-post-badge <?php echo $post['status'] === 'published' ? 'blog-post-published' : 'blog-post-draft'; ?>">
                                            <?php echo ucfirst($post['status']); ?>
                                        </span>
                                    </div>
                                    <div class="blog-post-content">
                                        <div class="blog-post-meta">
                                            <span class="blog-post-date"><?php echo date('M d, Y', strtotime($post['created_at'])); ?></span>
                                        </div>
                                        <h3 class="blog-post-title"><?php echo htmlspecialchars($post['title']); ?></h3>
                                        <p class="blog-post-excerpt"><?php echo htmlspecialchars($post['excerpt'] ?? substr(strip_tags($post['content']), 0, 100)); ?>...</p>
                                        <div class="blog-post-footer">
                                            <div class="blog-post-actions">
                                                <form method="POST" style="display: inline;">
                                                    <input type="hidden" name="action" value="update_status">
                                                    <input type="hidden" name="csrf_token" value="<?php echo $security->generateCSRFToken(); ?>">
                                                    <input type="hidden" name="id" value="<?php echo $post['id']; ?>">
                                                    <input type="hidden" name="status" value="<?php echo $post['status'] === 'published' ? 'draft' : 'published'; ?>">
                                                    <button type="submit" class="btn-outline-primary btn-sm">
                                                        <i class="fa-solid fa-<?php echo $post['status'] === 'published' ? 'eye-slash' : 'check'; ?>"></i>
                                                        <?php echo $post['status'] === 'published' ? 'Unpublish' : 'Publish'; ?>
                                                    </button>
                                                </form>
                                                <form method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this post?');">
                                                    <input type="hidden" name="action" value="delete">
                                                    <input type="hidden" name="csrf_token" value="<?php echo $security->generateCSRFToken(); ?>">
                                                    <input type="hidden" name="id" value="<?php echo $post['id']; ?>">
                                                    <button type="submit" class="btn-outline-danger btn-sm">
                                                        <i class="fa-solid fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </article>
                            <?php endforeach; ?>
                            
                            <?php if (empty($blogs)): ?>
                                <p style="grid-column: 1 / -1; text-align: center; padding: 3rem; color: var(--text-secondary);">
                                    No blog posts yet. Click "New Post" to create your first post.
                                </p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </main>

    <!-- New Post Modal -->
    <div id="newPostModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; padding: 2rem;">
        <div style="background: white; max-width: 800px; margin: 0 auto; border-radius: 10px; padding: 2rem; max-height: 90vh; overflow-y: auto;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
                <h2>New Blog Post</h2>
                <button onclick="document.getElementById('newPostModal').style.display='none'" style="background: none; border: none; font-size: 1.5rem; cursor: pointer;">&times;</button>
            </div>
            <form method="POST">
                <input type="hidden" name="action" value="create">
                <input type="hidden" name="csrf_token" value="<?php echo $security->generateCSRFToken(); ?>">
                <div style="margin-bottom: 1.5rem;">
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Title</label>
                    <input type="text" name="title" required style="width: 100%; padding: 0.75rem; border: 1px solid #ddd; border-radius: 5px;">
                </div>
                <div style="margin-bottom: 1.5rem;">
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Content</label>
                    <textarea name="content" required rows="10" style="width: 100%; padding: 0.75rem; border: 1px solid #ddd; border-radius: 5px;"></textarea>
                </div>
                <div style="margin-bottom: 1.5rem;">
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Status</label>
                    <select name="status" style="width: 100%; padding: 0.75rem; border: 1px solid #ddd; border-radius: 5px;">
                        <option value="draft">Draft</option>
                        <option value="published">Published</option>
                    </select>
                </div>
                <div style="display: flex; gap: 1rem;">
                    <button type="submit" style="padding: 0.75rem 2rem; background: var(--primary-color); color: white; border: none; border-radius: 5px; cursor: pointer;">Create Post</button>
                    <button type="button" onclick="document.getElementById('newPostModal').style.display='none'" style="padding: 0.75rem 2rem; background: #ccc; color: white; border: none; border-radius: 5px; cursor: pointer;">Cancel</button>
                </div>
            </form>
        </div>
    </div>

<?php include __DIR__ . '/includes/footer.php'; ?>

