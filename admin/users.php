<?php
require_once __DIR__ . '/../bootstrap.php';

$pageTitle = 'User Management';
$db = Database::getInstance();
$auth = new Auth();
$auth->requireAdmin();

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $security = Security::getInstance();
    $security->requireCSRF();
    
    $action = $_POST['action'] ?? '';
    
    if ($action === 'create') {
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $name = trim($_POST['name'] ?? '');
        $username = trim($_POST['username'] ?? '');
        $role = $_POST['role'] ?? 'user';
        
        if (empty($email) || empty($password)) {
            $error = 'Email and password are required';
        } else {
            try {
                $auth->createUser($email, $password, $role, $name, $username);
                $success = 'User created successfully!';
            } catch (Exception $e) {
                $error = $e->getMessage();
            }
        }
    } elseif ($action === 'update_role') {
        $userId = (int)($_POST['user_id'] ?? 0);
        $role = $_POST['role'] ?? 'user';
        
        if ($userId && in_array($role, ['admin', 'editor', 'writer', 'moderator', 'user'])) {
            $db->update('users', ['role' => $role], 'id = :id', ['id' => $userId]);
            $success = 'User role updated successfully!';
        } else {
            $error = 'Invalid user or role';
        }
    } elseif ($action === 'toggle_active') {
        $userId = (int)($_POST['user_id'] ?? 0);
        $isActive = (int)($_POST['is_active'] ?? 0);
        
        if ($userId) {
            $db->update('users', ['is_active' => $isActive], 'id = :id', ['id' => $userId]);
            $success = 'User status updated successfully!';
        }
    } elseif ($action === 'delete') {
        $userId = (int)($_POST['user_id'] ?? 0);
        
        if ($userId && $userId != $auth->getUserId()) {
            $db->delete('users', 'id = :id', ['id' => $userId]);
            $success = 'User deleted successfully!';
        } else {
            $error = 'Cannot delete your own account';
        }
    }
}

$users = $db->fetchAll("SELECT * FROM users ORDER BY created_at DESC");
$roleCounts = [
    'admin' => 0,
    'editor' => 0,
    'writer' => 0,
    'moderator' => 0,
    'user' => 0
];

