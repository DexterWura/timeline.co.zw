# Simple Installation Guide (If Installer Fails)

If you're getting 500 errors, follow these steps:

## Step 1: Test PHP

1. Access: `https://timeline.co.zw/test.php` or `https://timeline.co.zw/phpinfo.php`
2. If these don't work, PHP might not be configured correctly on your server

## Step 2: Check Server Requirements

Your server needs:
- PHP 7.0 or higher (7.4+ recommended)
- MySQL 5.7+ or MariaDB 10.2+
- mod_rewrite enabled
- PDO MySQL extension
- cURL extension

## Step 3: Manual Installation

If the installer won't work, you can install manually:

### 1. Create config/config.php

Create the file `config/config.php` with this content:

```php
<?php
define('DB_HOST', 'localhost');
define('DB_NAME', 'your_database_name');
define('DB_USER', 'your_database_user');
define('DB_PASS', 'your_database_password');
define('DB_CHARSET', 'utf8mb4');

define('APP_NAME', 'Timeline.co.zw');
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
define('APP_URL', $protocol . '://' . $_SERVER['HTTP_HOST']);
define('APP_ENV', 'production');

define('SESSION_LIFETIME', 3600);
define('PASSWORD_MIN_LENGTH', 8);
define('CSRF_TOKEN_NAME', 'csrf_token');

define('YOUTUBE_API_KEY', '');
define('ADSENSE_CLIENT_ID', '');
define('NEWS_API_KEY', '');
define('LASTFM_API_KEY', '');
define('SPOTIFY_CLIENT_ID', '');
define('SPOTIFY_CLIENT_SECRET', '');

define('CACHE_DURATION', 259200);
define('CACHE_DIR', __DIR__ . '/../cache/');

define('BASE_PATH', dirname(__DIR__));
define('CLASSES_PATH', BASE_PATH . '/classes');
define('MIGRATIONS_PATH', BASE_PATH . '/database/migrations');
define('UPLOADS_PATH', BASE_PATH . '/uploads');

if (APP_ENV === 'development') {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

date_default_timezone_set('UTC');
```

### 2. Create Database

Create a MySQL database and user with appropriate permissions.

### 3. Run Migrations

After creating config.php, you can run migrations via the admin panel:
1. Login to `/admin/login.php` (you'll need to create an admin user manually first)
2. Go to Migrations page
3. Click "Run Pending Migrations"

### 4. Create Admin User Manually

Run this SQL in your database (replace email and password):

```sql
INSERT INTO users (email, password, role, created_at) 
VALUES (
    'admin@example.com',
    '$2y$10$YourHashedPasswordHere',
    'admin',
    NOW()
);
```

To generate the password hash, use PHP:
```php
<?php echo password_hash('your_password', PASSWORD_DEFAULT); ?>
```

## Step 4: Set Permissions

```bash
chmod 755 config/
chmod 644 config/config.php
chmod 755 cache/
chmod 755 uploads/
chmod 755 logs/
```

## Troubleshooting

### If test.php doesn't work:
- Check PHP is installed and running
- Check file permissions
- Check server error logs

### If .htaccess is causing issues:
- Temporarily rename `.htaccess` to `.htaccess.bak`
- Try accessing install.php again
- If it works, the issue is with .htaccess rules

### Check Error Logs:
- cPanel: Error Logs section
- DirectAdmin: Error Log
- Or check `/var/log/apache2/error.log` or similar

