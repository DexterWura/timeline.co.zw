<?php
require_once __DIR__ . '/bootstrap.php';

$auth = new Auth();
$error = '';
$success = '';

// Redirect if already logged in
if ($auth->isLoggedIn()) {
    header('Location: /index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $security = Security::getInstance();
    $security->requireCSRF();
    
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $remember = isset($_POST['remember']);
    
    if (empty($email) || empty($password)) {
        $error = 'Please fill in all fields';
    } else {
        if ($auth->login($email, $password)) {
            if ($remember) {
                // Set cookie for 30 days
                setcookie('remember_token', bin2hex(random_bytes(32)), time() + (30 * 24 * 60 * 60), '/');
            }
            
            // Redirect based on role
            $role = $auth->getUserRole();
            if ($role === 'admin' || $role === 'editor') {
                header('Location: /admin/dashboard.php');
            } else {
                header('Location: /index.php');
            }
            exit;
        } else {
            $error = 'Invalid email or password';
        }
    }
}

$seo = new SEO();
$seo->setTitle('Login - ' . APP_NAME);
$seo->setDescription('Login to your ' . APP_NAME . ' account');
include __DIR__ . '/includes/header.php';
?>

<main class="main-content auth-page">
    <div class="auth-container">
        <div class="auth-card">
            <div class="auth-header">
                <h1>Welcome Back</h1>
                <p>Sign in to your account</p>
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
            
            <form method="POST" class="auth-form">
                <input type="hidden" name="csrf_token" value="<?php echo Security::getInstance()->generateCSRFToken(); ?>">
                
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email" required autofocus 
                           value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>"
                           placeholder="your@email.com">
                </div>
                
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required 
                           placeholder="Enter your password">
                </div>
                
                <div class="form-group form-group-inline">
                    <label class="checkbox-label">
                        <input type="checkbox" name="remember" value="1">
                        <span>Remember me</span>
                    </label>
                    <a href="/forgot-password.php" class="forgot-link">Forgot password?</a>
                </div>
                
                <button type="submit" class="btn btn-primary btn-block">Sign In</button>
            </form>
            
            <div class="auth-footer">
                <p>Don't have an account? <a href="/register.php">Sign up</a></p>
            </div>
        </div>
    </div>
</main>

<?php include __DIR__ . '/includes/footer.php'; ?>

