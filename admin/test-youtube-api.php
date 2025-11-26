<?php
/**
 * Test YouTube API Connection
 * This page helps diagnose YouTube API issues
 */
require_once __DIR__ . '/../bootstrap.php';

$auth = new Auth();
if (!$auth->isAdmin()) {
    die('Unauthorized');
}

$settings = new Settings();
$youtubeKey = $settings->get('youtube_api_key');
$error = '';
$success = '';
$testResult = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['test_api'])) {
    if (empty($youtubeKey)) {
        $error = 'No YouTube API key configured. Please add it in Settings first.';
    } else {
        // Test the API call
        $url = "https://www.googleapis.com/youtube/v3/videos?part=snippet,statistics&chart=mostPopular&videoCategoryId=10&maxResults=5&key={$youtubeKey}";
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);
        
        if ($curlError) {
            $error = "cURL Error: {$curlError}";
        } elseif ($httpCode !== 200) {
            $errorData = json_decode($response, true);
            $errorMessage = $errorData['error']['message'] ?? "Unknown error";
            $errorReason = $errorData['error']['errors'][0]['reason'] ?? "";
            $error = "HTTP {$httpCode}: {$errorMessage}";
            if ($errorReason) {
                $error .= " (Reason: {$errorReason})";
            }
            $testResult = [
                'http_code' => $httpCode,
                'error' => $errorData['error'] ?? null,
                'raw_response' => $response
            ];
        } else {
            $data = json_decode($response, true);
            if (isset($data['items']) && !empty($data['items'])) {
                $success = "Success! Fetched " . count($data['items']) . " videos from YouTube API.";
                $testResult = [
                    'http_code' => $httpCode,
                    'items_count' => count($data['items']),
                    'sample_item' => $data['items'][0] ?? null,
                    'page_info' => $data['pageInfo'] ?? null
                ];
            } else {
                $error = "API returned 200 but no items in response.";
                $testResult = [
                    'http_code' => $httpCode,
                    'raw_response' => $response
                ];
            }
        }
    }
}

include __DIR__ . '/includes/header.php';
?>

<main class="main-content">
    <header class="top-bar">
        <div class="top-bar-left">
            <h2 class="page-title">Test YouTube API Connection</h2>
        </div>
    </header>

    <div class="dashboard-content">
        <div class="info-card">
            <div class="card-header">
                <h3>YouTube Data API v3 Test</h3>
            </div>
            <div class="card-body">
                <div style="margin-bottom: 2rem;">
                    <p><strong>API Key Status:</strong> 
                        <?php if ($youtubeKey): ?>
                            <span style="color: #00d4aa;">✓ Configured</span>
                            <br><small style="color: #666;">Key: <?php echo substr($youtubeKey, 0, 10) . '...' . substr($youtubeKey, -5); ?></small>
                        <?php else: ?>
                            <span style="color: #e74c3c;">✗ Not configured</span>
                            <br><a href="/admin/settings.php">Configure API Key</a>
                        <?php endif; ?>
                    </p>
                </div>

                <?php if ($error): ?>
                    <div style="background: rgba(231, 76, 60, 0.1); color: #e74c3c; padding: 1rem; border-radius: 8px; margin-bottom: 1rem; border-left: 4px solid #e74c3c;">
                        <strong>Error:</strong> <?php echo htmlspecialchars($error); ?>
                    </div>
                <?php endif; ?>

                <?php if ($success): ?>
                    <div style="background: rgba(0, 212, 170, 0.1); color: #00d4aa; padding: 1rem; border-radius: 8px; margin-bottom: 1rem; border-left: 4px solid #00d4aa;">
                        <strong>Success:</strong> <?php echo htmlspecialchars($success); ?>
                    </div>
                <?php endif; ?>

                <?php if ($testResult): ?>
                    <div style="background: rgba(0, 0, 0, 0.02); padding: 1rem; border-radius: 8px; margin-bottom: 1rem;">
                        <h4>Test Results:</h4>
                        <pre style="background: #1a1a1a; color: #00d4aa; padding: 1rem; border-radius: 4px; overflow-x: auto; font-size: 0.85rem;"><?php echo htmlspecialchars(json_encode($testResult, JSON_PRETTY_PRINT)); ?></pre>
                    </div>
                <?php endif; ?>

                <form method="POST">
                    <button type="submit" name="test_api" style="padding: 0.875rem 2rem; background: var(--primary-color); color: white; border: none; border-radius: 8px; cursor: pointer; font-weight: 500;">
                        <i class="fa-solid fa-flask"></i> Test YouTube API Connection
                    </button>
                </form>

                <div style="margin-top: 2rem; padding: 1rem; background: rgba(0, 0, 0, 0.02); border-radius: 8px;">
                    <h4>API Endpoint Being Tested:</h4>
                    <code style="background: #1a1a1a; color: #00d4aa; padding: 0.5rem; border-radius: 4px; display: block; margin-top: 0.5rem;">
                        GET https://www.googleapis.com/youtube/v3/videos?part=snippet,statistics&chart=mostPopular&videoCategoryId=10&maxResults=5&key=YOUR_KEY
                    </code>
                    <p style="margin-top: 1rem; font-size: 0.9rem; color: #666;">
                        This endpoint fetches the most popular music videos (category 10) from YouTube.
                    </p>
                </div>
            </div>
        </div>
    </div>
</main>

<?php include __DIR__ . '/includes/footer.php'; ?>

