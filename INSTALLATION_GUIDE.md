# 🚀 Installation Guide - Timeline.co.zw

## Quick Start

### 1. **Upload Files**
Upload all files to your web server's document root directory.

### 2. **Set Permissions**
```bash
chmod 755 cache/
chmod 644 *.php
chmod 644 .htaccess
```

### 3. **Run Installation**
Visit your website URL - it will automatically redirect to the installation wizard.

### 4. **Configure**
Fill in the installation form with:
- **YouTube API Key** (required)
- **Google Analytics ID** (optional)
- **Site URL** (auto-detected)
- **Admin Email** (optional)

### 5. **Done!**
Your site will be ready to use!

## Detailed Setup

### Prerequisites
- **PHP 7.4+** with extensions: curl, json, mbstring, openssl
- **Web Server** (Apache/Nginx)
- **YouTube API Key** from Google Cloud Console

### Getting YouTube API Key

1. **Go to Google Cloud Console**
   - Visit: https://console.cloud.google.com/

2. **Create/Select Project**
   - Create a new project or select existing one

3. **Enable YouTube Data API v3**
   - Go to "APIs & Services" > "Library"
   - Search for "YouTube Data API v3"
   - Click "Enable"

4. **Create API Key**
   - Go to "APIs & Services" > "Credentials"
   - Click "Create Credentials" > "API Key"
   - Copy the generated key

5. **Restrict API Key (Recommended)**
   - Click on your API key
   - Under "Application restrictions", select "HTTP referrers"
   - Add your domain: `https://timeline.co.zw/*`
   - Under "API restrictions", select "Restrict key"
   - Choose "YouTube Data API v3"

### Installation Process

1. **Upload Files**
   ```bash
   # Upload all files to your server
   # Ensure proper file structure is maintained
   ```

2. **Set Permissions**
   ```bash
   # Make cache directory writable
   chmod 755 cache/
   
   # Set proper file permissions
   chmod 644 *.php
   chmod 644 .htaccess
   chmod 644 robots.txt
   ```

3. **Create Directories**
   ```bash
   # Create required directories
   mkdir -p cache config logs
   chmod 755 cache config logs
   ```

4. **Run Installation**
   - Visit your website URL
   - You'll be redirected to the installation wizard
   - Fill in the required information
   - Click "Install Timeline.co.zw"

5. **Verify Installation**
   - Check that `.env` file was created
   - Check that `config/installed.lock` exists
   - Visit your site to ensure it's working

### Troubleshooting

#### Internal Server Error (500)
1. **Check PHP Version**
   ```bash
   php -v
   # Should be 7.4 or higher
   ```

2. **Check Extensions**
   ```bash
   php -m | grep -E "(curl|json|mbstring|openssl)"
   # All should be present
   ```

3. **Check Permissions**
   ```bash
   ls -la cache/
   # Should be writable (755)
   ```

4. **Check Error Logs**
   ```bash
   tail -f /var/log/apache2/error.log
   # Or check your web server's error log
   ```

5. **Run Diagnostics**
   - Visit `https://yourdomain.com/diagnose.php`
   - Check all status indicators

#### YouTube API Issues
1. **Invalid API Key**
   - Verify the key is correct
   - Check if YouTube Data API v3 is enabled
   - Ensure quota is not exceeded

2. **CORS Errors**
   - The installation creates a server-side proxy
   - No CORS issues should occur

3. **Rate Limiting**
   - Built-in rate limiting prevents abuse
   - 20 requests per hour per IP for YouTube API

#### File Permission Issues
```bash
# Fix common permission issues
chown -R www-data:www-data /path/to/your/site
chmod -R 755 /path/to/your/site
chmod 644 /path/to/your/site/*.php
chmod 644 /path/to/your/site/.htaccess
```

### Post-Installation

#### 1. **Test Functionality**
- Visit all pages (charts, videos, richest, awards, business)
- Check that YouTube content loads
- Test location detection
- Verify error pages work

#### 2. **Configure Analytics**
- Set up Google Analytics
- Update the GA ID in your `.env` file
- Or re-run installation with the GA ID

#### 3. **Set Up Monitoring**
- Monitor error logs
- Check API usage
- Set up uptime monitoring

#### 4. **Security**
- Ensure HTTPS is working
- Check security headers
- Monitor for suspicious activity

### Manual Configuration

If you prefer to configure manually:

1. **Create .env file**
   ```bash
   cp env.example .env
   nano .env
   ```

2. **Update values**
   ```env
   YOUTUBE_API_KEY=your_actual_api_key
   GOOGLE_ANALYTICS_ID=GA_MEASUREMENT_ID
   SITE_URL=https://timeline.co.zw
   SITE_NAME=Timeline.co.zw
   ```

3. **Create lock file**
   ```bash
   echo "$(date)" > config/installed.lock
   ```

### Support

If you encounter issues:

1. **Check Diagnostics**
   - Visit `/diagnose.php` for system status

2. **Review Logs**
   - Check web server error logs
   - Check PHP error logs

3. **Common Solutions**
   - Ensure PHP 7.4+ with required extensions
   - Set proper file permissions
   - Verify YouTube API key is valid
   - Check that all directories exist

4. **Reinstall**
   - Delete `.env` and `config/installed.lock`
   - Visit your site to run installation again

---

**Installation Status:** ✅ Ready for deployment
**Security:** ✅ Production-ready
**Performance:** ✅ Optimized
