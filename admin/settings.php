<?php
require_once __DIR__ . '/../bootstrap.php';

$pageTitle = 'Settings';
$settings = new Settings();
$error = '';
$success = '';

$security = Security::getInstance();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $security->requireCSRF();
    $action = $_POST['action'] ?? '';
    
    if ($action === 'save_cache_settings') {
        $cacheDays = (int)($_POST['cache_duration_days'] ?? 3);
        if ($cacheDays < 1) $cacheDays = 1;
        if ($cacheDays > 30) $cacheDays = 30;
        $settings->set('cache_duration_days', $cacheDays, 'number', 'Cache duration in days');
        $success = 'Cache settings saved successfully!';
    } elseif ($action === 'save_sitemap_settings') {
        $sitemapFreq = (int)($_POST['sitemap_frequency'] ?? 1);
        if ($sitemapFreq < 1) $sitemapFreq = 1;
        if ($sitemapFreq > 30) $sitemapFreq = 30;
        $autoGenerate = isset($_POST['auto_generate']) ? 1 : 0;
        
        $db = Database::getInstance();
        $existing = $db->fetchOne("SELECT id FROM sitemap_settings WHERE id = 1");
        if ($existing) {
            $db->update('sitemap_settings', [
                'generation_frequency' => $sitemapFreq,
                'auto_generate' => $autoGenerate
            ], 'id = 1', []);
        } else {
            $db->insert('sitemap_settings', [
                'generation_frequency' => $sitemapFreq,
                'auto_generate' => $autoGenerate
            ]);
        }
        $success = 'Sitemap settings saved successfully!';
    } elseif ($action === 'save_api_keys') {
        $settings->set('youtube_api_key', $_POST['youtube_api_key'] ?? '', 'text', 'YouTube Data API v3 Key');
        $settings->set('adsense_client_id', $_POST['adsense_client_id'] ?? '', 'text', 'Google AdSense Client ID');
        $settings->set('news_api_key', $_POST['news_api_key'] ?? '', 'text', 'News API Key');
        $settings->set('lastfm_api_key', $_POST['lastfm_api_key'] ?? '', 'text', 'Last.fm API Key');
        $settings->set('spotify_client_id', $_POST['spotify_client_id'] ?? '', 'text', 'Spotify Client ID');
        $settings->set('spotify_client_secret', $_POST['spotify_client_secret'] ?? '', 'text', 'Spotify Client Secret');
        $success = 'API keys saved successfully!';
    } elseif ($action === 'change_password') {
        $auth = new Auth();
        $currentPassword = $_POST['current_password'] ?? '';
        $newPassword = $_POST['new_password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';
        
        if (empty($currentPassword) || empty($newPassword) || empty($confirmPassword)) {
            $error = 'All password fields are required';
        } elseif ($newPassword !== $confirmPassword) {
            $error = 'New passwords do not match';
        } elseif (strlen($newPassword) < PASSWORD_MIN_LENGTH) {
            $error = 'Password must be at least ' . PASSWORD_MIN_LENGTH . ' characters';
        } else {
            // Verify current password
            $user = $auth->db->fetchOne(
                "SELECT * FROM users WHERE id = :id",
                ['id' => $auth->getUserId()]
            );
            
            if ($user && password_verify($currentPassword, $user['password'])) {
                $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
                $auth->db->update('users', 
                    ['password' => $hashedPassword],
                    'id = :id',
                    ['id' => $auth->getUserId()]
                );
                $success = 'Password changed successfully!';
            } else {
                $error = 'Current password is incorrect';
            }
        }
    }
}

$youtubeKey = $settings->get('youtube_api_key');
$adsenseKey = $settings->get('adsense_client_id');
$newsKey = $settings->get('news_api_key');
$lastfmKey = $settings->get('lastfm_api_key');
$spotifyId = $settings->get('spotify_client_id');
$spotifySecret = $settings->get('spotify_client_secret');

