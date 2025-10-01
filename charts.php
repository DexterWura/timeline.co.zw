<?php
session_start();

// Page configuration
$page_title = 'Timeline Hot 100 - African Music Charts | Timeline.co.zw';
$page_description = 'Discover the hottest music from Africa and around the world. Real-time music charts featuring trending songs from Zimbabwe, Nigeria, South Africa, and more.';
$page_keywords = 'music charts, hot 100, african music, trending songs, zimbabwe music, nigeria music, south africa music, afrobeats, amapiano';
$canonical_url = 'https://timeline.co.zw/charts.php';
$body_class = 'charts-page';

// Additional CSS files
$additional_css = [
    'css/charts.css',
    'css/animations.css'
];

// Additional JavaScript files
$additional_js = [
    'js/youtubeApi.js',
    'js/locationService.js',
    'js/charts.js'
];

// Page-specific meta tags
$page_specific_meta = '
    <meta property="og:image" content="https://timeline.co.zw/images/music-charts-hero.jpg">
    <meta name="twitter:image" content="https://timeline.co.zw/images/music-charts-hero.jpg">
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
                        <a href="charts.php" class="nav-link active">Charts</a>
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

    <!-- Page Header -->
    <section class="page-header">
        <div class="container">
            <div class="page-header-content">
                <h1 class="page-title">
                    <i class="fas fa-chart-line"></i>
                    Timeline Hot 100™
                    <span class="african-badge" style="display: none;">🇿🇼</span>
                </h1>
                <p class="page-description">
                    The definitive ranking of the most popular songs from Africa and around the world, updated in real-time.
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

    <!-- Featured Top 3 -->
    <section class="featured-top3">
        <div class="container">
            <h2 class="section-title">Top 3 This Week</h2>
            <div class="featured-grid" id="featured-top3">
                <!-- Top 3 songs will be loaded here -->
                <div class="loading-placeholder">
                    <div class="loading-spinner"></div>
                    <p>Loading top songs...</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Charts Section -->
    <section class="charts-section">
        <div class="container">
            <div class="charts-header">
                <h2 class="section-title">Hot 100 Chart</h2>
                <div class="charts-controls">
                    <div class="filter-group">
                        <label for="genre-filter">Genre:</label>
                        <select id="genre-filter" class="filter-select">
                            <option value="all">All Genres</option>
                            <option value="afrobeats">Afrobeats</option>
                            <option value="amapiano">Amapiano</option>
                            <option value="zimdancehall">Zimdancehall</option>
                            <option value="chimurenga">Chimurenga</option>
                            <option value="sungura">Sungura</option>
                            <option value="pop">Pop</option>
                            <option value="hip-hop">Hip-Hop</option>
                            <option value="r&b">R&B</option>
                        </select>
                    </div>
                    <div class="filter-group">
                        <label for="time-filter">Time Period:</label>
                        <select id="time-filter" class="filter-select">
                            <option value="week">This Week</option>
                            <option value="month">This Month</option>
                            <option value="year">This Year</option>
                            <option value="all-time">All Time</option>
                        </select>
                    </div>
                    <button class="refresh-btn" onclick="refreshCharts()">
                        <i class="fas fa-sync-alt"></i>
                        Refresh
                    </button>
                </div>
            </div>

            <!-- Loading State -->
            <div class="charts-loading" id="charts-loading">
                <div class="loading-spinner"></div>
                <p>Loading music charts...</p>
            </div>

            <!-- Charts Grid -->
            <div class="charts-grid" id="charts-grid">
                <!-- Chart items will be loaded here -->
            </div>

            <!-- Load More Button -->
            <div class="load-more-container">
                <button class="load-more-btn" id="load-more-btn" onclick="loadMoreCharts()">
                    <i class="fas fa-plus"></i>
                    Load More
                </button>
            </div>
        </div>
    </section>

    <!-- Chart Statistics -->
    <section class="chart-stats">
        <div class="container">
            <h2 class="section-title">Chart Statistics</h2>
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-music"></i>
                    </div>
                    <div class="stat-content">
                        <h3 id="total-songs">0</h3>
                        <p>Total Songs</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="stat-content">
                        <h3 id="total-artists">0</h3>
                        <p>Unique Artists</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-globe-africa"></i>
                    </div>
                    <div class="stat-content">
                        <h3 id="african-songs">0</h3>
                        <p>African Songs</p>
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
            </div>
        </div>
    </section>

    <!-- African Music Spotlight -->
    <section class="african-spotlight">
        <div class="container">
            <h2 class="section-title">
                <i class="fas fa-globe-africa"></i>
                African Music Spotlight
            </h2>
            <div class="spotlight-tabs">
                <button class="spotlight-tab active" data-country="all">All Africa</button>
                <button class="spotlight-tab" data-country="zimbabwe">Zimbabwe 🇿🇼</button>
                <button class="spotlight-tab" data-country="nigeria">Nigeria 🇳🇬</button>
                <button class="spotlight-tab" data-country="south-africa">South Africa 🇿🇦</button>
                <button class="spotlight-tab" data-country="kenya">Kenya 🇰🇪</button>
                <button class="spotlight-tab" data-country="ghana">Ghana 🇬🇭</button>
            </div>
            <div class="spotlight-grid" id="african-spotlight-grid">
                <!-- African music content will be loaded here -->
            </div>
        </div>
    </section>

