<?php
// Common header for admin pages
$auth = new Auth();
$auth->requireAdmin();
$currentUser = [
    'email' => $auth->getUserEmail(),
    'id' => $auth->getUserId()
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? $pageTitle . ' - ' : ''; ?><?php echo APP_NAME; ?> Admin</title>
    <link rel="stylesheet" href="/admin/assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <?php if (isset($includeCharts) && $includeCharts): ?>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <?php endif; ?>
</head>
<body>
    <!-- Sidebar Navigation -->
    <aside class="sidebar">
        <div class="sidebar-header">
            <a href="/admin/dashboard.php" class="logo-link">
                <h1 class="logo"><?php echo APP_NAME; ?></h1>
            </a>
            <button class="sidebar-toggle" id="sidebarToggle" aria-label="Toggle sidebar">
                <i class="fa-solid fa-chevron-left"></i>
            </button>
        </div>
        <nav class="sidebar-nav">
            <a href="/admin/dashboard.php" class="nav-item <?php echo (basename($_SERVER['PHP_SELF']) == 'dashboard.php') ? 'active' : ''; ?>">
                <i class="fa-solid fa-house nav-icon"></i>
                <span>Dashboard</span>
            </a>
            <a href="/admin/analytics.php" class="nav-item <?php echo (basename($_SERVER['PHP_SELF']) == 'analytics.php') ? 'active' : ''; ?>">
                <i class="fa-solid fa-chart-line nav-icon"></i>
                <span>Analytics</span>
            </a>
            <a href="/admin/music-charts.php" class="nav-item <?php echo (basename($_SERVER['PHP_SELF']) == 'music-charts.php') ? 'active' : ''; ?>">
                <i class="fa-solid fa-music nav-icon"></i>
                <span>Music Charts</span>
            </a>
            <a href="/admin/videos.php" class="nav-item <?php echo (basename($_SERVER['PHP_SELF']) == 'videos.php') ? 'active' : ''; ?>">
                <i class="fa-solid fa-video nav-icon"></i>
                <span>Videos</span>
            </a>
            <a href="/admin/awards.php" class="nav-item <?php echo (basename($_SERVER['PHP_SELF']) == 'awards.php') ? 'active' : ''; ?>">
                <i class="fa-solid fa-trophy nav-icon"></i>
                <span>Awards</span>
            </a>
            <a href="/admin/richest.php" class="nav-item <?php echo (basename($_SERVER['PHP_SELF']) == 'richest.php') ? 'active' : ''; ?>">
                <i class="fa-solid fa-dollar-sign nav-icon"></i>
                <span>Richest People</span>
            </a>
            <a href="/admin/blog.php" class="nav-item <?php echo (basename($_SERVER['PHP_SELF']) == 'blog.php') ? 'active' : ''; ?>">
                <i class="fa-solid fa-blog nav-icon"></i>
                <span>Blog</span>
            </a>
            <a href="/admin/hall-of-fame.php" class="nav-item <?php echo (basename($_SERVER['PHP_SELF']) == 'hall-of-fame.php') ? 'active' : ''; ?>">
                <i class="fa-solid fa-star nav-icon"></i>
                <span>Hall of Fame</span>
            </a>
            <a href="/admin/news.php" class="nav-item <?php echo (basename($_SERVER['PHP_SELF']) == 'news.php') ? 'active' : ''; ?>">
                <i class="fa-solid fa-newspaper nav-icon"></i>
                <span>News</span>
            </a>
            <a href="/admin/users.php" class="nav-item <?php echo (basename($_SERVER['PHP_SELF']) == 'users.php') ? 'active' : ''; ?>">
                <i class="fa-solid fa-users nav-icon"></i>
                <span>Users</span>
            </a>
            <a href="/admin/page-content.php" class="nav-item <?php echo (basename($_SERVER['PHP_SELF']) == 'page-content.php') ? 'active' : ''; ?>">
                <i class="fa-solid fa-file-lines nav-icon"></i>
                <span>Page Content</span>
            </a>
            <a href="/admin/logs.php" class="nav-item <?php echo (basename($_SERVER['PHP_SELF']) == 'logs.php') ? 'active' : ''; ?>">
                <i class="fa-solid fa-file-lines nav-icon"></i>
                <span>System Logs</span>
            </a>
            <a href="/admin/migrations.php" class="nav-item <?php echo (basename($_SERVER['PHP_SELF']) == 'migrations.php') ? 'active' : ''; ?>">
                <i class="fa-solid fa-database nav-icon"></i>
                <span>Migrations</span>
            </a>
            <a href="/admin/settings.php" class="nav-item <?php echo (basename($_SERVER['PHP_SELF']) == 'settings.php') ? 'active' : ''; ?>">
                <i class="fa-solid fa-gear nav-icon"></i>
                <span>Settings</span>
            </a>
            <a href="/admin/logout.php" class="nav-item">
                <i class="fa-solid fa-sign-out nav-icon"></i>
                <span>Logout</span>
            </a>
        </nav>
        <div class="sidebar-version">v1.0.2</div>
    </aside>

