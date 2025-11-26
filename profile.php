<?php
require_once __DIR__ . '/bootstrap.php';

$auth = new Auth();
$auth->requireAuth();

$db = Database::getInstance();
$error = '';
$success = '';

// Get current user data
$userId = $auth->getUserId();
$user = $db->fetchOne("SELECT * FROM users WHERE id = :id", ['id' => $userId]);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $security = Security::getInstance();
    $security->requireCSRF();
    
    $action = $_POST['action'] ?? '';
    
    if ($action === 'update_profile') {
        $name = trim($_POST['name'] ?? '');
        $username = trim($_POST['username'] ?? '');
        $bio = trim($_POST['bio'] ?? '');
        
        // Check if username is taken by another user
        if (!empty($username)) {
            $existing = $db->fetchOne(
                "SELECT id FROM users WHERE username = :username AND id != :id",
                ['username' => $username, 'id' => $userId]
            );
            if ($existing) {
                $error = 'Username is already taken';
            } else {
                $db->update('users', [
                    'name' => $name ?: null,
                    'username' => $username ?: null,
                    'bio' => $bio ?: null
                ], 'id = :id', ['id' => $userId]);
                $success = 'Profile updated successfully!';
                // Refresh user data
                $user = $db->fetchOne("SELECT * FROM users WHERE id = :id", ['id' => $userId]);
            }
        } else {
            $db->update('users', [
                'name' => $name ?: null,
                'bio' => $bio ?: null
            ], 'id = :id', ['id' => $userId]);
            $success = 'Profile updated successfully!';
            $user = $db->fetchOne("SELECT * FROM users WHERE id = :id", ['id' => $userId]);
        }
    } elseif ($action === 'change_password') {
        $currentPassword = $_POST['current_password'] ?? '';
        $newPassword = $_POST['new_password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';
        
        if (empty($currentPassword) || empty($newPassword) || empty($confirmPassword)) {
            $error = 'All password fields are required';
        } elseif ($newPassword !== $confirmPassword) {
            $error = 'New passwords do not match';
        } elseif (strlen($newPassword) < 8) {
            $error = 'Password must be at least 8 characters long';
        } elseif (!password_verify($currentPassword, $user['password'])) {
            $error = 'Current password is incorrect';
        } else {
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
            $db->update('users', ['password' => $hashedPassword], 'id = :id', ['id' => $userId]);
            $success = 'Password changed successfully!';
        }
    }
}

// Get user's subscriptions
$subscription = new Subscription();
$subscriptions = $subscription->getSubscriptions($userId);

$seo = new SEO();
$seo->setTitle('My Profile - ' . APP_NAME);
$seo->setDescription('Manage your profile and account settings');
include __DIR__ . '/includes/header.php';
?>

<main class="main-content profile-page">
    <div class="container">
        <div class="profile-header">
            <h1>My Profile</h1>
            <p>Manage your account settings and preferences</p>
        </div>
        
        <?php if ($error): ?>
            <div class="alert alert-error">
                <i class="fas fa-exclamation-circle"></i>
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i>
                <?php echo htmlspecialchars($success); ?>
            </div>
        <?php endif; ?>
        
        <div class="profile-content">
            <!-- Profile Info Card -->
            <div class="profile-card">
                <div class="profile-card-header">
                    <h2>Profile Information</h2>
                </div>
                <div class="profile-card-body">
                    <div class="profile-avatar">
                        <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($user['name'] ?? $user['email']); ?>&background=007aff&color=fff&size=120" alt="Profile">
                    </div>
                    
                    <form method="POST" class="profile-form">
                        <input type="hidden" name="action" value="update_profile">
                        <input type="hidden" name="csrf_token" value="<?php echo Security::getInstance()->generateCSRFToken(); ?>">
                        
                        <div class="form-group">
                            <label for="name">Full Name</label>
                            <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($user['name'] ?? ''); ?>" placeholder="Your full name">
                        </div>
                        
                        <div class="form-group">
                            <label for="username">Username</label>
                            <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($user['username'] ?? ''); ?>" pattern="[a-zA-Z0-9_]+" placeholder="username">
                            <small>Only letters, numbers, and underscores</small>
                        </div>
                        
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" id="email" value="<?php echo htmlspecialchars($user['email']); ?>" disabled>
                            <small>Email cannot be changed</small>
                        </div>
                        
                        <div class="form-group">
                            <label for="role">Role</label>
                            <input type="text" id="role" value="<?php echo ucfirst(htmlspecialchars($user['role'])); ?>" disabled>
                        </div>
                        
                        <div class="form-group">
                            <label for="bio">Bio</label>
                            <textarea id="bio" name="bio" rows="4" placeholder="Tell us about yourself..."><?php echo htmlspecialchars($user['bio'] ?? ''); ?></textarea>
                        </div>
                        
                        <button type="submit" class="btn btn-primary">Update Profile</button>
                    </form>
                </div>
            </div>
            
            <!-- Change Password Card -->
            <div class="profile-card">
                <div class="profile-card-header">
                    <h2>Change Password</h2>
                </div>
                <div class="profile-card-body">
                    <form method="POST" class="profile-form">
                        <input type="hidden" name="action" value="change_password">
                        <input type="hidden" name="csrf_token" value="<?php echo Security::getInstance()->generateCSRFToken(); ?>">
                        
                        <div class="form-group">
                            <label for="current_password">Current Password</label>
                            <input type="password" id="current_password" name="current_password" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="new_password">New Password</label>
                            <input type="password" id="new_password" name="new_password" required minlength="8">
                            <small>Must be at least 8 characters</small>
                        </div>
                        
                        <div class="form-group">
                            <label for="confirm_password">Confirm New Password</label>
                            <input type="password" id="confirm_password" name="confirm_password" required minlength="8">
                        </div>
                        
                        <button type="submit" class="btn btn-primary">Change Password</button>
                    </form>
                </div>
            </div>
            
            <!-- Subscriptions Card -->
            <div class="profile-card">
                <div class="profile-card-header">
                    <h2>My Subscriptions</h2>
                </div>
                <div class="profile-card-body">
                    <?php if (empty($subscriptions)): ?>
                        <p>You are not subscribed to any newsletters.</p>
                    <?php else: ?>
                        <div class="subscriptions-list">
                            <?php foreach ($subscriptions as $sub): ?>
                                <div class="subscription-item">
                                    <div>
                                        <strong><?php echo ucfirst($sub['subscription_type']); ?></strong>
                                        <p>Subscribed: <?php echo date('M d, Y', strtotime($sub['subscribed_at'])); ?></p>
                                    </div>
                                    <span class="subscription-status <?php echo $sub['status']; ?>">
                                        <?php echo ucfirst($sub['status']); ?>
                                    </span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</main>

<?php include __DIR__ . '/includes/footer.php'; ?>

