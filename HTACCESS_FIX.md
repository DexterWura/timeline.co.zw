# .htaccess Fix Applied

## Problem
The original `.htaccess` file was using Apache 2.2 syntax (`Order allow,deny`) which can cause 500 errors on Apache 2.4+ servers or certain shared hosting configurations.

## Solution
Updated `.htaccess` to use:
- **Apache 2.4+ syntax** (`Require all denied/granted`) as primary
- **Apache 2.2 fallback** (`Order allow,deny`) for compatibility

## What Changed

1. **Security directives** now use `<IfModule mod_authz_core.c>` to detect Apache 2.4+
2. **Fallback** to Apache 2.2 syntax if mod_authz_core is not available
3. **Removed `<DirectoryMatch>`** directives (not always supported on shared hosting)
4. **Simplified** cache/uploads protection (handled by .htaccess files in those directories)

## Testing

After renaming `.htaccess.bak` back to `.htaccess`, test:

1. ✅ `https://timeline.co.zw/install.php` - Should work
2. ✅ `https://timeline.co.zw/debug.php` - Should work  
3. ✅ `https://timeline.co.zw/` - Homepage should load
4. ✅ `https://timeline.co.zw/admin` - Should redirect to login

## If You Still Get 500 Errors

1. Check Apache version in cPanel or server info
2. Check error logs for specific Apache directive errors
3. You can temporarily disable security rules by commenting them out:
   ```apache
   # <FilesMatch "^(config|bootstrap|classes|database)">
   #     Require all denied
   # </FilesMatch>
   ```

## Notes

- The installer will create `.htaccess` files in `cache/` and `uploads/` directories
- These use the same Apache 2.4/2.2 compatible syntax
- All security rules are now compatible with both Apache versions

