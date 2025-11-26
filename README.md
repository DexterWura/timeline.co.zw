# Timeline.co.zw - Music Charts & Entertainment Platform

A responsive web application that replicates the Billboard Hot 100 website with additional features for music charts, videos, richest people, awards, and business analytics. Now enhanced with a complete OOP PHP backend, admin panel, and API integration.

## 🚀 Features

### Frontend Pages
- **Homepage** - Hero section with animations, featured charts, and trending content
- **Charts** - Billboard Hot 100 style music charts with filtering and sorting
- **Videos** - Top 100 music videos with grid/list view toggle
- **Richest People** - Top 100 richest people with wealth tracking
- **Awards** - Music awards and recognition showcase
- **Business** - Music industry business charts and analytics

### Backend Features
- ✨ **OOP PHP Architecture** - Clean, maintainable code structure
- 🔐 **Admin Panel** - Full-featured admin dashboard at `/admin`
- 🔑 **API Key Management** - Configure YouTube, AdSense, News APIs, Last.fm, Spotify
- 📝 **Blog Management** - Create, edit, and manage blog posts
- 🎵 **Music API Integration** - Automatic fetching and ranking of music charts
- 🎬 **Video API Integration** - YouTube video integration with ranking
- 📰 **News Integration** - News API integration for entertainment news
- 💾 **Database Migrations** - Flyway-style migration system
- ⏰ **3-Day Caching** - Automatic caching with 3-day refresh cycle
- 🎯 **Ranking Algorithm** - Intelligent ranking based on streams, views, and engagement
- 🚀 **Auto-Install** - One-click installation wizard

## 🛠️ Technology Stack

### Frontend
- **HTML5** - Semantic markup
- **CSS3** - Modern styling with Flexbox and Grid
- **JavaScript (ES6+)** - Interactive functionality
- **Font Awesome** - Icons
- **Google Fonts** - Typography (Inter font family)

### Backend
- **PHP 7.4+** - Server-side logic
- **MySQL/MariaDB** - Database
- **PDO** - Database abstraction
- **OOP** - Object-oriented programming
- **RESTful API** - API endpoints

## 📁 Project Structure

```
timeline.co.zw/
├── admin/                  # Admin panel
│   ├── includes/          # Header and footer includes
│   ├── assets/            # CSS, JS, images
│   ├── dashboard.php      # Admin dashboard
│   ├── settings.php       # API keys and settings
│   ├── blog.php           # Blog management
│   └── login.php          # Admin login
├── api/                   # API endpoints
│   ├── get-charts.php     # Get music charts
│   ├── get-videos.php     # Get videos
│   ├── fetch-music.php    # Fetch music from APIs
│   └── fetch-videos.php   # Fetch videos from APIs
├── classes/               # PHP Classes
│   ├── Database.php       # Database connection
│   ├── Auth.php           # Authentication
│   ├── Migration.php      # Database migrations
│   ├── Settings.php       # Settings manager
│   ├── Blog.php           # Blog manager
│   ├── ApiService.php     # Base API service
│   ├── MusicApiService.php # Music API service
│   ├── VideoApiService.php # Video API service
│   └── NewsService.php    # News API service
├── config/                # Configuration
│   └── config.php         # Main config (auto-generated)
├── database/              # Database migrations
│   └── migrations/        # Migration files
├── cron/                  # Cron jobs
│   └── fetch-data.php     # Daily data fetch
├── bootstrap.php          # Application bootstrap
├── install.php            # Installation wizard
└── .htaccess              # URL rewriting
```

## 🔧 Installation

### Step 1: Upload Files
Upload all files to your web server.

### Step 2: Run Installer
Navigate to `http://yourdomain.com/install.php` in your browser.

### Step 3: Configure Database
Enter your database credentials:
- Database Host (usually `localhost`)
- Database Name
- Database User
- Database Password

### Step 4: Create Admin Account
Create your admin account:
- Admin Email
- Admin Password (minimum 8 characters)

### Step 5: Configure API Keys
1. Login to admin panel at `/admin`
2. Go to Settings
3. Enter your API keys:
   - **YouTube API Key** - Get from [Google Cloud Console](https://console.cloud.google.com/)
   - **AdSense Client ID** - Get from [Google AdSense](https://www.google.com/adsense/)
   - **News API Key** - Get from [NewsAPI.org](https://newsapi.org/)
   - **Last.fm API Key** - Get from [Last.fm API](https://www.last.fm/api)
   - **Spotify Client ID & Secret** - Get from [Spotify Developer](https://developer.spotify.com/)

### Step 6: Set Up Cron Job (Optional)
For automatic data fetching every 3 days, add this to your crontab:
```bash
0 0 * * * php /path/to/your/site/cron/fetch-data.php
```

## 📊 How It Works

### Data Flow
1. **API Fetching** - System fetches data from configured APIs (YouTube, Last.fm, etc.)
2. **Ranking Algorithm** - Data is processed and ranked based on:
   - Streams/Views (60-70% weight)
   - Play Count/Likes (30-40% weight)
3. **Database Storage** - Ranked data is stored in database
4. **Caching** - Data is cached for 3 days to reduce API calls
5. **Frontend Display** - Frontend fetches data from database via API endpoints

### Ranking Algorithm
- **Music Charts**: `score = (streams × 0.6) + (play_count × 0.4)`
- **Videos**: `score = (views × 0.7) + (likes × 100 × 0.3)`

### Caching System
- Data is cached in database for 3 days
- After 3 days, system automatically fetches fresh data
- Cache is stored in `api_cache` table with expiration timestamps

## 🔐 Admin Panel

Access the admin panel at `/admin` (redirects to login).

### Features
- **Dashboard** - Overview of charts, videos, blogs, and news
- **Settings** - Configure API keys and change password
- **Blog Management** - Create, edit, publish, and delete blog posts
- **Music Charts** - View and manage music charts
- **Videos** - View and manage video charts
- **News** - View and manage news articles

## 🌐 API Endpoints

### Public Endpoints
- `GET /api/get-charts.php` - Get music charts
- `GET /api/get-videos.php` - Get video charts

### Admin Endpoints (Requires Authentication)
- `POST /api/fetch-music.php` - Manually fetch music charts
- `POST /api/fetch-videos.php` - Manually fetch videos

### Query Parameters
- `limit` - Number of results (default: 100)
- `date` - Chart date (default: today)

## 🗄️ Database Migrations

The system uses a Flyway-style migration system. All migrations are in `database/migrations/`.

Migrations are automatically run during installation and can be manually triggered:
```php
$migration = new Migration();
$migration->runMigrations();
```

## 🔒 Security Features

- Password hashing with bcrypt
- Session-based authentication
- SQL injection protection (PDO prepared statements)
- XSS protection (input sanitization)
- CSRF protection ready
- Admin-only API endpoints

## 📝 License

This project is created for educational and portfolio purposes. Feel free to use and modify as needed.

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Test across different devices
5. Submit a pull request

## 📞 Support

For questions or suggestions, please reach out through the project repository.

---

**Built with ❤️ for the music industry**
