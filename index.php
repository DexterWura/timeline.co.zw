<?php
session_start();

// Check if installation is required
if (!file_exists('.env') || !file_exists('config/installed.lock')) {
    header('Location: install.php');
    exit();
}

// Load environment variables
if (file_exists('.env')) {
    $lines = file('.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos($line, '=') !== false && strpos($line, '#') !== 0) {
            list($key, $value) = explode('=', $line, 2);
            $_ENV[trim($key)] = trim($value);
        }
    }
}

// Page configuration
$page_title = 'Timeline.co.zw - African Music & Entertainment Hub';
$page_description = 'Discover trending music, videos, and entertainment from Africa and around the world. Zimbabwe\'s premier music and entertainment platform.';
$page_keywords = 'music, videos, entertainment, Africa, Zimbabwe, trending, charts, afrobeats, amapiano, zimdancehall';
$canonical_url = 'https://timeline.co.zw';
$body_class = 'homepage';

// Additional CSS files
$additional_css = [
    'css/animations.css'
];

// Additional JavaScript files
$additional_js = [
    'js/youtubeApi.js',
    'js/locationService.js'
];

// Page-specific meta tags
$page_specific_meta = '
    <meta property="og:image" content="https://timeline.co.zw/images/african-music-hero.jpg">
    <meta name="twitter:image" content="https://timeline.co.zw/images/african-music-hero.jpg">
';

