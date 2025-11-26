<?php
// Minimal debug - outputs immediately
echo "1. PHP is working<br>";
echo "2. PHP Version: " . PHP_VERSION . "<br>";
echo "3. Script location: " . __FILE__ . "<br>";
echo "4. Document root: " . (isset($_SERVER['DOCUMENT_ROOT']) ? $_SERVER['DOCUMENT_ROOT'] : 'Not set') . "<br>";
echo "5. Current directory: " . __DIR__ . "<br>";
echo "6. install.php exists: " . (file_exists(__DIR__ . '/install.php') ? 'Yes' : 'No') . "<br>";
echo "7. .htaccess exists: " . (file_exists(__DIR__ . '/.htaccess') ? 'Yes' : 'No') . "<br>";
echo "8. config/ exists: " . (is_dir(__DIR__ . '/config') ? 'Yes' : 'No') . "<br>";
?>

