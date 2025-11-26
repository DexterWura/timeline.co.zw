<?php
require_once __DIR__ . '/../bootstrap.php';

$auth = new Auth();
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    
    if ($auth->login($email, $password)) {
        header('Location: /admin/dashboard.php');
        exit;
    } else {
        $error = 'Invalid email or password';
    }
}

// Redirect if already logged in
if ($auth->isLoggedIn()) {
    header('Location: /admin/dashboard.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - <?php echo APP_NAME; ?> Admin</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>
<body class="auth-page">
    <div class="auth-container">
        <div class="auth-card">
            <div class="auth-header">
                <h1 class="logo" style="font-size: 2rem; margin-bottom: 0.5rem;"><?php echo APP_NAME; ?></h1>
                <p style="color: var(--text-secondary); margin: 0;">Welcome back! Please login to your account.</p>
            </div>

            <?php if ($error): ?>
                <div style="background: rgba(255, 0, 0, 0.1); color: #c33; padding: 12px; border-radius: 8px; margin-bottom: 1.5rem; text-align: center;">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <form class="auth-form" method="POST" action="">
                <div class="form-group">
                    <label for="email">
                        <i class="fa-solid fa-envelope"></i> Email Address
                    </label>
                    <input type="email" id="email" name="email" placeholder="Enter your email" required>
                </div>

                <div class="form-group">
                    <label for="password">
                        <i class="fa-solid fa-lock"></i> Password
                    </label>
                    <div style="position: relative;">
                        <input type="password" id="password" name="password" placeholder="Enter your password" required style="padding-right: 3rem;">
                        <button type="button" class="password-toggle" onclick="togglePassword()">
                            <i class="fa-solid fa-eye" id="passwordToggleIcon"></i>
                        </button>
                    </div>
                </div>

                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                    <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer; font-size: 0.9rem; color: var(--text-secondary);">
                        <input type="checkbox" style="cursor: pointer;">
                        <span>Remember me</span>
                    </label>
                </div>

                <button type="submit" class="auth-submit-btn">
                    <span>Sign In</span>
                    <i class="fa-solid fa-arrow-right"></i>
                </button>
            </form>

            <div style="margin-top: 2rem; padding-top: 2rem; border-top: 1px solid var(--glass-border);">
                <p style="text-align: center; color: var(--text-tertiary); font-size: 0.85rem; margin: 0;">
                    <i class="fa-solid fa-shield-halved"></i> Secure login with encryption
                </p>
            </div>
        </div>
    </div>

    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const toggleIcon = document.getElementById('passwordToggleIcon');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.classList.remove('fa-eye');
                toggleIcon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                toggleIcon.classList.remove('fa-eye-slash');
                toggleIcon.classList.add('fa-eye');
            }
        }
    </script>
</body>
</html>