foreach ($users as $user) {
    if (isset($roleCounts[$user['role']])) {
        $roleCounts[$user['role']]++;
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
            <h2 class="page-title">User Management</h2>
        </div>
        <div class="top-bar-right">
            <button class="notification-btn btn-primary" onclick="openModal('newUserModal')">
                <i class="fa-solid fa-plus"></i> New User
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
        <span class="breadcrumb-current">Users</span>
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

        <!-- Role Statistics -->
        <section class="additional-cards">
            <div class="info-card">
                <div class="card-header">
                    <h3>User Statistics</h3>
                </div>
                <div class="card-body">
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 1rem;">
                        <div style="text-align: center; padding: 1rem; background: rgba(0, 122, 255, 0.1); border-radius: 8px;">
                            <div style="font-size: 2rem; font-weight: 700; color: #007aff;"><?php echo $roleCounts['admin']; ?></div>
                            <div style="color: var(--text-secondary); font-size: 0.9rem;">Admins</div>
                        </div>
                        <div style="text-align: center; padding: 1rem; background: rgba(88, 86, 214, 0.1); border-radius: 8px;">
                            <div style="font-size: 2rem; font-weight: 700; color: #5856d6;"><?php echo $roleCounts['editor']; ?></div>
                            <div style="color: var(--text-secondary); font-size: 0.9rem;">Editors</div>
                        </div>
                        <div style="text-align: center; padding: 1rem; background: rgba(175, 82, 222, 0.1); border-radius: 8px;">
                            <div style="font-size: 2rem; font-weight: 700; color: #af52de;"><?php echo $roleCounts['writer']; ?></div>
                            <div style="color: var(--text-secondary); font-size: 0.9rem;">Writers</div>
                        </div>
                        <div style="text-align: center; padding: 1rem; background: rgba(255, 45, 85, 0.1); border-radius: 8px;">
                            <div style="font-size: 2rem; font-weight: 700; color: #ff2d55;"><?php echo $roleCounts['moderator']; ?></div>
                            <div style="color: var(--text-secondary); font-size: 0.9rem;">Moderators</div>
                        </div>
                        <div style="text-align: center; padding: 1rem; background: rgba(0, 0, 0, 0.05); border-radius: 8px;">
                            <div style="font-size: 2rem; font-weight: 700; color: var(--text-primary);"><?php echo $roleCounts['user']; ?></div>
                            <div style="color: var(--text-secondary); font-size: 0.9rem;">Users</div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Users Table -->
        <section class="additional-cards">
            <div class="info-card">
                <div class="card-header">
                    <h3>All Users (<?php echo count($users); ?>)</h3>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Username</th>
                                    <th>Role</th>
                                    <th>Status</th>
                                    <th>Last Login</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($users as $user): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($user['name'] ?? 'N/A'); ?></td>
                                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                                        <td><?php echo htmlspecialchars($user['username'] ?? 'N/A'); ?></td>
                                        <td>
                                            <form method="POST" style="display: inline;">
                                                <input type="hidden" name="action" value="update_role">
                                                <input type="hidden" name="csrf_token" value="<?php echo Security::getInstance()->generateCSRFToken(); ?>">
                                                <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                                <select name="role" onchange="this.form.submit()" style="padding: 0.5rem; border: 1px solid var(--glass-border); border-radius: 6px;">
                                                    <option value="admin" <?php echo $user['role'] === 'admin' ? 'selected' : ''; ?>>Admin</option>
                                                    <option value="editor" <?php echo $user['role'] === 'editor' ? 'selected' : ''; ?>>Editor</option>
                                                    <option value="writer" <?php echo $user['role'] === 'writer' ? 'selected' : ''; ?>>Writer</option>
                                                    <option value="moderator" <?php echo $user['role'] === 'moderator' ? 'selected' : ''; ?>>Moderator</option>
                                                    <option value="user" <?php echo $user['role'] === 'user' ? 'selected' : ''; ?>>User</option>
                                                </select>
                                            </form>
                                        </td>
                                        <td>
                                            <form method="POST" style="display: inline;">
                                                <input type="hidden" name="action" value="toggle_active">
                                                <input type="hidden" name="csrf_token" value="<?php echo Security::getInstance()->generateCSRFToken(); ?>">
                                                <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                                <input type="hidden" name="is_active" value="<?php echo $user['is_active'] ? 0 : 1; ?>">
                                                <button type="submit" class="btn-outline-<?php echo $user['is_active'] ? 'success' : 'danger'; ?> btn-sm">
                                                    <?php echo $user['is_active'] ? 'Active' : 'Inactive'; ?>
                                                </button>
                                            </form>
                                        </td>
                                        <td><?php echo $user['last_login'] ? date('M d, Y', strtotime($user['last_login'])) : 'Never'; ?></td>
                                        <td>
                                            <?php if ($user['id'] != $auth->getUserId()): ?>
                                                <form method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this user?');">
                                                    <input type="hidden" name="action" value="delete">
                                                    <input type="hidden" name="csrf_token" value="<?php echo Security::getInstance()->generateCSRFToken(); ?>">
                                                    <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                                    <button type="submit" class="btn-outline-danger btn-sm">
                                                        <i class="fa-solid fa-trash"></i>
                                                    </button>
                                                </form>
                                            <?php endif; ?>
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

<!-- New User Modal -->
<div id="newUserModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Create New User</h2>
            <button type="button" class="close-btn" onclick="closeModal('newUserModal')">&times;</button>
        </div>
        <form method="POST">
            <input type="hidden" name="action" value="create">
            <input type="hidden" name="csrf_token" value="<?php echo Security::getInstance()->generateCSRFToken(); ?>">
            <div class="form-group">
                <label for="name">Full Name</label>
                <input type="text" id="name" name="name" class="form-control">
            </div>
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" class="form-control" pattern="[a-zA-Z0-9_]+">
            </div>
            <div class="form-group">
                <label for="email">Email *</label>
                <input type="email" id="email" name="email" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="password">Password *</label>
                <input type="password" id="password" name="password" class="form-control" required minlength="8">
            </div>
            <div class="form-group">
                <label for="role">Role *</label>
                <select id="role" name="role" class="form-control" required>
                    <option value="user">User</option>
                    <option value="writer">Writer</option>
                    <option value="moderator">Moderator</option>
                    <option value="editor">Editor</option>
                    <option value="admin">Admin</option>
                </select>
            </div>
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Create User</button>
                <button type="button" class="btn btn-secondary" onclick="closeModal('newUserModal')">Cancel</button>
            </div>
        </form>
    </div>
</div>

<script>
function openModal(modalId) {
    document.getElementById(modalId).style.display = 'block';
}

function closeModal(modalId) {
    document.getElementById(modalId).style.display = 'none';
}
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>