// Include head
include 'includes/head.php';
?>

    <!-- Header -->
    <header class="header">
        <div class="header-top">
            <div class="container">
                <div class="header-top-content">
                    <div class="logo">
                        <i class="fas fa-music"></i>
                        <span>timeline</span>
                    </div>
                    <nav class="main-nav">
                        <a href="index.php" class="nav-link active">Home</a>
                        <a href="charts.php" class="nav-link">Charts</a>
                        <a href="videos.php" class="nav-link">Videos</a>
                        <a href="richest.php" class="nav-link">Richest</a>
                        <a href="awards.php" class="nav-link">Awards</a>
                        <a href="business.php" class="nav-link">Business</a>
                    </nav>
                    <div class="header-actions">
                        <button class="subscribe-btn">SUBSCRIBE</button>
                        <button class="login-btn">LOGIN</button>
                        <button class="search-btn"><i class="fas fa-search"></i></button>
                    </div>
                </div>
            </div>
        </div>
        <div class="header-bottom">
            <div class="container">
                <div class="chart-nav">
                    <a href="charts.php" class="chart-nav-link active">TIMELINE HOT 100™</a>
                    <a href="charts.php" class="chart-nav-link">TIMELINE 200™</a>
                    <a href="charts.php" class="chart-nav-link">GLOBAL 200</a>
                    <a href="charts.php" class="chart-nav-link">ARTIST 100</a>
                </div>
            </div>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="hero">
        <div class="hero-background">
            <div class="hero-overlay"></div>
        </div>
        <div class="container">
            <div class="hero-content">
                <h1 class="hero-title">
                    <span class="hero-title-main">African Music</span>
                    <span class="hero-title-sub">Entertainment Hub</span>
                </h1>
                <p class="hero-description">
                    Discover trending music, videos, and entertainment from Zimbabwe and across Africa. 
                    Your gateway to the continent's vibrant music scene.
                </p>
                <div class="hero-actions">
                    <button class="hero-btn primary" onclick="loadTrendingContent()">
                        <i class="fas fa-play"></i>
                        Explore Trending
                    </button>
                    <button class="hero-btn secondary" onclick="loadAfricanMusic()">
                        <i class="fas fa-music"></i>
                        African Music
                    </button>
                </div>
                <div class="hero-stats">
                    <div class="stat">
                        <span class="stat-number" id="total-videos">0</span>
                        <span class="stat-label">Videos</span>
                    </div>
                    <div class="stat">
                        <span class="stat-number" id="total-artists">0</span>
                        <span class="stat-label">Artists</span>
                    </div>
                    <div class="stat">
                        <span class="stat-number" id="total-countries">0</span>
                        <span class="stat-label">Countries</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Featured Charts Section -->
    <section class="featured-charts">
        <div class="container">
            <h2 class="section-title">Featured Charts</h2>
            <div class="charts-grid">
                <div class="chart-card">
                    <div class="chart-icon">
                        <i class="fas fa-music"></i>
                    </div>
                    <h3>Hot 100 Music</h3>
                    <p>Top trending songs from Africa and around the world</p>
                    <a href="charts.php" class="chart-link">View Chart <i class="fas fa-arrow-right"></i></a>
                </div>
                <div class="chart-card">
                    <div class="chart-icon">
                        <i class="fas fa-video"></i>
                    </div>
                    <h3>Top 100 Videos</h3>
                    <p>Most popular music videos and entertainment content</p>
                    <a href="videos.php" class="chart-link">View Chart <i class="fas fa-arrow-right"></i></a>
                </div>
                <div class="chart-card">
                    <div class="chart-icon">
                        <i class="fas fa-crown"></i>
                    </div>
                    <h3>Richest People</h3>
                    <p>Real-time billionaire rankings and wealth tracking</p>
                    <a href="richest.php" class="chart-link">View Chart <i class="fas fa-arrow-right"></i></a>
                </div>
            </div>
        </div>
    </section>

    <!-- Trending Now Section -->
    <section class="trending-now">
        <div class="container">
            <h2 class="section-title">Trending Now</h2>
            
            <!-- Regional Selector -->
            <div class="regional-selector">
                <div class="selector-tabs">
                    <button class="selector-tab active" data-region="local">
                        <i class="fas fa-map-marker-alt"></i>
                        <span id="local-region-name">Your Region</span>
                    </button>
                    <button class="selector-tab" data-region="africa">
                        <i class="fas fa-globe-africa"></i>
                        Africa
                    </button>
                    <button class="selector-tab" data-region="global">
                        <i class="fas fa-globe"></i>
                        Global
                    </button>
                </div>
            </div>
            
            <!-- Trending Categories Tabs -->
            <div class="trending-tabs">
                <button class="trending-tab active" data-category="music">Music</button>
                <button class="trending-tab" data-category="videos">Videos</button>
                <button class="trending-tab" data-category="news">News</button>
                <button class="trending-tab" data-category="artists">Artists</button>
            </div>

            <!-- Loading State -->
            <div class="trending-loading" id="trending-loading">
                <div class="loading-spinner"></div>
                <p>Loading trending content...</p>
            </div>

            <!-- Trending Music -->
            <div class="trending-content active" id="trending-music">
                <div class="trending-grid" id="trending-music-grid">
                    <!-- Content will be loaded dynamically -->
                </div>
            </div>

            <!-- Trending Videos -->
            <div class="trending-content" id="trending-videos">
                <div class="trending-grid" id="trending-videos-grid">
                    <!-- Content will be loaded dynamically -->
                </div>
            </div>

            <!-- Trending News -->
            <div class="trending-content" id="trending-news">
                <div class="trending-grid" id="trending-news-grid">
                    <!-- Content will be loaded dynamically -->
                </div>
            </div>

            <!-- Trending Artists -->
            <div class="trending-content" id="trending-artists">
                <div class="trending-grid" id="trending-artists-grid">
                    <!-- Content will be loaded dynamically -->
                </div>
            </div>
        </div>
    </section>

    <!-- African Music Spotlight -->
    <section class="african-spotlight">
        <div class="container">
            <h2 class="section-title">
                <i class="fas fa-globe-africa"></i>
                African Music Spotlight
                <span class="african-badge" style="display: none;">🇿🇼</span>
            </h2>
            <div class="spotlight-content">
                <div class="spotlight-tabs">
                    <button class="spotlight-tab active" data-genre="all">All Genres</button>
                    <button class="spotlight-tab" data-genre="afrobeats">Afrobeats</button>
                    <button class="spotlight-tab" data-genre="amapiano">Amapiano</button>
                    <button class="spotlight-tab" data-genre="zimdancehall">Zimdancehall</button>
                    <button class="spotlight-tab" data-genre="chimurenga">Chimurenga</button>
                    <button class="spotlight-tab" data-genre="sungura">Sungura</button>
                </div>
                <div class="spotlight-grid" id="african-music-grid">
                    <!-- African music content will be loaded here -->
                </div>
            </div>
        </div>
    </section>

    <!-- Newsletter Section -->
    <section class="newsletter">
        <div class="container">
            <div class="newsletter-content">
                <h2>Stay Updated</h2>
                <p>Get the latest African music and entertainment news delivered to your inbox.</p>
                <form class="newsletter-form" id="newsletter-form">
                    <input type="email" placeholder="Enter your email address" required>
                    <button type="submit">Subscribe</button>
                </form>
            </div>
        </div>
    </section>

