# Pre-Deployment Checklist

## ✅ Pre-Upload Verification

### Files Structure
- [x] All PHP files present
- [x] All class files in `/classes/` directory
- [x] All migration files in `/database/migrations/` directory
- [x] CSS file (`/css/style.css`) present
- [x] `.htaccess` file present and configured
- [x] `install.php` present and ready
- [x] `bootstrap.php` present
- [x] `robots.txt` present
- [x] All admin PHP pages present
- [x] All frontend PHP pages present

### Server Requirements Check
Before uploading, ensure your server has:
- [ ] PHP 7.4 or higher
- [ ] MySQL 5.7+ or MariaDB 10.2+
- [ ] mod_rewrite enabled
- [ ] PDO MySQL extension
- [ ] cURL extension
- [ ] Write permissions on directories

### Directory Permissions
Set these permissions before installation:
- `/config/` - 755 (must be writable)
- `/cache/` - 755 (will be created if missing)
- `/uploads/` - 755 (will be created if missing)
- `/logs/` - 755 (will be created if missing)

## Installation Process

### Step 1: Upload Files
1. Upload all files to your web server
2. Ensure file structure is maintained
3. Set directory permissions as above

### Step 2: Run Installer
1. Navigate to: `http://yourdomain.com/install.php`
2. You'll see a 3-step installation wizard

### Step 3: Database Setup
The installer will:
- ✅ Test database connection
- ✅ Create database if it doesn't exist
- ✅ Store credentials in session (temporarily)
- ✅ Move to step 2

**Enter:**
- Database Host (usually `localhost`)
- Database Name (will be created if doesn't exist)
- Database User
- Database Password

### Step 4: Admin Account
The installer will:
- ✅ Validate email format
- ✅ Validate password (min 8 characters)
- ✅ Create config file
- ✅ Run all database migrations (V001, V002, V003, V004)
- ✅ Create admin user account
- ✅ Create required directories (cache, uploads, logs)
- ✅ Set up security (.htaccess files)
- ✅ Complete installation

**Enter:**
- Admin Email (validated)
- Admin Password (min 8 chars)
- Confirm Password

### Step 5: Post-Installation
After successful installation:
1. ✅ Login to `/admin` with your credentials
2. ✅ Go to Settings and configure API keys
3. ✅ Set cache duration
4. ✅ Set sitemap generation frequency
5. ✅ Fetch initial data (Dashboard → Fetch buttons)

## What Gets Created Automatically

### Database Tables (via Migrations)
- ✅ `users` - Admin/users table
- ✅ `settings` - Application settings
- ✅ `music_charts` - Music chart data
- ✅ `videos` - Video chart data
- ✅ `api_cache` - API response cache
- ✅ `blogs` - Blog posts
- ✅ `news_articles` - News articles
- ✅ `awards` - Awards data
- ✅ `richest_people` - Richest people data
- ✅ `hall_of_fame` - Hall of Fame entries
- ✅ `countries` - Countries reference table
- ✅ `migrations` - Migration tracking
- ✅ `sitemap_settings` - Sitemap configuration

### Directories Created
- ✅ `/cache/` - For API cache
- ✅ `/uploads/` - For file uploads
- ✅ `/logs/` - For error logs

### Security Files Created
- ✅ `/cache/.htaccess` - Protects cache directory
- ✅ `/uploads/.htaccess` - Protects uploads directory
- ✅ `/config/config.php` - Database configuration

## Troubleshooting

### Installation Fails at Database Step
- Check database credentials
- Verify MySQL service is running
- Check user has CREATE DATABASE permission
- Check firewall/network settings

### Installation Fails at Config Step
- Check `/config/` directory permissions (755 or 775)
- Verify web server has write access
- Check disk space

### Installation Fails at Migration Step
- Check database user has CREATE TABLE permission
- Verify all migration files uploaded
- Check error logs for specific migration errors
- Verify PHP version is 7.4+

### Admin User Not Created
- Check if migrations ran successfully
- Verify users table exists
- Check error logs
- Try creating user manually via database if needed

## Post-Installation Security

1. **Optional**: Delete `install.php` after installation
2. **Recommended**: Set config.php permissions to 600 (owner read/write only)
3. **Recommended**: Enable HTTPS if available
4. **Recommended**: Set up cron job for automatic data fetching

## Verification Steps

After installation, verify:
- [ ] Can access homepage (`/`)
- [ ] Can login to admin (`/admin`)
- [ ] Can access settings page
- [ ] Can create blog post
- [ ] Can fetch music/video data
- [ ] API endpoints work (`/api/get-charts.php`)
- [ ] Sitemap generates (`/sitemap.php`)

## Quick Test Commands

After installation, test these URLs:
- `http://yourdomain.com/` - Homepage
- `http://yourdomain.com/admin` - Admin login
- `http://yourdomain.com/api/get-charts.php` - API endpoint
- `http://yourdomain.com/sitemap.php` - Sitemap

## Support

If installation fails:
1. Check server error logs
2. Check PHP error logs
3. Verify all requirements met
4. Check file permissions
5. Verify database connection

---

**Status**: ✅ READY FOR DEPLOYMENT

The installer has been tested and verified. It will:
- ✅ Create database automatically
- ✅ Run all migrations in order
- ✅ Create admin user
- ✅ Set up all directories
- ✅ Configure security
- ✅ Handle errors gracefully

