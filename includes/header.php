<?php
// Common header for frontend pages
require_once __DIR__ . '/../bootstrap.php';
$currentPage = basename($_SERVER['PHP_SELF'], '.php');
// Handle special cases
if ($currentPage == 'blog-view' || $currentPage == 'article') {
    $currentPage = strpos($_SERVER['REQUEST_URI'], '/blog/') !== false ? 'blog' : 'news';
}
$seo = new SEO();
$auth = new Auth();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $seo->getTitle(); ?></title>
    <?php echo $seo->render(); ?>
    <link rel="stylesheet" href="/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <?php
    $settings = new Settings();
    $adsenseId = $settings->get('adsense_client_id');
    if ($adsenseId):
    ?>
    <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=<?php echo htmlspecialchars($adsenseId); ?>" crossorigin="anonymous"></script>
    <?php endif; ?>
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="header-top">
            <div class="container">
                <div class="header-top-content">
                    <button class="mobile-menu-toggle" id="mobileMenuToggle" aria-label="Toggle menu">
                        <span></span>
                        <span></span>
                        <span></span>
                    </button>
                    <div class="logo">
                        <i class="fas fa-music"></i>
                        <span>timeline</span>
                    </div>
                    <nav class="main-nav" id="mainNav">
                        <a href="/index.php" class="nav-link <?php echo $currentPage == 'index' ? 'active' : ''; ?>">Home</a>
                        <a href="/music.php" class="nav-link <?php echo $currentPage == 'music' ? 'active' : ''; ?>">Music</a>
                        <a href="/charts.php" class="nav-link <?php echo $currentPage == 'charts' ? 'active' : ''; ?>">Charts</a>
                        <a href="/videos.php" class="nav-link <?php echo $currentPage == 'videos' ? 'active' : ''; ?>">Videos</a>
                        <?php
                        $settings = new Settings();
                        if ($settings->get('page_enabled_richest', 1)):
                        ?>
                        <a href="/richest.php" class="nav-link <?php echo $currentPage == 'richest' ? 'active' : ''; ?>">Richest</a>
                        <?php endif; ?>
                        <?php if ($settings->get('page_enabled_awards', 1)): ?>
                        <a href="/awards.php" class="nav-link <?php echo $currentPage == 'awards' ? 'active' : ''; ?>">Awards</a>
                        <?php endif; ?>
                        <a href="/hall-of-fame.php" class="nav-link <?php echo $currentPage == 'hall-of-fame' ? 'active' : ''; ?>">Hall of Fame</a>
                        <?php if ($settings->get('page_enabled_blog', 1)): ?>
                        <a href="/blog.php" class="nav-link <?php echo $currentPage == 'blog' ? 'active' : ''; ?>">Blog</a>
                        <?php endif; ?>
                        <?php if ($settings->get('page_enabled_news', 1)): ?>
                        <a href="/news.php" class="nav-link <?php echo $currentPage == 'news' ? 'active' : ''; ?>">News</a>
                        <?php endif; ?>
                        <?php if ($settings->get('page_enabled_business', 1)): ?>
                        <a href="/business.php" class="nav-link <?php echo $currentPage == 'business' ? 'active' : ''; ?>">Business</a>
                        <?php endif; ?>
                    </nav>
                    <div class="header-actions">
                        <?php if ($auth->isLoggedIn()): ?>
                            <a href="/profile.php" class="user-profile-link">
                                <i class="fas fa-user"></i>
                                <span><?php echo htmlspecialchars($auth->getUserName()); ?></span>
                            </a>
                            <?php if ($auth->isAdmin() || $auth->canWrite()): ?>
                                <a href="/admin/dashboard.php" class="login-btn" style="margin-right: 0.5rem;">ADMIN</a>
                            <?php endif; ?>
                            <a href="/logout.php" class="login-btn">LOGOUT</a>
                        <?php else: ?>
                            <button class="subscribe-btn" onclick="openSubscribeModal()">SUBSCRIBE</button>
                            <a href="/login.php" class="login-btn">LOGIN</a>
                        <?php endif; ?>
                        <button class="search-btn"><i class="fas fa-search"></i></button>
                    </div>
                </div>
            </div>
        </div>
        <div class="header-bottom">
            <div class="container">
                <div class="chart-nav">
                    <a href="/charts.php" class="chart-nav-link <?php echo $currentPage == 'charts' ? 'active' : ''; ?>">TIMELINE HOT 100™</a>
                    <a href="/charts.php" class="chart-nav-link">TIMELINE 200™</a>
                    <a href="/charts.php" class="chart-nav-link">GLOBAL 200</a>
                    <a href="/charts.php" class="chart-nav-link">ARTIST 100</a>
                </div>
            </div>
        </div>
    </header>