<?php
// Page-specific scripts
$page_specific_scripts = '
<script>
// Initialize homepage functionality
document.addEventListener("DOMContentLoaded", async function() {
    // Initialize location service
    await window.locationService.initialize();
    
    // Update regional selector
    updateRegionalSelector();
    
    // Load trending content
    loadTrendingContent();
    
    // Load African music
    loadAfricanMusic();
    
    // Initialize animations
    initAnimations();
    initCounters();
    initSearch();
    initMobileMenu();
    initTrendingTabs();
    initSpotlightTabs();
    initNewsletter();
});

// Update regional selector based on user location
function updateRegionalSelector() {
    const locationService = window.locationService;
    const localTab = document.querySelector(\'[data-region="local"] span\');
    
    if (locationService.isZimbabweanUser()) {
        localTab.textContent = "Zimbabwe";
    } else if (locationService.isAfricanUser()) {
        localTab.textContent = locationService.getCountryName();
    } else {
        localTab.textContent = "Your Region";
    }
}

// Load trending content based on selected region
async function loadTrendingContent(region = null) {
    const loading = document.getElementById("trending-loading");
    const musicGrid = document.getElementById("trending-music-grid");
    const videosGrid = document.getElementById("trending-videos-grid");
    
    loading.style.display = "block";
    
    try {
        const locationService = window.locationService;
        const youtubeApi = window.youtubeApi;
        
        // Determine region
        const selectedRegion = region || locationService.getContentPreferences().primaryRegion;
        
        // Load trending music
        const music = await youtubeApi.getTrendingMusic(selectedRegion, 10);
        displayTrendingMusic(music, musicGrid);
        
        // Load trending videos
        const videos = await youtubeApi.getTrendingVideos(selectedRegion, 10);
        displayTrendingVideos(videos, videosGrid);
        
        // Update counters
        updateCounters(music.length + videos.length);
        
    } catch (error) {
        console.error("Error loading trending content:", error);
        showError("Failed to load trending content. Please try again.");
    } finally {
        loading.style.display = "none";
    }
}

// Load African music content
async function loadAfricanMusic() {
    const grid = document.getElementById("african-music-grid");
    
    try {
        const youtubeApi = window.youtubeApi;
        const music = await youtubeApi.getAfricanMusic(12);
        displayAfricanMusic(music, grid);
    } catch (error) {
        console.error("Error loading African music:", error);
    }
}

// Display trending music
function displayTrendingMusic(music, container) {
    container.innerHTML = "";
    
    music.forEach((item, index) => {
        const musicItem = document.createElement("div");
        musicItem.className = "trending-item";
        musicItem.innerHTML = `
            <div class="trending-rank">${index + 1}</div>
            <div class="trending-artwork">
                <img src="${item.thumbnail.high}" alt="${item.title}" class="artwork-img">
                <div class="play-overlay">
                    <button class="play-btn" onclick="playVideo(\'${item.id}\')">
                        <i class="fas fa-play"></i>
                    </button>
                </div>
            </div>
            <div class="trending-info">
                <h4>${item.title}</h4>
                <p>${item.channelTitle}</p>
                <div class="trending-stats">
                    <span class="views">${youtubeApi.formatViewCount(item.statistics.viewCount)} views</span>
                    <span class="time">${formatTimeAgo(item.publishedAt)}</span>
                </div>
            </div>
            <div class="trending-actions">
                <button class="play-btn" onclick="playVideo(\'${item.id}\')">
                    <i class="fas fa-play"></i>
                </button>
                <button class="add-btn" onclick="addToPlaylist(\'${item.id}\')">
                    <i class="fas fa-plus"></i>
                </button>
            </div>
        `;
        container.appendChild(musicItem);
    });
}

// Display trending videos
function displayTrendingVideos(videos, container) {
    container.innerHTML = "";
    
    videos.forEach((item, index) => {
        const videoItem = document.createElement("div");
        videoItem.className = "trending-item";
        videoItem.innerHTML = `
            <div class="trending-rank">${index + 1}</div>
            <div class="trending-artwork">
                <img src="${item.thumbnail.high}" alt="${item.title}" class="artwork-img">
                <div class="play-overlay">
                    <button class="play-btn" onclick="playVideo(\'${item.id}\')">
                        <i class="fas fa-play"></i>
                    </button>
                </div>
            </div>
            <div class="trending-info">
                <h4>${item.title}</h4>
                <p>${item.channelTitle}</p>
                <div class="trending-stats">
                    <span class="views">${youtubeApi.formatViewCount(item.statistics.viewCount)} views</span>
                    <span class="time">${formatTimeAgo(item.publishedAt)}</span>
                </div>
            </div>
            <div class="trending-actions">
                <button class="play-btn" onclick="playVideo(\'${item.id}\')">
                    <i class="fas fa-play"></i>
                </button>
                <button class="add-btn" onclick="shareVideo(\'${item.id}\')">
                    <i class="fas fa-share"></i>
                </button>
            </div>
        `;
        container.appendChild(videoItem);
    });
}

// Display African music
function displayAfricanMusic(music, container) {
    container.innerHTML = "";
    
    music.forEach((item, index) => {
        const musicItem = document.createElement("div");
        musicItem.className = "spotlight-item";
        musicItem.innerHTML = `
            <div class="spotlight-artwork">
                <img src="${item.thumbnail.high}" alt="${item.title}">
                <div class="spotlight-overlay">
                    <button class="play-btn" onclick="playVideo(\'${item.id}\')">
                        <i class="fas fa-play"></i>
                    </button>
                </div>
                ${item.isZimbabwean ? \'<span class="zimbabwe-badge">🇿🇼</span>\' : \'\'}
                ${item.isAfrican ? \'<span class="african-badge">🌍</span>\' : \'\'}
            </div>
            <div class="spotlight-info">
                <h4>${item.title}</h4>
                <p>${item.channelTitle}</p>
                <div class="spotlight-stats">
                    <span class="views">${youtubeApi.formatViewCount(item.statistics.viewCount)} views</span>
                </div>
            </div>
        `;
        container.appendChild(musicItem);
    });
}

// Initialize spotlight tabs
function initSpotlightTabs() {
    const tabs = document.querySelectorAll(".spotlight-tab");
    tabs.forEach(tab => {
        tab.addEventListener("click", () => {
            tabs.forEach(t => t.classList.remove("active"));
            tab.classList.add("active");
            
            const genre = tab.getAttribute("data-genre");
            filterAfricanMusic(genre);
        });
    });
}

// Filter African music by genre
function filterAfricanMusic(genre) {
    // Implementation for filtering by genre
    console.log("Filtering by genre:", genre);
}

// Play video
function playVideo(videoId) {
    // Open video in new tab or embed player
    window.open(`https://www.youtube.com/watch?v=${videoId}`, \'_blank\');
}

// Add to playlist
function addToPlaylist(videoId) {
    // Implementation for adding to playlist
    console.log("Adding to playlist:", videoId);
}

// Share video
function shareVideo(videoId) {
    if (navigator.share) {
        navigator.share({
            title: "Check out this video",
            url: `https://www.youtube.com/watch?v=${videoId}`
        });
    } else {
        // Fallback to copying URL
        navigator.clipboard.writeText(`https://www.youtube.com/watch?v=${videoId}`);
        showNotification("Video URL copied to clipboard!");
    }
}

// Update counters
function updateCounters(count) {
    const totalVideos = document.getElementById("total-videos");
    if (totalVideos) {
        animateCounter(totalVideos, count);
    }
}

// Format time ago
function formatTimeAgo(dateString) {
    const date = new Date(dateString);
    const now = new Date();
    const diffInSeconds = Math.floor((now - date) / 1000);
    
    if (diffInSeconds < 60) return "Just now";
    if (diffInSeconds < 3600) return Math.floor(diffInSeconds / 60) + " minutes ago";
    if (diffInSeconds < 86400) return Math.floor(diffInSeconds / 3600) + " hours ago";
    if (diffInSeconds < 2592000) return Math.floor(diffInSeconds / 86400) + " days ago";
    return Math.floor(diffInSeconds / 2592000) + " months ago";
}

// Initialize newsletter
function initNewsletter() {
    const form = document.getElementById("newsletter-form");
    form.addEventListener("submit", async (e) => {
        e.preventDefault();
        const email = form.querySelector("input[type=email]").value;
        
        try {
            const response = await fetch("api/newsletter.php", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                },
                body: JSON.stringify({ email: email })
            });
            
            const data = await response.json();
            if (data.success) {
                showNotification("Successfully subscribed to newsletter!", "success");
                form.reset();
            } else {
                showNotification("Failed to subscribe. Please try again.", "error");
            }
        } catch (error) {
            showNotification("Failed to subscribe. Please try again.", "error");
        }
    });
}

// Show notification
function showNotification(message, type = "info") {
    // Implementation for showing notifications
    console.log(`${type.toUpperCase()}: ${message}`);
}
</script>
';

// Include footer
include 'includes/footer.php';
?>
