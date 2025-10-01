<?php
session_start();

// Page configuration
$page_title = 'Top 100 Videos - African Music Videos | Timeline.co.zw';
$page_description = 'Watch the most popular music videos from Africa and around the world. Discover trending videos from Zimbabwe, Nigeria, South Africa, and more.';
$page_keywords = 'music videos, top videos, african videos, trending videos, zimbabwe videos, nigeria videos, south africa videos, music video charts';
$canonical_url = 'https://timeline.co.zw/videos.php';
$body_class = 'videos-page';

// Additional CSS files
$additional_css = [
    'css/videos.css',
    'css/animations.css'
];

// Additional JavaScript files
$additional_js = [
    'js/youtubeApi.js',
    'js/locationService.js',
    'js/videos.js'
];

// Page-specific meta tags
$page_specific_meta = '
    <meta property="og:image" content="https://timeline.co.zw/images/videos-hero.jpg">
    <meta name="twitter:image" content="https://timeline.co.zw/images/videos-hero.jpg">
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
                        <a href="index.php" class="nav-link">Home</a>
                        <a href="charts.php" class="nav-link">Charts</a>
                        <a href="videos.php" class="nav-link active">Videos</a>
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
                    <a href="videos.php" class="chart-nav-link active">TOP 100 VIDEOS</a>
                    <a href="videos.php" class="chart-nav-link">MUSIC VIDEOS</a>
                    <a href="videos.php" class="chart-nav-link">LIVE PERFORMANCES</a>
                    <a href="videos.php" class="chart-nav-link">LYRIC VIDEOS</a>
                </div>
            </div>
        </div>
    </header>

    <!-- Page Header -->
    <section class="page-header">
        <div class="container">
            <div class="page-header-content">
                <h1 class="page-title">
                    <i class="fas fa-video"></i>
                    Top 100 Videos
                    <span class="african-badge" style="display: none;">🇿🇼</span>
                </h1>
                <p class="page-description">
                    The most popular music videos from Africa and around the world, featuring the latest hits and trending content.
                </p>
                
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
            </div>
        </div>
    </section>

    <!-- Featured Top 3 Videos -->
    <section class="featured-top3">
        <div class="container">
            <h2 class="section-title">Top 3 Videos This Week</h2>
            <div class="featured-grid" id="featured-top3">
                <!-- Top 3 videos will be loaded here -->
                <div class="loading-placeholder">
                    <div class="loading-spinner"></div>
                    <p>Loading top videos...</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Videos Section -->
    <section class="videos-section">
        <div class="container">
            <div class="videos-header">
                <h2 class="section-title">Top 100 Videos</h2>
                <div class="videos-controls">
                    <div class="filter-group">
                        <label for="category-filter">Category:</label>
                        <select id="category-filter" class="filter-select">
                            <option value="all">All Categories</option>
                            <option value="music-video">Music Videos</option>
                            <option value="live">Live Performances</option>
                            <option value="lyric">Lyric Videos</option>
                            <option value="behind-scenes">Behind the Scenes</option>
                            <option value="dance">Dance Videos</option>
                        </select>
                    </div>
                    <div class="filter-group">
                        <label for="duration-filter">Duration:</label>
                        <select id="duration-filter" class="filter-select">
                            <option value="all">Any Duration</option>
                            <option value="short">Under 3 minutes</option>
                            <option value="medium">3-5 minutes</option>
                            <option value="long">Over 5 minutes</option>
                        </select>
                    </div>
                    <div class="filter-group">
                        <label for="sort-filter">Sort by:</label>
                        <select id="sort-filter" class="filter-select">
                            <option value="views">Most Views</option>
                            <option value="likes">Most Likes</option>
                            <option value="recent">Most Recent</option>
                            <option value="trending">Trending</option>
                        </select>
                    </div>
                    <button class="refresh-btn" onclick="refreshVideos()">
                        <i class="fas fa-sync-alt"></i>
                        Refresh
                    </button>
                </div>
            </div>

            <!-- Loading State -->
            <div class="videos-loading" id="videos-loading">
                <div class="loading-spinner"></div>
                <p>Loading videos...</p>
            </div>

            <!-- Videos Grid -->
            <div class="videos-grid" id="videos-grid">
                <!-- Video items will be loaded here -->
            </div>

            <!-- Load More Button -->
            <div class="load-more-container">
                <button class="load-more-btn" id="load-more-btn" onclick="loadMoreVideos()">
                    <i class="fas fa-plus"></i>
                    Load More
                </button>
            </div>
        </div>
    </section>

    <!-- Video Statistics -->
    <section class="video-stats">
        <div class="container">
            <h2 class="section-title">Video Statistics</h2>
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-video"></i>
                    </div>
                    <div class="stat-content">
                        <h3 id="total-videos">0</h3>
                        <p>Total Videos</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-play"></i>
                    </div>
                    <div class="stat-content">
                        <h3 id="total-views">0</h3>
                        <p>Total Views</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-heart"></i>
                    </div>
                    <div class="stat-content">
                        <h3 id="total-likes">0</h3>
                        <p>Total Likes</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="stat-content">
                        <h3 id="total-duration">0</h3>
                        <p>Total Duration</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- African Video Spotlight -->
    <section class="african-spotlight">
        <div class="container">
            <h2 class="section-title">
                <i class="fas fa-globe-africa"></i>
                African Video Spotlight
            </h2>
            <div class="spotlight-tabs">
                <button class="spotlight-tab active" data-type="all">All Videos</button>
                <button class="spotlight-tab" data-type="music-videos">Music Videos</button>
                <button class="spotlight-tab" data-type="live">Live Performances</button>
                <button class="spotlight-tab" data-type="dance">Dance Videos</button>
                <button class="spotlight-tab" data-type="behind-scenes">Behind the Scenes</button>
            </div>
            <div class="spotlight-grid" id="african-spotlight-grid">
                <!-- African video content will be loaded here -->
            </div>
        </div>
    </section>

    <!-- Video Categories -->
    <section class="video-categories">
        <div class="container">
            <h2 class="section-title">Browse by Category</h2>
            <div class="categories-grid">
                <div class="category-card" onclick="filterByCategory('music-video')">
                    <div class="category-icon">
                        <i class="fas fa-music"></i>
                    </div>
                    <h3>Music Videos</h3>
                    <p>Official music videos from your favorite artists</p>
                </div>
                <div class="category-card" onclick="filterByCategory('live')">
                    <div class="category-icon">
                        <i class="fas fa-microphone"></i>
                    </div>
                    <h3>Live Performances</h3>
                    <p>Amazing live performances and concerts</p>
                </div>
                <div class="category-card" onclick="filterByCategory('lyric')">
                    <div class="category-icon">
                        <i class="fas fa-quote-left"></i>
                    </div>
                    <h3>Lyric Videos</h3>
                    <p>Videos with lyrics for easy singing along</p>
                </div>
                <div class="category-card" onclick="filterByCategory('dance')">
                    <div class="category-icon">
                        <i class="fas fa-running"></i>
                    </div>
                    <h3>Dance Videos</h3>
                    <p>Dance challenges and choreography videos</p>
                </div>
                <div class="category-card" onclick="filterByCategory('behind-scenes')">
                    <div class="category-icon">
                        <i class="fas fa-camera"></i>
                    </div>
                    <h3>Behind the Scenes</h3>
                    <p>Exclusive behind-the-scenes content</p>
                </div>
                <div class="category-card" onclick="filterByCategory('tutorial')">
                    <div class="category-icon">
                        <i class="fas fa-graduation-cap"></i>
                    </div>
                    <h3>Tutorials</h3>
                    <p>Learn to play instruments and sing</p>
                </div>
            </div>
        </div>
    </section>

