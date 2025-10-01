# 🔧 Troubleshooting Guide - Timeline.co.zw

## 🚨 **500 Internal Server Error - PHP 8.1**

### **Quick Fix Steps:**

#### **Step 1: Test Basic PHP**
Visit: `https://timeline.co.zw/simple-test.php`
- This will show if PHP is working at all
- Check for any error messages

#### **Step 2: Run Minimal Installation**
Visit: `https://timeline.co.zw/minimal-install.php`
- Simple form with just YouTube API key
- Creates basic .env file
- Tests file permissions

#### **Step 3: Check Server Logs**
Look for these common issues in your server error logs:

```bash
# Common error patterns to look for:
- "Call to undefined function"
- "Parse error"
- "Fatal error"
- "Permission denied"
- "No such file or directory"
```

### **Common Causes & Solutions:**

#### **1. Missing Directories**
**Error:** `No such file or directory: config/installed.lock`

**Solution:**
```bash
# Create required directories
mkdir -p config cache logs
chmod 755 config cache logs
```

#### **2. File Permissions**
**Error:** `Permission denied`

**Solution:**
```bash
# Set proper permissions
chmod 755 .
chmod 644 *.php
chmod 644 .htaccess
chmod 755 config/
chmod 755 cache/
chmod 755 logs/
```

#### **3. PHP Extensions Missing**
**Error:** `Call to undefined function`

**Required Extensions:**
- `curl` - for API calls
- `json` - for JSON handling
- `mbstring` - for string functions
- `openssl` - for HTTPS

**Check Extensions:**
```bash
php -m | grep -E "(curl|json|mbstring|openssl)"
```

#### **4. .htaccess Issues**
**Error:** `500 Internal Server Error` on all pages

**Solution:**
```bash
# Temporarily rename .htaccess
mv .htaccess .htaccess.backup

# Test if site loads
# If it works, the issue is in .htaccess
```

#### **5. PHP 8.1 Compatibility**
**Fixed Issues:**
- ✅ Updated `list()` to `[]` syntax
- ✅ Added directory creation checks
- ✅ Improved error handling

### **Step-by-Step Debugging:**

#### **Method 1: Progressive Testing**
1. **Test Basic PHP:**
   ```
   https://timeline.co.zw/simple-test.php
   ```

2. **Test Minimal Install:**
   ```
   https://timeline.co.zw/minimal-install.php
   ```

3. **Test Full Install:**
   ```
   https://timeline.co.zw/install.php
   ```

4. **Test Main Site:**
   ```
   https://timeline.co.zw/index.php
   ```

#### **Method 2: Server Log Analysis**
Check your server's error log for specific errors:

**Apache:**
```bash
tail -f /var/log/apache2/error.log
# or
tail -f /var/log/httpd/error_log
```

**Nginx:**
```bash
tail -f /var/log/nginx/error.log
```

**cPanel/Hosting Panel:**
- Look for "Error Logs" section
- Check "Raw Access Logs"

#### **Method 3: Manual Configuration**
If installation fails, create files manually:

1. **Create .env file:**
   ```bash
   nano .env
   ```
   Add:
   ```
   YOUTUBE_API_KEY=your_api_key_here
   SITE_URL=https://timeline.co.zw
   SITE_NAME=Timeline.co.zw
   ```

2. **Create lock file:**
   ```bash
   echo "$(date)" > config/installed.lock
   ```

3. **Set permissions:**
   ```bash
   chmod 644 .env
   chmod 644 config/installed.lock
   ```

### **Hosting-Specific Solutions:**

#### **cPanel/Shared Hosting:**
1. **Check PHP Version:**
   - Go to "Select PHP Version"
   - Ensure PHP 8.1 is selected
   - Enable required extensions

2. **File Manager:**
   - Upload files via File Manager
   - Set permissions: 644 for files, 755 for folders

3. **Error Logs:**
   - Check "Error Logs" in cPanel
   - Look for specific error messages

#### **VPS/Dedicated Server:**
1. **Check PHP Installation:**
   ```bash
   php -v
   php -m | grep -E "(curl|json|mbstring|openssl)"
   ```

2. **Check Web Server:**
   ```bash
   # Apache
   systemctl status apache2
   
   # Nginx
   systemctl status nginx
   ```

3. **Check Permissions:**
   ```bash
   ls -la /path/to/your/site/
   ```

### **Emergency Recovery:**

#### **If Nothing Works:**
1. **Backup Current Files:**
   ```bash
   cp -r /path/to/site /path/to/backup
   ```

2. **Start Fresh:**
   - Delete all files except test files
   - Upload fresh copy
   - Run minimal installation

3. **Contact Hosting Support:**
   - Provide error logs
   - Mention PHP 8.1 compatibility
   - Ask about required extensions

### **Test Files Created:**

1. **`simple-test.php`** - Basic PHP functionality test
2. **`minimal-install.php`** - Simple installation form
3. **`test.php`** - Comprehensive system check
4. **`diagnose.php`** - Full diagnostic tool

### **Quick Commands:**

```bash
# Create directories
mkdir -p config cache logs

# Set permissions
chmod 755 config cache logs
chmod 644 *.php .htaccess

# Test PHP syntax
php -l index.php
php -l install.php

# Check extensions
php -m | grep -E "(curl|json|mbstring|openssl)"
```

### **Still Having Issues?**

1. **Check Server Logs** - Most important step
2. **Test with Simple Files** - Use test files provided
3. **Contact Hosting Support** - They can check server logs
4. **Try Different PHP Version** - Test with PHP 7.4 or 8.0

---

**Remember:** The 500 error is usually a server configuration issue, not a code issue. The test files will help identify the exact problem.