<?php
// Page-specific scripts
$page_specific_scripts = '
<script>
// Initialize charts page functionality
document.addEventListener("DOMContentLoaded", async function() {
    // Initialize location service
    await window.locationService.initialize();
    
    // Update regional selector
    updateRegionalSelector();
    
    // Load charts data
    await loadChartsData();
    
    // Load African spotlight
    await loadAfricanSpotlight();
    
    // Initialize page functionality
    initChartsPage();
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

// Load charts data
async function loadChartsData(region = null) {
    const loading = document.getElementById("charts-loading");
    const chartsGrid = document.getElementById("charts-grid");
    const featuredGrid = document.getElementById("featured-top3");
    
    loading.style.display = "block";
    
    try {
        const locationService = window.locationService;
        const youtubeApi = window.youtubeApi;
        
        // Determine region
        const selectedRegion = region || locationService.getContentPreferences().primaryRegion;
        
        // Load top 100 music
        const music = await youtubeApi.getTop100Music(selectedRegion);
        
        // Display featured top 3
        displayFeaturedTop3(music.slice(0, 3), featuredGrid);
        
        // Display charts grid
        displayChartsGrid(music, chartsGrid);
        
        // Update statistics
        updateChartStatistics(music);
        
    } catch (error) {
        console.error("Error loading charts data:", error);
        showError("Failed to load music charts. Please try again.");
    } finally {
        loading.style.display = "none";
    }
}

// Display featured top 3
function displayFeaturedTop3(top3, container) {
    container.innerHTML = "";
    
    top3.forEach((song, index) => {
        const featuredItem = document.createElement("div");
        featuredItem.className = `featured-item rank-${index + 1}`;
        featuredItem.innerHTML = `
            <div class="featured-rank">${index + 1}</div>
            <div class="featured-artwork">
                <img src="${song.thumbnail.high}" alt="${song.title}">
                <div class="play-overlay">
                    <button class="play-btn" onclick="playVideo(\'${song.id}\')">
                        <i class="fas fa-play"></i>
                    </button>
                </div>
            </div>
            <div class="featured-info">
                <h3 class="featured-title">${song.title}</h3>
                <p class="featured-artist">${song.channelTitle}</p>
                <div class="featured-stats">
                    <span class="views">${youtubeApi.formatViewCount(song.statistics.viewCount)} views</span>
                    <span class="duration">${youtubeApi.formatDuration(song.duration)}</span>
                </div>
            </div>
            <div class="featured-actions">
                <button class="action-btn play" onclick="playVideo(\'${song.id}\')">
                    <i class="fas fa-play"></i>
                </button>
                <button class="action-btn add" onclick="addToPlaylist(\'${song.id}\')">
                    <i class="fas fa-plus"></i>
                </button>
                <button class="action-btn share" onclick="shareSong(\'${song.id}\')">
                    <i class="fas fa-share"></i>
                </button>
            </div>
        `;
        container.appendChild(featuredItem);
    });
}

// Display charts grid
function displayChartsGrid(music, container) {
    container.innerHTML = "";
    
    music.forEach((song, index) => {
        const chartItem = document.createElement("div");
        chartItem.className = "chart-item";
        chartItem.innerHTML = `
            <div class="chart-rank">${index + 1}</div>
            <div class="chart-artwork">
                <img src="${song.thumbnail.medium}" alt="${song.title}">
                <div class="play-overlay">
                    <button class="play-btn" onclick="playVideo(\'${song.id}\')">
                        <i class="fas fa-play"></i>
                    </button>
                </div>
            </div>
            <div class="chart-info">
                <h4 class="chart-title">${song.title}</h4>
                <p class="chart-artist">${song.channelTitle}</p>
                <div class="chart-stats">
                    <span class="views">${youtubeApi.formatViewCount(song.statistics.viewCount)} views</span>
                    <span class="duration">${youtubeApi.formatDuration(song.duration)}</span>
                </div>
            </div>
            <div class="chart-actions">
                <button class="action-btn play" onclick="playVideo(\'${song.id}\')">
                    <i class="fas fa-play"></i>
                </button>
                <button class="action-btn add" onclick="addToPlaylist(\'${song.id}\')">
                    <i class="fas fa-plus"></i>
                </button>
                <button class="action-btn share" onclick="shareSong(\'${song.id}\')">
                    <i class="fas fa-share"></i>
                </button>
            </div>
        `;
        container.appendChild(chartItem);
    });
}

// Load African spotlight
async function loadAfricanSpotlight(country = "all") {
    const grid = document.getElementById("african-spotlight-grid");
    
    try {
        const youtubeApi = window.youtubeApi;
        let music;
        
        if (country === "zimbabwe") {
            music = await youtubeApi.getZimbabweanMusic(12);
        } else if (country === "all") {
            music = await youtubeApi.getAfricanMusic(12);
        } else {
            // For other countries, search for country-specific music
            music = await youtubeApi.searchVideos(`${country} music`, country.toUpperCase(), 12);
        }
        
        displayAfricanSpotlight(music, grid);
    } catch (error) {
        console.error("Error loading African spotlight:", error);
    }
}

// Display African spotlight
function displayAfricanSpotlight(music, container) {
    container.innerHTML = "";
    
    music.forEach((song, index) => {
        const spotlightItem = document.createElement("div");
        spotlightItem.className = "spotlight-item";
        spotlightItem.innerHTML = `
            <div class="spotlight-artwork">
                <img src="${song.thumbnail.high}" alt="${song.title}">
                <div class="spotlight-overlay">
                    <button class="play-btn" onclick="playVideo(\'${song.id}\')">
                        <i class="fas fa-play"></i>
                    </button>
                </div>
                ${song.isZimbabwean ? \'<span class="zimbabwe-badge">🇿🇼</span>\' : \'\'}
                ${song.isAfrican ? \'<span class="african-badge">🌍</span>\' : \'\'}
            </div>
            <div class="spotlight-info">
                <h4>${song.title}</h4>
                <p>${song.channelTitle}</p>
                <div class="spotlight-stats">
                    <span class="views">${youtubeApi.formatViewCount(song.statistics.viewCount)} views</span>
                </div>
            </div>
        `;
        container.appendChild(spotlightItem);
    });
}

// Update chart statistics
function updateChartStatistics(music) {
    const totalSongs = document.getElementById("total-songs");
    const totalArtists = document.getElementById("total-artists");
    const africanSongs = document.getElementById("african-songs");
    const totalViews = document.getElementById("total-views");
    
    if (totalSongs) {
        animateCounter(totalSongs, music.length);
    }
    
    if (totalArtists) {
        const uniqueArtists = new Set(music.map(song => song.channelTitle)).size;
        animateCounter(totalArtists, uniqueArtists);
    }
    
    if (africanSongs) {
        const africanCount = music.filter(song => song.isAfrican).length;
        animateCounter(africanSongs, africanCount);
    }
    
    if (totalViews) {
        const totalViewCount = music.reduce((sum, song) => sum + song.statistics.viewCount, 0);
        totalViews.textContent = youtubeApi.formatViewCount(totalViewCount);
    }
}

// Initialize charts page
function initChartsPage() {
    // Initialize regional selector
    const regionTabs = document.querySelectorAll(\'[data-region]\');
    regionTabs.forEach(tab => {
        tab.addEventListener("click", () => {
            regionTabs.forEach(t => t.classList.remove("active"));
            tab.classList.add("active");
            
            const region = tab.getAttribute("data-region");
            loadChartsData(region);
        });
    });
}

// Initialize filters
function initFilters() {
    const genreFilter = document.getElementById("genre-filter");
    const timeFilter = document.getElementById("time-filter");
    
    if (genreFilter) {
        genreFilter.addEventListener("change", () => {
            filterChartsByGenre(genreFilter.value);
        });
    }
    
    if (timeFilter) {
        timeFilter.addEventListener("change", () => {
            filterChartsByTime(timeFilter.value);
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
            
            const country = tab.getAttribute("data-country");
            loadAfricanSpotlight(country);
        });
    });
}

// Filter charts by genre
function filterChartsByGenre(genre) {
    // Implementation for filtering by genre
    console.log("Filtering by genre:", genre);
}

// Filter charts by time
function filterChartsByTime(time) {
    // Implementation for filtering by time period
    console.log("Filtering by time:", time);
}

// Refresh charts
function refreshCharts() {
    loadChartsData();
}

// Load more charts
function loadMoreCharts() {
    // Implementation for loading more charts
    console.log("Loading more charts...");
}

// Play video
function playVideo(videoId) {
    window.open(`https://www.youtube.com/watch?v=${videoId}`, \'_blank\');
}

// Add to playlist
function addToPlaylist(videoId) {
    console.log("Adding to playlist:", videoId);
}

// Share song
function shareSong(videoId) {
    if (navigator.share) {
        navigator.share({
            title: "Check out this song",
            url: `https://www.youtube.com/watch?v=${videoId}`
        });
    } else {
        navigator.clipboard.writeText(`https://www.youtube.com/watch?v=${videoId}`);
        showNotification("Song URL copied to clipboard!");
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