include __DIR__ . '/includes/header.php';
?>

    <!-- Main Content -->
    <main class="main-content">
        <!-- Top Bar -->
        <header class="top-bar">
            <div class="top-bar-left">
                <button class="menu-toggle" id="menuToggle">
                    <i class="fa-solid fa-bars"></i>
                </button>
                <h2 class="page-title">Settings</h2>
            </div>
            <div class="top-bar-right">
                <div class="search-box">
                    <i class="fa-solid fa-magnifying-glass search-icon"></i>
                    <input type="text" placeholder="Search...">
                </div>
                <button class="notification-btn">
                    <i class="fa-solid fa-bell"></i>
                    <span class="notification-badge">3</span>
                </button>
                <div class="user-profile">
                    <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($currentUser['email']); ?>&background=random" alt="User" class="profile-img">
                </div>
            </div>
        </header>

        <!-- Breadcrumbs -->
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
            <span class="breadcrumb-current">Settings</span>
        </nav>

        <!-- Settings Content -->
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

            <section class="additional-cards" style="grid-template-columns: 1fr;">
                <!-- API Keys Settings -->
                <div class="info-card">
                    <div class="card-header">
                        <h3>API Keys Configuration</h3>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="">
                            <input type="hidden" name="action" value="save_api_keys">
                            <div style="display: grid; gap: 1.5rem;">
                                <input type="hidden" name="csrf_token" value="<?php echo $security->generateCSRFToken(); ?>">
                                <div>
                                    <label style="display: block; margin-bottom: 0.5rem; color: var(--text-secondary); font-size: 0.9rem; font-weight: 500;">
                                        <i class="fa-brands fa-youtube"></i> YouTube API Key
                                    </label>
                                    <input type="password" name="youtube_api_key" value="<?php echo htmlspecialchars($youtubeKey); ?>" placeholder="Enter YouTube Data API v3 Key" style="width: 100%; padding: 0.75rem 1rem; background: rgba(0, 0, 0, 0.04); border: 1px solid var(--glass-border); border-radius: 8px; color: var(--text-primary); font-size: 0.95rem;">
                                    <p style="margin: 0.5rem 0 0 0; font-size: 0.85rem; color: var(--text-tertiary);">Get your API key from <a href="https://console.cloud.google.com/" target="_blank">Google Cloud Console</a></p>
                                </div>
                                
                                <div>
                                    <label style="display: block; margin-bottom: 0.5rem; color: var(--text-secondary); font-size: 0.9rem; font-weight: 500;">
                                        <i class="fa-brands fa-google"></i> AdSense Client ID
                                    </label>
                                    <input type="password" name="adsense_client_id" value="<?php echo htmlspecialchars($adsenseKey); ?>" placeholder="Enter Google AdSense Client ID" style="width: 100%; padding: 0.75rem 1rem; background: rgba(0, 0, 0, 0.04); border: 1px solid var(--glass-border); border-radius: 8px; color: var(--text-primary); font-size: 0.95rem;">
                                </div>
                                
                                <div>
                                    <label style="display: block; margin-bottom: 0.5rem; color: var(--text-secondary); font-size: 0.9rem; font-weight: 500;">
                                        <i class="fa-solid fa-newspaper"></i> News API Key (Optional - Free RSS feeds used if not set)
                                    </label>
                                    <input type="password" name="news_api_key" value="<?php echo htmlspecialchars($newsKey); ?>" placeholder="Enter News API Key (Optional)" style="width: 100%; padding: 0.75rem 1rem; background: rgba(0, 0, 0, 0.04); border: 1px solid var(--glass-border); border-radius: 8px; color: var(--text-primary); font-size: 0.95rem;">
                                    <p style="margin: 0.5rem 0 0 0; font-size: 0.85rem; color: var(--text-tertiary);">Optional: Get from <a href="https://newsapi.org/" target="_blank">NewsAPI.org</a>. System uses free RSS feeds if not provided.</p>
                                </div>
                                
                                <div>
                                    <label style="display: block; margin-bottom: 0.5rem; color: var(--text-secondary); font-size: 0.9rem; font-weight: 500;">
                                        <i class="fa-brands fa-lastfm"></i> Last.fm API Key (Optional)
                                    </label>
                                    <input type="password" name="lastfm_api_key" value="<?php echo htmlspecialchars($lastfmKey); ?>" placeholder="Enter Last.fm API Key (Optional)" style="width: 100%; padding: 0.75rem 1rem; background: rgba(0, 0, 0, 0.04); border: 1px solid var(--glass-border); border-radius: 8px; color: var(--text-primary); font-size: 0.95rem;">
                                    <p style="margin: 0.5rem 0 0 0; font-size: 0.85rem; color: var(--text-tertiary);">Optional: Get from <a href="https://www.last.fm/api" target="_blank">Last.fm API</a></p>
                                </div>
                                
                                <div>
                                    <label style="display: block; margin-bottom: 0.5rem; color: var(--text-secondary); font-size: 0.9rem; font-weight: 500;">
                                        <i class="fa-brands fa-spotify"></i> Spotify Client ID (Optional)
                                    </label>
                                    <input type="password" name="spotify_client_id" value="<?php echo htmlspecialchars($spotifyId); ?>" placeholder="Enter Spotify Client ID (Optional)" style="width: 100%; padding: 0.75rem 1rem; background: rgba(0, 0, 0, 0.04); border: 1px solid var(--glass-border); border-radius: 8px; color: var(--text-primary); font-size: 0.95rem;">
                                </div>
                                
                                <div>
                                    <label style="display: block; margin-bottom: 0.5rem; color: var(--text-secondary); font-size: 0.9rem; font-weight: 500;">
                                        <i class="fa-brands fa-spotify"></i> Spotify Client Secret (Optional)
                                    </label>
                                    <input type="password" name="spotify_client_secret" value="<?php echo htmlspecialchars($spotifySecret); ?>" placeholder="Enter Spotify Client Secret (Optional)" style="width: 100%; padding: 0.75rem 1rem; background: rgba(0, 0, 0, 0.04); border: 1px solid var(--glass-border); border-radius: 8px; color: var(--text-primary); font-size: 0.95rem;">
                                </div>
                                
                                <button type="submit" style="padding: 0.875rem 2rem; background: var(--primary-color); color: white; border: none; border-radius: 8px; cursor: pointer; font-weight: 500; width: fit-content; margin-top: 1rem;">
                                    Save API Keys
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Cache Settings -->
                <div class="info-card">
                    <div class="card-header">
                        <h3>Cache Settings</h3>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="">
                            <input type="hidden" name="action" value="save_cache_settings">
                            <input type="hidden" name="csrf_token" value="<?php echo $security->generateCSRFToken(); ?>">
                            <div style="display: grid; gap: 1.5rem;">
                                <div>
                                    <label style="display: block; margin-bottom: 0.5rem; color: var(--text-secondary); font-size: 0.9rem; font-weight: 500;">
                                        <i class="fa-solid fa-clock"></i> Cache Duration (Days)
                                    </label>
                                    <input type="number" name="cache_duration_days" value="<?php echo htmlspecialchars($settings->get('cache_duration_days', 3)); ?>" min="1" max="30" required style="width: 100%; padding: 0.75rem 1rem; background: rgba(0, 0, 0, 0.04); border: 1px solid var(--glass-border); border-radius: 8px; color: var(--text-primary); font-size: 0.95rem;">
                                    <p style="margin: 0.5rem 0 0 0; font-size: 0.85rem; color: var(--text-tertiary);">How many days to cache API data before refreshing (default: 3 days)</p>
                                </div>
                                <button type="submit" style="padding: 0.875rem 2rem; background: var(--primary-color); color: white; border: none; border-radius: 8px; cursor: pointer; font-weight: 500; width: fit-content; margin-top: 1rem;">
                                    Save Cache Settings
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Sitemap Settings -->
                <div class="info-card">
                    <div class="card-header">
                        <h3>Sitemap Settings</h3>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="">
                            <input type="hidden" name="action" value="save_sitemap_settings">
                            <input type="hidden" name="csrf_token" value="<?php echo $security->generateCSRFToken(); ?>">
                            <?php
                            $sitemapSettings = $db->fetchOne("SELECT * FROM sitemap_settings WHERE id = 1");
                            $sitemapFreq = $sitemapSettings['generation_frequency'] ?? 1;
                            $autoGenerate = $sitemapSettings['auto_generate'] ?? 1;
                            $lastGenerated = $sitemapSettings['last_generated'] ?? null;
                            ?>
                            <div style="display: grid; gap: 1.5rem;">
                                <div>
                                    <label style="display: block; margin-bottom: 0.5rem; color: var(--text-secondary); font-size: 0.9rem; font-weight: 500;">
                                        <i class="fa-solid fa-sitemap"></i> Sitemap Generation Frequency (Days)
                                    </label>
                                    <input type="number" name="sitemap_frequency" value="<?php echo htmlspecialchars($sitemapFreq); ?>" min="1" max="30" required style="width: 100%; padding: 0.75rem 1rem; background: rgba(0, 0, 0, 0.04); border: 1px solid var(--glass-border); border-radius: 8px; color: var(--text-primary); font-size: 0.95rem;">
                                    <p style="margin: 0.5rem 0 0 0; font-size: 0.85rem; color: var(--text-tertiary);">How often to automatically regenerate sitemap (default: 1 day)</p>
                                </div>
                                <div>
                                    <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer; font-size: 0.9rem; color: var(--text-secondary);">
                                        <input type="checkbox" name="auto_generate" value="1" <?php echo $autoGenerate ? 'checked' : ''; ?> style="cursor: pointer;">
                                        <span>Auto-generate sitemap</span>
                                    </label>
                                </div>
                                <?php if ($lastGenerated): ?>
                                    <p style="font-size: 0.85rem; color: var(--text-tertiary);">
                                        Last generated: <?php echo date('Y-m-d H:i:s', strtotime($lastGenerated)); ?>
                                    </p>
                                <?php endif; ?>
                                <div style="display: flex; gap: 1rem;">
                                <button type="submit" style="padding: 0.875rem 2rem; background: var(--primary-color); color: white; border: none; border-radius: 8px; cursor: pointer; font-weight: 500;">
                                    Save Settings
                                </button>
                                    <button type="button" onclick="generateSitemap()" style="padding: 0.875rem 2rem; background: #27ae60; color: white; border: none; border-radius: 8px; cursor: pointer; font-weight: 500;">
                                        Generate Now
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                
                <!-- System Information -->
                <div class="info-card">
                    <div class="card-header">
                        <h3>System Information</h3>
                    </div>
                    <div class="card-body">
                        <div style="display: grid; gap: 1rem;">
                            <div style="display: flex; justify-content: space-between; padding: 0.75rem; background: rgba(0, 0, 0, 0.02); border-radius: 8px;">
                                <span style="color: var(--text-secondary);">PHP Version</span>
                                <span style="font-weight: 600;"><?php echo PHP_VERSION; ?></span>
                            </div>
                            <div style="display: flex; justify-content: space-between; padding: 0.75rem; background: rgba(0, 0, 0, 0.02); border-radius: 8px;">
                                <span style="color: var(--text-secondary);">Server</span>
                                <span style="font-weight: 600;"><?php echo htmlspecialchars($_SERVER['SERVER_SOFTWARE'] ?? 'Unknown', ENT_QUOTES, 'UTF-8'); ?></span>
                            </div>
                            <div style="display: flex; justify-content: space-between; padding: 0.75rem; background: rgba(0, 0, 0, 0.02); border-radius: 8px;">
                                <span style="color: var(--text-secondary);">Database</span>
                                <span style="font-weight: 600;">Connected</span>
                            </div>
                            <div style="display: flex; justify-content: space-between; padding: 0.75rem; background: rgba(0, 0, 0, 0.02); border-radius: 8px;">
                                <span style="color: var(--text-secondary);">Environment</span>
                                <span style="font-weight: 600;"><?php echo APP_ENV; ?></span>
                            </div>
                        </div>
                    </div>
                </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Security Settings -->
                <div class="info-card">
                    <div class="card-header">
                        <h3>Security</h3>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="">
                            <input type="hidden" name="action" value="change_password">
                            <input type="hidden" name="csrf_token" value="<?php echo $security->generateCSRFToken(); ?>">
                            <div style="display: grid; gap: 1.5rem;">
                                <div>
                                    <label style="display: block; margin-bottom: 0.5rem; color: var(--text-secondary); font-size: 0.9rem; font-weight: 500;">Current Password</label>
                                    <input type="password" name="current_password" placeholder="Enter current password" required style="width: 100%; padding: 0.75rem 1rem; background: rgba(0, 0, 0, 0.04); border: 1px solid var(--glass-border); border-radius: 8px; color: var(--text-primary); font-size: 0.95rem;">
                                </div>
                                <div>
                                    <label style="display: block; margin-bottom: 0.5rem; color: var(--text-secondary); font-size: 0.9rem; font-weight: 500;">New Password</label>
                                    <input type="password" name="new_password" placeholder="Enter new password" required style="width: 100%; padding: 0.75rem 1rem; background: rgba(0, 0, 0, 0.04); border: 1px solid var(--glass-border); border-radius: 8px; color: var(--text-primary); font-size: 0.95rem;">
                                </div>
                                <div>
                                    <label style="display: block; margin-bottom: 0.5rem; color: var(--text-secondary); font-size: 0.9rem; font-weight: 500;">Confirm New Password</label>
                                    <input type="password" name="confirm_password" placeholder="Confirm new password" required style="width: 100%; padding: 0.75rem 1rem; background: rgba(0, 0, 0, 0.04); border: 1px solid var(--glass-border); border-radius: 8px; color: var(--text-primary); font-size: 0.95rem;">
                                </div>
                                <button type="submit" style="padding: 0.875rem 2rem; background: var(--primary-color); color: white; border: none; border-radius: 8px; cursor: pointer; font-weight: 500; width: fit-content; margin-top: 1rem;">
                                    Update Password
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </section>
        </div>
    </main>

<script>
function generateSitemap() {
    fetch('/admin/generate-sitemap.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            csrf_token: '<?php echo Security::getInstance()->generateCSRFToken(); ?>'
        })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            alert('Sitemap generated successfully!');
            location.reload();
        } else {
            alert('Error: ' + (data.error || 'Failed to generate sitemap'));
        }
    });
}
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>

