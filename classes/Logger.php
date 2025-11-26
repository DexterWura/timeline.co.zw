<?php
/**
 * Logger Class for Error and Activity Logging
 */
class Logger {
    private static $instance = null;
    private $logDir;
    private $maxLogSize = 10485760; // 10MB per file
    private $maxLogAge = 30; // 30 days
    private $logFile;
    
    private function __construct() {
        $this->logDir = __DIR__ . '/../logs';
        
        // Create logs directory if it doesn't exist
        if (!is_dir($this->logDir)) {
            @mkdir($this->logDir, 0755, true);
        }
        
        // Set log file path
        $this->logFile = $this->logDir . '/app_' . date('Y-m-d') . '.log';
        
        // Clean old logs on first use
        $this->cleanOldLogs();
    }
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Log an error message
     */
    public function error($message, $context = []) {
        $this->writeLog('ERROR', $message, $context);
    }
    
    /**
     * Log a warning message
     */
    public function warning($message, $context = []) {
        $this->writeLog('WARNING', $message, $context);
    }
    
    /**
     * Log an info message
     */
    public function info($message, $context = []) {
        $this->writeLog('INFO', $message, $context);
    }
    
    /**
     * Log a debug message
     */
    public function debug($message, $context = []) {
        if (defined('APP_ENV') && APP_ENV === 'development') {
            $this->writeLog('DEBUG', $message, $context);
        }
    }
    
    /**
     * Write log entry to file
     */
    private function writeLog($level, $message, $context = []) {
        try {
            // Rotate log if file is too large
            if (file_exists($this->logFile) && filesize($this->logFile) > $this->maxLogSize) {
                $this->rotateLog();
            }
            
            $timestamp = date('Y-m-d H:i:s');
            $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
            $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';
            $userId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 'guest';
            $url = $_SERVER['REQUEST_URI'] ?? 'unknown';
            
            $logEntry = [
                'timestamp' => $timestamp,
                'level' => $level,
                'message' => $message,
                'ip' => $ip,
                'user_id' => $userId,
                'url' => $url,
                'user_agent' => $userAgent
            ];
            
            if (!empty($context)) {
                $logEntry['context'] = $context;
            }
            
            // Add stack trace for errors
            if ($level === 'ERROR') {
                $logEntry['trace'] = $this->getStackTrace();
            }
            
            $logLine = json_encode($logEntry) . PHP_EOL;
            
            @file_put_contents($this->logFile, $logLine, FILE_APPEND | LOCK_EX);
        } catch (Exception $e) {
            // Fallback to PHP error_log if file writing fails
            error_log("Logger error: " . $e->getMessage());
        }
    }
    
    /**
     * Get stack trace for errors
     */
    private function getStackTrace() {
        $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 10);
        $formatted = [];
        foreach ($trace as $index => $frame) {
            if ($index === 0) continue; // Skip Logger::writeLog
            $formatted[] = [
                'file' => $frame['file'] ?? 'unknown',
                'line' => $frame['line'] ?? 0,
                'function' => $frame['function'] ?? 'unknown',
                'class' => $frame['class'] ?? null
            ];
        }
        return $formatted;
    }
    
    /**
     * Rotate log file when it gets too large
     */
    private function rotateLog() {
        $backupFile = $this->logFile . '.' . time() . '.bak';
        if (file_exists($this->logFile)) {
            @rename($this->logFile, $backupFile);
        }
    }
    
    /**
     * Clean old log files
     */
    public function cleanOldLogs() {
        try {
            $files = glob($this->logDir . '/*.log*');
            $cutoffTime = time() - ($this->maxLogAge * 24 * 60 * 60);
            
            foreach ($files as $file) {
                if (is_file($file) && filemtime($file) < $cutoffTime) {
                    @unlink($file);
                }
            }
        } catch (Exception $e) {
            error_log("Log cleanup error: " . $e->getMessage());
        }
    }
    
    /**
     * Get log files list
     */
    public function getLogFiles() {
        $files = glob($this->logDir . '/*.log*');
        $logFiles = [];
        
        foreach ($files as $file) {
            if (is_file($file)) {
                $logFiles[] = [
                    'name' => basename($file),
                    'path' => $file,
                    'size' => filesize($file),
                    'modified' => filemtime($file),
                    'size_formatted' => $this->formatBytes(filesize($file))
                ];
            }
        }
        
        // Sort by modified time, newest first
        usort($logFiles, function($a, $b) {
            return $b['modified'] - $a['modified'];
        });
        
        return $logFiles;
    }
    
    /**
     * Read log file content
     */
    public function readLogFile($filename, $lines = 1000) {
        $filepath = $this->logDir . '/' . basename($filename);
        
        // Security check - ensure file is in log directory
        if (!file_exists($filepath) || strpos(realpath($filepath), realpath($this->logDir)) !== 0) {
            throw new Exception('Invalid log file');
        }
        
        $content = [];
        $handle = @fopen($filepath, 'r');
        
        if ($handle) {
            // Read file line by line from end
            $lineCount = 0;
            $buffer = '';
            fseek($handle, -1, SEEK_END);
            
            while ($lineCount < $lines) {
                $char = fgetc($handle);
                if ($char === "\n" || ftell($handle) <= 0) {
                    if (!empty($buffer)) {
                        $content[] = trim($buffer);
                        $lineCount++;
                        $buffer = '';
                    }
                    if (ftell($handle) <= 0) break;
                } else {
                    $buffer = $char . $buffer;
                }
                fseek($handle, -2, SEEK_CUR);
            }
            
            fclose($handle);
            $content = array_reverse($content);
        }
        
        return $content;
    }
    
    /**
     * Format bytes to human readable format
     */
    private function formatBytes($bytes, $precision = 2) {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, $precision) . ' ' . $units[$i];
    }
    
    /**
     * Delete a log file
     */
    public function deleteLogFile($filename) {
        $filepath = $this->logDir . '/' . basename($filename);
        
        // Security check
        if (!file_exists($filepath) || strpos(realpath($filepath), realpath($this->logDir)) !== 0) {
            throw new Exception('Invalid log file');
        }
        
        return @unlink($filepath);
    }
    
    /**
     * Get log statistics
     */
    public function getLogStats() {
        $files = $this->getLogFiles();
        $totalSize = 0;
        $errorCount = 0;
        $warningCount = 0;
        $infoCount = 0;
        
        foreach ($files as $file) {
            $totalSize += $file['size'];
            
            // Count log levels in current day's log
            if (strpos($file['name'], date('Y-m-d')) !== false) {
                $content = $this->readLogFile($file['name'], 10000);
                foreach ($content as $line) {
                    $log = json_decode($line, true);
                    if ($log) {
                        switch ($log['level'] ?? '') {
                            case 'ERROR':
                                $errorCount++;
                                break;
                            case 'WARNING':
                                $warningCount++;
                                break;
                            case 'INFO':
                                $infoCount++;
                                break;
                        }
                    }
                }
            }
        }
        
        return [
            'total_files' => count($files),
            'total_size' => $totalSize,
            'total_size_formatted' => $this->formatBytes($totalSize),
            'today_errors' => $errorCount,
            'today_warnings' => $warningCount,
            'today_info' => $infoCount
        ];
    }
}

