# 🚀 Timeline.co.zw Setup Guide

## Overview
Timeline.co.zw is an African-focused music and entertainment platform that provides real-time trending content from YouTube, with special emphasis on Zimbabwean and African content.

## 🛠️ Prerequisites

### Required Software
- **PHP 7.4+** with the following extensions:
  - `curl`
  - `json`
  - `mbstring`
  - `openssl`
- **Web Server** (Apache/Nginx)
- **YouTube Data API v3 Key**

### Optional Software
- **MySQL/PostgreSQL** (for user data and newsletter)
- **Redis** (for caching)

## 📋 Installation Steps

### 1. Clone/Download the Project
```bash
git clone https://github.com/yourusername/timeline.co.zw.git
cd timeline.co.zw
```

### 2. Configure YouTube API

#### Get YouTube API Key
1. Go to [Google Cloud Console](https://console.cloud.google.com/)
2. Create a new project or select existing one
3. Enable YouTube Data API v3
4. Create credentials (API Key)
5. Restrict the API key to your domain (recommended)

#### Update Configuration
Edit `config/app.php` and replace `YOUR_YOUTUBE_API_KEY_HERE` with your actual API key:

```php
'youtube' => [
    'api_key' => 'AIzaSyBxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx', // Your actual API key
    // ... other settings
],
```

### 3. Set Up Web Server

#### Apache Configuration
Create a `.htaccess` file in the root directory:

```apache
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^([^/]+)/?$ $1.php [L,QSA]

# Security headers
Header always set X-Content-Type-Options nosniff
Header always set X-Frame-Options DENY
Header always set X-XSS-Protection "1; mode=block"
```

#### Nginx Configuration
```nginx
server {
    listen 80;
    server_name timeline.co.zw;
    root /path/to/timeline.co.zw;
    index index.php;

    location / {
        try_files $uri $uri/ $uri.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php7.4-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }
}
```

### 4. Set Permissions
```bash
chmod 755 -R .
chmod 777 cache/ # If using file-based caching
```

### 5. Test Installation
1. Open your browser and navigate to your domain
2. Check if the homepage loads correctly
3. Test the YouTube API integration by clicking "Explore Trending"
4. Verify location detection works

## 🔧 Configuration

### Regional Settings
The platform automatically detects user location and prioritizes content accordingly:

- **Zimbabwean users**: See Zimbabwean content first, then African, then global
- **African users**: See their country's content first, then African, then global
- **Global users**: See global content with African content highlighted

### Content Categories
The platform supports various African music genres:
- **Afrobeats** (Nigeria/West Africa)
- **Amapiano** (South Africa)
- **Zimdancehall** (Zimbabwe)
- **Chimurenga** (Zimbabwe)
- **Sungura** (Zimbabwe)
- **Kwaito** (South Africa)
- **Highlife** (Ghana)
- **Bongo Flava** (Tanzania)

## 📱 Features

### Real-Time Content
- **Trending Music**: Top 100 songs from YouTube
- **Trending Videos**: Most popular music videos
- **Regional Content**: Location-based content filtering
- **African Spotlight**: Dedicated African music section

### User Experience
- **Location Detection**: Automatic country/region detection
- **Language Support**: Multiple African languages
- **Responsive Design**: Works on all devices
- **Real-Time Updates**: Content refreshes automatically

### API Integration
- **YouTube Data API v3**: For trending content
- **Location APIs**: For user location detection
- **Newsletter API**: For email subscriptions

## 🚨 Troubleshooting

### Common Issues

#### 1. YouTube API Not Working
**Symptoms**: "Failed to load trending content" error
**Solutions**:
- Verify API key is correct
- Check API quota limits
- Ensure YouTube Data API v3 is enabled
- Check network connectivity

#### 2. Location Detection Failing
**Symptoms**: Default location (Zimbabwe) always shown
**Solutions**:
- Check if HTTPS is enabled (required for geolocation)
- Verify IP detection services are accessible
- Check browser permissions for location

#### 3. CORS Errors
**Symptoms**: API requests blocked by browser
**Solutions**:
- Ensure proper CORS headers in API responses
- Check if API endpoints are accessible
- Verify domain restrictions on API key

### Debug Mode
Enable debug mode by adding to `config/app.php`:
```php
'debug' => true,
'log_level' => 'debug'
```

## 📊 Performance Optimization

### Caching
- **YouTube API**: 10-minute cache for trending content
- **Location Data**: 5-minute cache for user location
- **Static Assets**: Long-term caching for CSS/JS

### CDN Setup
Consider using a CDN for:
- Static assets (CSS, JS, images)
- API responses
- Video thumbnails

## 🔒 Security

### API Security
- Restrict YouTube API key to your domain
- Implement rate limiting
- Use HTTPS for all requests
- Validate all user inputs

### Data Protection
- No personal data stored (except newsletter emails)
- Session-based location preferences
- Secure API endpoints

## 📈 Analytics

### Google Analytics
Add your tracking ID to `includes/head.php`:
```javascript
gtag('config', 'GA_MEASUREMENT_ID');
```

### Custom Analytics
Track user engagement with:
- Content views
- Regional preferences
- Search queries
- Newsletter signups

## 🌍 Internationalization

### Supported Languages
- **English** (en) - Default
- **Shona** (sn) - Zimbabwe
- **Ndebele** (nd) - Zimbabwe
- **French** (fr) - West/Central Africa
- **Portuguese** (pt) - Angola, Mozambique
- **Arabic** (ar) - North Africa
- **Swahili** (sw) - East Africa

### Adding New Languages
1. Add language code to `api/update-language.php`
2. Update language selector in `includes/footer.php`
3. Add translations (if implementing full i18n)

## 🚀 Deployment

### Production Checklist
- [ ] YouTube API key configured
- [ ] HTTPS enabled
- [ ] Error reporting configured
- [ ] Analytics tracking enabled
- [ ] Caching enabled
- [ ] Security headers set
- [ ] Domain restrictions on API key
- [ ] Backup strategy in place

### Environment Variables
For production, use environment variables instead of hardcoded values:
```php
'youtube' => [
    'api_key' => $_ENV['YOUTUBE_API_KEY'],
    // ...
],
```

## 📞 Support

### Getting Help
- Check the troubleshooting section above
- Review browser console for errors
- Test API endpoints directly
- Check server error logs

### Contributing
1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Test thoroughly
5. Submit a pull request

## 📄 License

This project is licensed under the MIT License - see the LICENSE file for details.

---

**Made with ❤️ in Zimbabwe for Africa and the world!**
