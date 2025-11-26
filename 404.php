<?php
require_once __DIR__ . '/bootstrap.php';

$seo = new SEO();
$seo->setTitle('404 - Page Not Found')
    ->setDescription('The page you are looking for could not be found.')
    ->setKeywords(['404', 'not found', 'error']);

http_response_code(404);
include __DIR__ . '/includes/header.php';
?>

<main class="main-container">
    <div class="content">
        <div class="empty-state" style="text-align: center; padding: 4rem 2rem;">
            <h1 style="font-size: 6rem; font-weight: 800; color: var(--primary-color); margin-bottom: 1rem;">404</h1>
            <h2 style="font-size: 2rem; margin-bottom: 1rem; color: var(--text-primary);">Page Not Found</h2>
            <p style="font-size: 1.1rem; color: var(--text-secondary); margin-bottom: 2rem;">
                The page you are looking for might have been removed, had its name changed, or is temporarily unavailable.
            </p>
            <div style="display: flex; gap: 1rem; justify-content: center; flex-wrap: wrap;">
                <a href="/index.php" class="btn btn-primary" style="padding: 0.875rem 2rem;">
                    <i class="fas fa-home"></i> Go to Homepage
                </a>
                <a href="javascript:history.back()" class="btn btn-secondary" style="padding: 0.875rem 2rem;">
                    <i class="fas fa-arrow-left"></i> Go Back
                </a>
            </div>
        </div>
    </div>
</main>

<?php include __DIR__ . '/includes/footer.php'; ?>

