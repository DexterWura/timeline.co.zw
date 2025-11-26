<?php
require_once __DIR__ . '/../bootstrap.php';

$pageTitle = 'System Logs';
$auth = new Auth();
$auth->requireAdmin(); // Only admins can view logs

$logger = Logger::getInstance();
$error = '';
$success = '';

// Handle actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $security = Security::getInstance();
    $security->requireCSRF();
    
    $action = $_POST['action'] ?? '';
    
    if ($action === 'delete_log') {
        $filename = $_POST['filename'] ?? '';
        if ($filename) {
            try {
                $logger->deleteLogFile($filename);
                $success = 'Log file deleted successfully!';
            } catch (Exception $e) {
                $error = 'Error deleting log file: ' . $e->getMessage();
            }
        }
    } elseif ($action === 'clean_old_logs') {
        try {
            $logger->cleanOldLogs();
            $success = 'Old log files cleaned successfully!';
        } catch (Exception $e) {
            $error = 'Error cleaning logs: ' . $e->getMessage();
        }
    }
}

// Get log files
$logFiles = $logger->getLogFiles();
$logStats = $logger->getLogStats();

// Get selected log file content
$selectedFile = $_GET['file'] ?? '';
$logContent = [];
$currentFile = null;

if ($selectedFile) {
    try {
        $lines = (int)($_GET['lines'] ?? 500);
        $logContent = $logger->readLogFile($selectedFile, $lines);
        $currentFile = basename($selectedFile);
    } catch (Exception $e) {
        $error = 'Error reading log file: ' . $e->getMessage();
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
            <h2 class="page-title">System Logs</h2>
        </div>
        <div class="top-bar-right">
            <form method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to clean all old log files?');">
                <input type="hidden" name="action" value="clean_old_logs">
                <input type="hidden" name="csrf_token" value="<?php echo Security::getInstance()->generateCSRFToken(); ?>">
                <button type="submit" class="notification-btn btn-secondary" style="margin-right: 1rem;">
                    <i class="fa-solid fa-broom"></i> Clean Old Logs
                </button>
            </form>
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
        <span class="breadcrumb-current">System Logs</span>
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

        <!-- Log Statistics -->
        <section class="additional-cards">
            <div class="info-card">
                <div class="card-header">
                    <h3>Log Statistics</h3>
                </div>
                <div class="card-body">
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem;">
                        <div style="padding: 1rem; background: rgba(0, 0, 0, 0.02); border-radius: 8px;">
                            <div style="font-size: 0.85rem; color: var(--text-secondary); margin-bottom: 0.5rem;">Total Log Files</div>
                            <div style="font-size: 1.5rem; font-weight: 600; color: var(--text-primary);"><?php echo $logStats['total_files']; ?></div>
                        </div>
                        <div style="padding: 1rem; background: rgba(0, 0, 0, 0.02); border-radius: 8px;">
                            <div style="font-size: 0.85rem; color: var(--text-secondary); margin-bottom: 0.5rem;">Total Size</div>
                            <div style="font-size: 1.5rem; font-weight: 600; color: var(--text-primary);"><?php echo $logStats['total_size_formatted']; ?></div>
                        </div>
                        <div style="padding: 1rem; background: rgba(231, 76, 60, 0.1); border-radius: 8px; border-left: 4px solid #e74c3c;">
                            <div style="font-size: 0.85rem; color: var(--text-secondary); margin-bottom: 0.5rem;">Today's Errors</div>
                            <div style="font-size: 1.5rem; font-weight: 600; color: #e74c3c;"><?php echo $logStats['today_errors']; ?></div>
                        </div>
                        <div style="padding: 1rem; background: rgba(241, 196, 15, 0.1); border-radius: 8px; border-left: 4px solid #f1c40f;">
                            <div style="font-size: 0.85rem; color: var(--text-secondary); margin-bottom: 0.5rem;">Today's Warnings</div>
                            <div style="font-size: 1.5rem; font-weight: 600; color: #f1c40f;"><?php echo $logStats['today_warnings']; ?></div>
                        </div>
                        <div style="padding: 1rem; background: rgba(52, 152, 219, 0.1); border-radius: 8px; border-left: 4px solid #3498db;">
                            <div style="font-size: 0.85rem; color: var(--text-secondary); margin-bottom: 0.5rem;">Today's Info</div>
                            <div style="font-size: 1.5rem; font-weight: 600; color: #3498db;"><?php echo $logStats['today_info']; ?></div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <div style="display: grid; grid-template-columns: 300px 1fr; gap: 2rem; margin-top: 2rem;">
            <!-- Log Files List -->
            <div class="info-card">
                <div class="card-header">
                    <h3>Log Files</h3>
                </div>
                <div class="card-body" style="padding: 0;">
                    <div style="max-height: 600px; overflow-y: auto;">
                        <?php if (empty($logFiles)): ?>
                            <div style="padding: 2rem; text-align: center; color: var(--text-secondary);">
                                No log files found
                            </div>
                        <?php else: ?>
                            <?php foreach ($logFiles as $file): ?>
                                <a href="?file=<?php echo urlencode($file['name']); ?>" 
                                   style="display: block; padding: 1rem; border-bottom: 1px solid var(--glass-border); text-decoration: none; color: var(--text-primary); transition: background 0.2s; <?php echo $currentFile === $file['name'] ? 'background: rgba(0, 212, 170, 0.1);' : ''; ?>"
                                   onmouseover="this.style.background='rgba(0, 0, 0, 0.05)'"
                                   onmouseout="this.style.background='<?php echo $currentFile === $file['name'] ? 'rgba(0, 212, 170, 0.1)' : 'transparent'; ?>'">
                                    <div style="font-weight: 600; margin-bottom: 0.25rem;"><?php echo htmlspecialchars($file['name']); ?></div>
                                    <div style="font-size: 0.85rem; color: var(--text-secondary);">
                                        <?php echo $file['size_formatted']; ?> • <?php echo date('M d, Y H:i', $file['modified']); ?>
                                    </div>
                                </a>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Log Content -->
            <div class="info-card">
                <div class="card-header">
                    <h3><?php echo $currentFile ? htmlspecialchars($currentFile) : 'Select a log file'; ?></h3>
                    <?php if ($currentFile): ?>
                        <div style="display: flex; gap: 0.5rem;">
                            <a href="?file=<?php echo urlencode($currentFile); ?>&lines=100" class="btn-outline-secondary btn-sm">100 lines</a>
                            <a href="?file=<?php echo urlencode($currentFile); ?>&lines=500" class="btn-outline-secondary btn-sm">500 lines</a>
                            <a href="?file=<?php echo urlencode($currentFile); ?>&lines=1000" class="btn-outline-secondary btn-sm">1000 lines</a>
                            <form method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this log file?');">
                                <input type="hidden" name="action" value="delete_log">
                                <input type="hidden" name="csrf_token" value="<?php echo Security::getInstance()->generateCSRFToken(); ?>">
                                <input type="hidden" name="filename" value="<?php echo htmlspecialchars($currentFile); ?>">
                                <button type="submit" class="btn-outline-danger btn-sm">
                                    <i class="fa-solid fa-trash"></i> Delete
                                </button>
                            </form>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="card-body">
                    <?php if (empty($logContent)): ?>
                        <div style="padding: 3rem; text-align: center; color: var(--text-secondary);">
                            <?php if ($currentFile): ?>
                                No log entries found in this file
                            <?php else: ?>
                                Select a log file from the list to view its contents
                            <?php endif; ?>
                        </div>
                    <?php else: ?>
                        <div style="background: #1e1e1e; color: #d4d4d4; padding: 1rem; border-radius: 8px; font-family: 'Courier New', monospace; font-size: 0.85rem; max-height: 600px; overflow-y: auto;">
                            <?php foreach ($logContent as $line): ?>
                                <?php 
                                $log = json_decode($line, true);
                                if ($log):
                                    $level = $log['level'] ?? 'INFO';
                                    $levelColors = [
                                        'ERROR' => '#e74c3c',
                                        'WARNING' => '#f1c40f',
                                        'INFO' => '#3498db',
                                        'DEBUG' => '#95a5a6'
                                    ];
                                    $color = $levelColors[$level] ?? '#95a5a6';
                                ?>
                                    <div style="margin-bottom: 1rem; padding: 0.75rem; background: rgba(255, 255, 255, 0.02); border-radius: 4px; border-left: 3px solid <?php echo $color; ?>;">
                                        <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                                            <span style="color: <?php echo $color; ?>; font-weight: 600;">[<?php echo htmlspecialchars($level); ?>]</span>
                                            <span style="color: #95a5a6; font-size: 0.8rem;"><?php echo htmlspecialchars($log['timestamp'] ?? ''); ?></span>
                                        </div>
                                        <div style="color: #d4d4d4; margin-bottom: 0.5rem;">
                                            <?php echo htmlspecialchars($log['message'] ?? ''); ?>
                                        </div>
                                        <div style="font-size: 0.75rem; color: #7f8c8d; display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 0.5rem;">
                                            <div><strong>IP:</strong> <?php echo htmlspecialchars($log['ip'] ?? 'unknown'); ?></div>
                                            <div><strong>User ID:</strong> <?php echo htmlspecialchars($log['user_id'] ?? 'guest'); ?></div>
                                            <div><strong>URL:</strong> <?php echo htmlspecialchars(substr($log['url'] ?? 'unknown', 0, 50)); ?></div>
                                        </div>
                                        <?php if (!empty($log['context'])): ?>
                                            <details style="margin-top: 0.5rem;">
                                                <summary style="cursor: pointer; color: #3498db; font-size: 0.8rem;">Context</summary>
                                                <pre style="margin-top: 0.5rem; padding: 0.5rem; background: rgba(0, 0, 0, 0.3); border-radius: 4px; overflow-x: auto; font-size: 0.75rem;"><?php echo htmlspecialchars(json_encode($log['context'], JSON_PRETTY_PRINT)); ?></pre>
                                            </details>
                                        <?php endif; ?>
                                        <?php if (!empty($log['trace'])): ?>
                                            <details style="margin-top: 0.5rem;">
                                                <summary style="cursor: pointer; color: #e74c3c; font-size: 0.8rem;">Stack Trace</summary>
                                                <pre style="margin-top: 0.5rem; padding: 0.5rem; background: rgba(0, 0, 0, 0.3); border-radius: 4px; overflow-x: auto; font-size: 0.75rem;"><?php echo htmlspecialchars(json_encode($log['trace'], JSON_PRETTY_PRINT)); ?></pre>
                                            </details>
                                        <?php endif; ?>
                                    </div>
                                <?php else: ?>
                                    <div style="color: #7f8c8d; font-size: 0.8rem; margin-bottom: 0.5rem;">
                                        <?php echo htmlspecialchars($line); ?>
                                    </div>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</main>

<?php include __DIR__ . '/includes/footer.php'; ?>