<?php
// Page-specific scripts
$page_specific_scripts = '
<script>
// Initialize videos page functionality
document.addEventListener("DOMContentLoaded", async function() {
    // Initialize location service
    await window.locationService.initialize();
    
    // Update regional selector
    updateRegionalSelector();
    
    // Load videos data
    await loadVideosData();
    
    // Load African spotlight
    await loadAfricanSpotlight();
    
    // Initialize page functionality
    initVideosPage();
    initFilters();
    initSpotlightTabs();
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

// Load videos data
async function loadVideosData(region = null) {
    const loading = document.getElementById("videos-loading");
    const videosGrid = document.getElementById("videos-grid");
    const featuredGrid = document.getElementById("featured-top3");
    
    loading.style.display = "block";
    
    try {
        const locationService = window.locationService;
        const youtubeApi = window.youtubeApi;
        
        // Determine region
        const selectedRegion = region || locationService.getContentPreferences().primaryRegion;
        
        // Load trending videos
        const videos = await youtubeApi.getTrendingVideos(selectedRegion, 100);
        
        // Display featured top 3
        displayFeaturedTop3(videos.slice(0, 3), featuredGrid);
        
        // Display videos grid
        displayVideosGrid(videos, videosGrid);
        
        // Update statistics
        updateVideoStatistics(videos);
        
    } catch (error) {
        console.error("Error loading videos data:", error);
        showError("Failed to load videos. Please try again.");
    } finally {
        loading.style.display = "none";
    }
}

// Display featured top 3
function displayFeaturedTop3(top3, container) {
    container.innerHTML = "";
    
    top3.forEach((video, index) => {
        const featuredItem = document.createElement("div");
        featuredItem.className = `featured-item rank-${index + 1}`;
        featuredItem.innerHTML = `
            <div class="featured-rank">${index + 1}</div>
            <div class="featured-thumbnail">
                <img src="${video.thumbnail.high}" alt="${video.title}">
                <div class="play-overlay">
                    <button class="play-btn" onclick="playVideo(\'${video.id}\')">
                        <i class="fas fa-play"></i>
                    </button>
                </div>
                <div class="video-duration">${youtubeApi.formatDuration(video.duration)}</div>
            </div>
            <div class="featured-info">
                <h3 class="featured-title">${video.title}</h3>
                <p class="featured-channel">${video.channelTitle}</p>
                <div class="featured-stats">
                    <span class="views">${youtubeApi.formatViewCount(video.statistics.viewCount)} views</span>
                    <span class="likes">${youtubeApi.formatViewCount(video.statistics.likeCount)} likes</span>
                </div>
            </div>
            <div class="featured-actions">
                <button class="action-btn play" onclick="playVideo(\'${video.id}\')">
                    <i class="fas fa-play"></i>
                </button>
                <button class="action-btn like" onclick="likeVideo(\'${video.id}\')">
                    <i class="fas fa-heart"></i>
                </button>
                <button class="action-btn share" onclick="shareVideo(\'${video.id}\')">
                    <i class="fas fa-share"></i>
                </button>
            </div>
        `;
        container.appendChild(featuredItem);
    });
}

// Display videos grid
function displayVideosGrid(videos, container) {
    container.innerHTML = "";
    
    videos.forEach((video, index) => {
        const videoItem = document.createElement("div");
        videoItem.className = "video-item";
        videoItem.innerHTML = `
            <div class="video-rank">${index + 1}</div>
            <div class="video-thumbnail">
                <img src="${video.thumbnail.medium}" alt="${video.title}">
                <div class="play-overlay">
                    <button class="play-btn" onclick="playVideo(\'${video.id}\')">
                        <i class="fas fa-play"></i>
                    </button>
                </div>
                <div class="video-duration">${youtubeApi.formatDuration(video.duration)}</div>
            </div>
            <div class="video-info">
                <h4 class="video-title">${video.title}</h4>
                <p class="video-channel">${video.channelTitle}</p>
                <div class="video-stats">
                    <span class="views">${youtubeApi.formatViewCount(video.statistics.viewCount)} views</span>
                    <span class="likes">${youtubeApi.formatViewCount(video.statistics.likeCount)} likes</span>
                </div>
            </div>
            <div class="video-actions">
                <button class="action-btn play" onclick="playVideo(\'${video.id}\')">
                    <i class="fas fa-play"></i>
                </button>
                <button class="action-btn like" onclick="likeVideo(\'${video.id}\')">
                    <i class="fas fa-heart"></i>
                </button>
                <button class="action-btn share" onclick="shareVideo(\'${video.id}\')">
                    <i class="fas fa-share"></i>
                </button>
            </div>
        `;
        container.appendChild(videoItem);
    });
}

// Load African spotlight
async function loadAfricanSpotlight(type = "all") {
    const grid = document.getElementById("african-spotlight-grid");
    
    try {
        const youtubeApi = window.youtubeApi;
        let videos;
        
        if (type === "all") {
            videos = await youtubeApi.getTrendingVideos("ZW", 12); // Zimbabwe as default for African content
        } else {
            // Search for specific type of African videos
            const searchQuery = `african ${type} music video`;
            videos = await youtubeApi.searchVideos(searchQuery, "ZW", 12);
        }
        
        displayAfricanSpotlight(videos, grid);
    } catch (error) {
        console.error("Error loading African spotlight:", error);
    }
}

// Display African spotlight
function displayAfricanSpotlight(videos, container) {
    container.innerHTML = "";
    
    videos.forEach((video, index) => {
        const spotlightItem = document.createElement("div");
        spotlightItem.className = "spotlight-item";
        spotlightItem.innerHTML = `
            <div class="spotlight-thumbnail">
                <img src="${video.thumbnail.high}" alt="${video.title}">
                <div class="spotlight-overlay">
                    <button class="play-btn" onclick="playVideo(\'${video.id}\')">
                        <i class="fas fa-play"></i>
                    </button>
                </div>
                <div class="video-duration">${youtubeApi.formatDuration(video.duration)}</div>
                ${video.isZimbabwean ? \'<span class="zimbabwe-badge">🇿🇼</span>\' : \'\'}
                ${video.isAfrican ? \'<span class="african-badge">🌍</span>\' : \'\'}
            </div>
            <div class="spotlight-info">
                <h4>${video.title}</h4>
                <p>${video.channelTitle}</p>
                <div class="spotlight-stats">
                    <span class="views">${youtubeApi.formatViewCount(video.statistics.viewCount)} views</span>
                </div>
            </div>
        `;
        container.appendChild(spotlightItem);
    });
}

// Update video statistics
function updateVideoStatistics(videos) {
    const totalVideos = document.getElementById("total-videos");
    const totalViews = document.getElementById("total-views");
    const totalLikes = document.getElementById("total-likes");
    const totalDuration = document.getElementById("total-duration");
    
    if (totalVideos) {
        animateCounter(totalVideos, videos.length);
    }
    
    if (totalViews) {
        const totalViewCount = videos.reduce((sum, video) => sum + video.statistics.viewCount, 0);
        totalViews.textContent = youtubeApi.formatViewCount(totalViewCount);
    }
    
    if (totalLikes) {
        const totalLikeCount = videos.reduce((sum, video) => sum + video.statistics.likeCount, 0);
        totalLikes.textContent = youtubeApi.formatViewCount(totalLikeCount);
    }
    
    if (totalDuration) {
        // Calculate total duration (simplified)
        const avgDuration = videos.reduce((sum, video) => sum + parseDuration(video.duration), 0) / videos.length;
        totalDuration.textContent = formatDuration(avgDuration);
    }
}

// Initialize videos page
function initVideosPage() {
    // Initialize regional selector
    const regionTabs = document.querySelectorAll(\'[data-region]\');
    regionTabs.forEach(tab => {
        tab.addEventListener("click", () => {
            regionTabs.forEach(t => t.classList.remove("active"));
            tab.classList.add("active");
            
            const region = tab.getAttribute("data-region");
            loadVideosData(region);
        });
    });
}

// Initialize filters
function initFilters() {
    const categoryFilter = document.getElementById("category-filter");
    const durationFilter = document.getElementById("duration-filter");
    const sortFilter = document.getElementById("sort-filter");
    
    if (categoryFilter) {
        categoryFilter.addEventListener("change", () => {
            filterVideosByCategory(categoryFilter.value);
        });
    }
    
    if (durationFilter) {
        durationFilter.addEventListener("change", () => {
            filterVideosByDuration(durationFilter.value);
        });
    }
    
    if (sortFilter) {
        sortFilter.addEventListener("change", () => {
            sortVideosBy(sortFilter.value);
        });
    }
}

// Initialize spotlight tabs
function initSpotlightTabs() {
    const tabs = document.querySelectorAll(".spotlight-tab");
    tabs.forEach(tab => {
        tab.addEventListener("click", () => {
            tabs.forEach(t => t.classList.remove("active"));
            tab.classList.add("active");
            
            const type = tab.getAttribute("data-type");
            loadAfricanSpotlight(type);
        });
    });
}

// Filter videos by category
function filterVideosByCategory(category) {
    console.log("Filtering by category:", category);
    // Implementation for filtering by category
}

// Filter videos by duration
function filterVideosByDuration(duration) {
    console.log("Filtering by duration:", duration);
    // Implementation for filtering by duration
}

// Sort videos by criteria
function sortVideosBy(criteria) {
    console.log("Sorting by:", criteria);
    // Implementation for sorting videos
}

// Filter by category from category cards
function filterByCategory(category) {
    const categoryFilter = document.getElementById("category-filter");
    if (categoryFilter) {
        categoryFilter.value = category;
        filterVideosByCategory(category);
    }
}

// Refresh videos
function refreshVideos() {
    loadVideosData();
}

// Load more videos
function loadMoreVideos() {
    console.log("Loading more videos...");
    // Implementation for loading more videos
}

// Play video
function playVideo(videoId) {
    window.open(`https://www.youtube.com/watch?v=${videoId}`, \'_blank\');
}

// Like video
function likeVideo(videoId) {
    console.log("Liking video:", videoId);
    // Implementation for liking videos
}

// Share video
function shareVideo(videoId) {
    if (navigator.share) {
        navigator.share({
            title: "Check out this video",
            url: `https://www.youtube.com/watch?v=${videoId}`
        });
    } else {
        navigator.clipboard.writeText(`https://www.youtube.com/watch?v=${videoId}`);
        showNotification("Video URL copied to clipboard!");
    }
}

// Parse duration from ISO 8601 format
function parseDuration(duration) {
    const match = duration.match(/PT(?:(\d+)H)?(?:(\d+)M)?(?:(\d+)S)?/);
    if (!match) return 0;
    
    const hours = parseInt(match[1] || 0);
    const minutes = parseInt(match[2] || 0);
    const seconds = parseInt(match[3] || 0);
    
    return hours * 3600 + minutes * 60 + seconds;
}

// Format duration in seconds to readable format
function formatDuration(seconds) {
    const hours = Math.floor(seconds / 3600);
    const minutes = Math.floor((seconds % 3600) / 60);
    const secs = Math.floor(seconds % 60);
    
    if (hours > 0) {
        return `${hours}h ${minutes}m`;
    } else if (minutes > 0) {
        return `${minutes}m ${secs}s`;
    } else {
        return `${secs}s`;
    }
}

// Animate counter
function animateCounter(element, target) {
    const start = 0;
    const duration = 2000;
    const increment = target / (duration / 16);
    let current = start;
    
    const timer = setInterval(() => {
        current += increment;
        if (current >= target) {
            current = target;
            clearInterval(timer);
        }
        element.textContent = Math.floor(current);
    }, 16);
}

// Show error
function showError(message) {
    console.error(message);
    // Implementation for showing error messages
}

// Show notification
function showNotification(message) {
    console.log(message);
    // Implementation for showing notifications
}
</script>
';

// Include footer
include 'includes/footer.php';
?>
