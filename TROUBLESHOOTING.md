# Troubleshooting 500 Internal Server Error

## Quick Fix Steps

### Step 1: Check PHP Syntax
Run this in your browser: `https://timeline.co.zw/install-debug.php`

This will show:
- PHP version
- Missing extensions
- File permissions
- Syntax errors
- Any execution errors

### Step 2: Common Issues

#### Issue: PHP Version Too Old
**Solution**: Ensure PHP 7.4 or higher is installed

#### Issue: Missing Extensions
**Solution**: Enable these PHP extensions:
- `pdo`
- `pdo_mysql`
- `curl`
- `session`

#### Issue: File Permissions
**Solution**: Set these permissions:
```bash
chmod 755 config/
chmod 644 install.php
chmod 644 .htaccess
```

#### Issue: .htaccess Blocking
**Solution**: Temporarily rename `.htaccess` to `.htaccess.bak` and try again

#### Issue: Config Directory Missing
**Solution**: Create it manually:
```bash
mkdir config
chmod 755 config
```

### Step 3: Check Server Error Logs
Look in your server's error log (usually in cPanel or server logs) for the specific error message.

### Step 4: Enable Error Display (Temporary)
If you have access to php.ini or .htaccess, temporarily enable error display:
```apache
php_flag display_errors On
php_value error_reporting E_ALL
```

### Step 5: Test Direct Access
Try accessing install.php directly:
- `https://timeline.co.zw/install.php` (with .htaccess)
- `https://timeline.co.zw/install.php?step=1` (direct)

## Alternative: Manual Installation

If installer continues to fail, you can install manually:

1. **Create config/config.php** manually with your database credentials
2. **Run migrations** via admin panel after login
3. **Create admin user** via database directly

## Contact Support

If issues persist, check:
1. Server error logs
2. PHP error logs
3. Apache/Nginx error logs
4. File permissions
5. PHP version and extensions

