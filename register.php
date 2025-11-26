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
    $confirmPassword = $_POST['confirm_password'] ?? '';
    $name = trim($_POST['name'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $agreeTerms = isset($_POST['agree_terms']);
    
    if (empty($email) || empty($password) || empty($confirmPassword)) {
        $error = 'Please fill in all required fields';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address';
    } elseif (strlen($password) < 8) {
        $error = 'Password must be at least 8 characters long';
    } elseif ($password !== $confirmPassword) {
        $error = 'Passwords do not match';
    } elseif (!$agreeTerms) {
        $error = 'You must agree to the terms and conditions';
    } else {
        try {
            $result = $auth->register($email, $password, $name, $username);
            $success = 'Account created successfully! Please check your email to verify your account.';
            
            // Auto-login after registration
            if ($auth->login($email, $password)) {
                header('Location: /index.php?registered=1');
                exit;
            }
        } catch (Exception $e) {
            $error = $e->getMessage();
        }
    }
}

$seo = new SEO();
$seo->setTitle('Sign Up - ' . APP_NAME);
$seo->setDescription('Create a new ' . APP_NAME . ' account');
include __DIR__ . '/includes/header.php';
?>

<main class="main-content auth-page">
    <div class="auth-container">
        <div class="auth-card">
            <div class="auth-header">
                <h1>Create Account</h1>
                <p>Join <?php echo APP_NAME; ?> today</p>
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
                    <label for="name">Full Name</label>
                    <input type="text" id="name" name="name" 
                           value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>"
                           placeholder="John Doe">
                </div>
                
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" 
                           value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>"
                           placeholder="johndoe" pattern="[a-zA-Z0-9_]+" 
                           title="Only letters, numbers, and underscores allowed">
                    <small>Only letters, numbers, and underscores</small>
                </div>
                
                <div class="form-group">
                    <label for="email">Email Address *</label>
                    <input type="email" id="email" name="email" required 
                           value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>"
                           placeholder="your@email.com">
                </div>
                
                <div class="form-group">
                    <label for="password">Password *</label>
                    <input type="password" id="password" name="password" required 
                           placeholder="At least 8 characters" minlength="8">
                    <small>Must be at least 8 characters</small>
                </div>
                
                <div class="form-group">
                    <label for="confirm_password">Confirm Password *</label>
                    <input type="password" id="confirm_password" name="confirm_password" required 
                           placeholder="Confirm your password">
                </div>
                
                <div class="form-group">
                    <label class="checkbox-label">
                        <input type="checkbox" name="agree_terms" value="1" required>
                        <span>I agree to the <a href="/terms.php" target="_blank">Terms and Conditions</a> and <a href="/privacy.php" target="_blank">Privacy Policy</a></span>
                    </label>
                </div>
                
                <button type="submit" class="btn btn-primary btn-block">Create Account</button>
            </form>
            
            <div class="auth-footer">
                <p>Already have an account? <a href="/login.php">Sign in</a></p>
            </div>
        </div>
    </div>
</main>

<?php include __DIR__ . '/includes/footer.php'; ?>

