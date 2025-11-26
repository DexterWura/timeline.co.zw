# Installation Checklist

## Pre-Upload Checklist

### ✅ Files to Upload
- [x] All PHP files
- [x] CSS files (style.css)
- [x] JavaScript files
- [x] Images and assets
- [x] .htaccess file
- [x] robots.txt
- [x] All class files
- [x] Migration files
- [x] install.php

### ✅ Server Requirements
- PHP 7.4 or higher
- MySQL 5.7 or higher (or MariaDB 10.2+)
- mod_rewrite enabled (for .htaccess)
- PDO MySQL extension
- cURL extension (for API calls)
- GD extension (for image handling, optional)
- Write permissions on:
  - `/config/` directory
  - `/cache/` directory
  - `/uploads/` directory
  - `/logs/` directory (will be created automatically)

## Installation Steps

### Step 1: Upload Files
1. Upload all files to your web server
2. Ensure file permissions are correct:
   - Directories: 755
   - PHP files: 644
   - .htaccess: 644

### Step 2: Run Installer
1. Navigate to: `http://yourdomain.com/install.php`
2. Follow the installation wizard

### Step 3: Database Configuration
Enter your database credentials:
- **Database Host**: Usually `localhost` (or your database server)
- **Database Name**: Create a new database or use existing
- **Database User**: Your MySQL username
- **Database Password**: Your MySQL password

**Note**: The installer will automatically:
- Create the database if it doesn't exist
- Set up all required tables via migrations
- Create the admin user account

### Step 4: Admin Account
Create your admin account:
- **Admin Email**: Your email address
- **Admin Password**: Minimum 8 characters
- **Confirm Password**: Re-enter password

### Step 5: Post-Installation
After installation:
1. Login to admin panel at `/admin`
2. Go to Settings and configure:
   - API keys (YouTube, News API, etc.)
   - Cache duration
   - Sitemap generation frequency
3. Run initial data fetch:
   - Go to Dashboard
   - Click "Fetch Music" and "Fetch Videos"
   - Or wait for cron job to run automatically

## Troubleshooting

### Installation Fails
- **Check PHP version**: Must be 7.4+
- **Check database credentials**: Verify they're correct
- **Check file permissions**: Config directory must be writable
- **Check error logs**: Look in server error logs

### Cannot Write Config File
- Set `/config/` directory permissions to 755 or 775
- Ensure web server user has write access

### Database Connection Fails
- Verify database credentials
- Check if MySQL service is running
- Verify database user has CREATE DATABASE permission
- Check firewall settings

### Migrations Fail
- Check database user has CREATE TABLE permissions
- Verify all migration files are uploaded
- Check error logs for specific migration errors

### Admin Login Doesn't Work
- Verify admin account was created during installation
- Try resetting password via database if needed
- Check session settings in PHP

## Post-Installation Security

1. **Delete install.php** after successful installation (optional but recommended)
2. **Set proper file permissions**:
   - Config files: 600 (read/write owner only)
   - PHP files: 644
   - Directories: 755
3. **Enable HTTPS** if available
4. **Set up cron job** for automatic data fetching:
   ```bash
   0 0 * * * php /path/to/your/site/cron/fetch-data.php
   ```

## Verification

After installation, verify:
- [ ] Can access homepage
- [ ] Can login to admin panel
- [ ] Database tables created (check via phpMyAdmin)
- [ ] Migrations table shows all migrations executed
- [ ] Can access settings page
- [ ] Can create blog posts
- [ ] API endpoints work

## Support

If you encounter issues:
1. Check server error logs
2. Check PHP error logs
3. Verify all requirements are met
4. Check file permissions
5. Verify database connection

---

**Status**: Ready for server deployment ✅

