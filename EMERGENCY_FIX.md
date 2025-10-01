# 🚨 Emergency Fix for 500 Error

## **Immediate Steps to Fix 500 Error:**

### **Step 1: Test Basic Server**
Visit: `https://timeline.co.zw/test.html`
- If this works → Server is OK, issue is with PHP
- If this fails → Server configuration problem

### **Step 2: Test Minimal PHP**
Visit: `https://timeline.co.zw/basic.php`
- If this works → PHP is OK, issue is with code
- If this fails → PHP configuration problem

### **Step 3: Disable .htaccess**
**Most Common Cause of 500 Errors:**

1. **Rename .htaccess file:**
   ```bash
   mv .htaccess .htaccess.backup
   ```

2. **Test again:**
   - Visit: `https://timeline.co.zw/basic.php`
   - If it works now → .htaccess was the problem

3. **Use minimal .htaccess:**
   ```bash
   cp .htaccess.minimal .htaccess
   ```

### **Step 4: Run Debug Tool**
Visit: `https://timeline.co.zw/debug.php`
- This will show exactly what's wrong
- Follow the troubleshooting steps shown

### **Step 5: Manual Installation**
If debug tool works, use it to install:
1. Enter your YouTube API key
2. Click "Test Installation"
3. Visit main site

## **Common 500 Error Causes:**

### **1. .htaccess Issues (90% of cases)**
**Symptoms:** All PHP files return 500 error
**Fix:** Rename .htaccess to .htaccess.backup

### **2. Missing PHP Extensions**
**Symptoms:** Specific function errors
**Fix:** Contact hosting provider to enable extensions

### **3. File Permissions**
**Symptoms:** Permission denied errors
**Fix:** 
```bash
chmod 755 config cache logs
chmod 644 *.php
```

### **4. PHP Version Issues**
**Symptoms:** Parse errors or fatal errors
**Fix:** Ensure PHP 7.4+ is enabled

## **Quick Commands:**

```bash
# Disable .htaccess
mv .htaccess .htaccess.backup

# Create directories
mkdir -p config cache logs

# Set permissions
chmod 755 config cache logs
chmod 644 *.php

# Test basic PHP
# Visit: https://timeline.co.zw/basic.php
```

## **If Nothing Works:**

1. **Check Server Error Logs**
   - Look in your hosting panel
   - Find the specific error message

2. **Contact Hosting Support**
   - Tell them you're getting 500 errors
   - Ask them to check error logs
   - Mention PHP 8.1 compatibility

3. **Try Different PHP Version**
   - Switch to PHP 7.4 or 8.0
   - Test if the error persists

## **Test Files Created:**

- `test.html` - Tests if server works (no PHP)
- `basic.php` - Tests if PHP works (minimal code)
- `debug.php` - Comprehensive diagnostic tool
- `.htaccess.minimal` - Safe .htaccess alternative

## **Expected Results:**

✅ **test.html** should always work
✅ **basic.php** should work if PHP is configured
✅ **debug.php** should work and show system status

If any of these fail, the issue is server configuration, not your code.

---

**Most likely fix:** Rename .htaccess to .htaccess.backup
