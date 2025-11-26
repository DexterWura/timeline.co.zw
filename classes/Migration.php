<?php
/**
 * Database Migration System (Flyways)
 */
class Migration {
    private $db;
    private $migrationsPath;
    
    public function __construct() {
        $this->db = Database::getInstance();
        $this->migrationsPath = MIGRATIONS_PATH;
        $this->ensureMigrationsTable();
    }
    
    private function ensureMigrationsTable() {
        try {
            $this->db->query("
                CREATE TABLE IF NOT EXISTS migrations (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    version VARCHAR(255) NOT NULL UNIQUE,
                    description TEXT,
                    executed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                )
            ");
        } catch (Exception $e) {
            // Table might already exist
        }
    }
    
    public function runMigrations() {
        $executedMigrations = $this->getExecutedMigrations();
        $migrationFiles = $this->getMigrationFiles();
        
        foreach ($migrationFiles as $file) {
            $version = $this->getVersionFromFilename($file);
            
            if (!in_array($version, $executedMigrations)) {
                // Silent mode for web installer
                if (php_sapi_name() !== 'cli') {
                    error_log("Running migration: {$file}");
                } else {
                    echo "Running migration: {$file}\n";
                }
                $this->executeMigration($file, $version);
            }
        }
    }
    
    private function getExecutedMigrations() {
        $results = $this->db->fetchAll("SELECT version FROM migrations ORDER BY version");
        return array_column($results, 'version');
    }
    
    public function getMigrationFiles() {
        if (!is_dir($this->migrationsPath)) {
            mkdir($this->migrationsPath, 0755, true);
        }
        
        $files = glob($this->migrationsPath . '/V*.php');
        sort($files);
        return $files;
    }
    
    private function getVersionFromFilename($filename) {
        $basename = basename($filename, '.php');
        return $basename;
    }
    
    private function executeMigration($file, $version) {
        try {
            $this->db->beginTransaction();
            
            require_once $file;
            
            // Extract class name from filename
            $className = $this->getClassNameFromFile($file);
            
            if (class_exists($className)) {
                $migration = new $className($this->db);
                $migration->up();
                
                // Record migration
                $this->db->insert('migrations', [
                    'version' => $version,
                    'description' => $migration->getDescription() ?? 'Migration'
                ]);
                
                $this->db->commit();
                if (php_sapi_name() === 'cli') {
                    echo "Migration {$version} executed successfully\n";
                }
            } else {
                throw new Exception("Migration class {$className} not found");
            }
        } catch (Exception $e) {
            $this->db->rollback();
            throw new Exception("Migration failed: " . $e->getMessage());
        }
    }
    
    private function getClassNameFromFile($file) {
        $content = file_get_contents($file);
        if (preg_match('/class\s+(\w+)/', $content, $matches)) {
            return $matches[1];
        }
        return 'Migration_' . basename($file, '.php');
    }
}

