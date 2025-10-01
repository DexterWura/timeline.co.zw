<?php
session_start();

// Page configuration
$page_title = 'Top 100 Richest People - Real-Time Billionaire Rankings | Timeline.co.zw';
$page_description = 'Discover the world\'s wealthiest individuals with real-time billionaire rankings. Track wealth changes, view detailed profiles, and explore the global elite.';
$page_keywords = 'richest people, billionaires, wealth rankings, real-time wealth, fortune tracking, global elite, wealth statistics';
$canonical_url = 'https://timeline.co.zw/richest.php';
$body_class = 'richest-page';

// Additional CSS files
$additional_css = [
    'css/richest.css',
    'css/animations.css'
];

// Additional JavaScript files
$additional_js = [
    'js/billionaireApi.js',
    'js/imageService.js',
    'js/images.js',
    'js/richest.js'
];

// Page-specific meta tags
$page_specific_meta = '
    <meta property="og:image" content="https://timeline.co.zw/images/billionaires-hero.jpg">
    <meta name="twitter:image" content="https://timeline.co.zw/images/billionaires-hero.jpg">
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
                        <a href="videos.php" class="nav-link">Videos</a>
                        <a href="richest.php" class="nav-link active">Richest</a>
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
                    <a href="richest.php" class="chart-nav-link active">TOP 100 RICHEST</a>
                    <a href="richest.php" class="chart-nav-link">TECH BILLIONAIRES</a>
                    <a href="richest.php" class="chart-nav-link">ENTERTAINMENT</a>
                    <a href="richest.php" class="chart-nav-link">REAL ESTATE</a>
                </div>
            </div>
        </div>
    </header>

    <!-- Page Header -->
    <section class="page-header">
        <div class="container">
            <div class="page-header-content">
                <h1 class="page-title">
                    <i class="fas fa-crown"></i>
                    Top 100 Richest People
                    <span class="real-time-badge">LIVE</span>
                </h1>
                <p class="page-description">
                    Real-time billionaire rankings with live wealth tracking. Discover the world's wealthiest individuals and their sources of fortune.
                </p>
                
                <!-- Last Updated -->
                <div class="last-updated">
                    <i class="fas fa-clock"></i>
                    <span>Last updated: <span id="last-updated-time">Loading...</span></span>
                </div>
            </div>
        </div>
    </section>

    <!-- Featured Top 3 Billionaires -->
    <section class="featured-billionaires">
        <div class="container">
            <h2 class="section-title">Top 3 This Week</h2>
            <div class="featured-grid" id="featured-billionaires">
                <!-- Top 3 billionaires will be loaded here -->
                <div class="loading-placeholder">
                    <div class="loading-spinner"></div>
                    <p>Loading billionaire data...</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Billionaires Section -->
    <section class="billionaires-section">
        <div class="container">
            <div class="billionaires-header">
                <h2 class="section-title">Billionaire Rankings</h2>
                <div class="billionaires-controls">
                    <div class="filter-group">
                        <label for="industry-filter">Industry:</label>
                        <select id="industry-filter" class="filter-select">
                            <option value="all">All Industries</option>
                            <option value="technology">Technology</option>
                            <option value="finance">Finance</option>
                            <option value="retail">Retail</option>
                            <option value="manufacturing">Manufacturing</option>
                            <option value="real-estate">Real Estate</option>
                            <option value="media">Media</option>
                            <option value="energy">Energy</option>
                            <option value="healthcare">Healthcare</option>
                        </select>
                    </div>
                    <div class="filter-group">
                        <label for="country-filter">Country:</label>
                        <select id="country-filter" class="filter-select">
                            <option value="all">All Countries</option>
                            <option value="usa">United States</option>
                            <option value="china">China</option>
                            <option value="india">India</option>
                            <option value="france">France</option>
                            <option value="germany">Germany</option>
                            <option value="uk">United Kingdom</option>
                            <option value="canada">Canada</option>
                            <option value="australia">Australia</option>
                        </select>
                    </div>
                    <div class="filter-group">
                        <label for="sort-filter">Sort by:</label>
                        <select id="sort-filter" class="filter-select">
                            <option value="net-worth">Net Worth</option>
                            <option value="wealth-change">Wealth Change</option>
                            <option value="age">Age</option>
                            <option value="name">Name</option>
                        </select>
                    </div>
                    <button class="refresh-btn" onclick="refreshBillionaireData()">
                        <i class="fas fa-sync-alt"></i>
                        Refresh
                    </button>
                </div>
            </div>

            <!-- Loading State -->
            <div class="billionaires-loading" id="billionaires-loading">
                <div class="loading-spinner"></div>
                <p>Loading billionaire data...</p>
            </div>

            <!-- Billionaires Grid -->
            <div class="billionaires-grid" id="billionaires-container">
                <!-- Billionaire items will be loaded here -->
            </div>

            <!-- Load More Button -->
            <div class="load-more-container">
                <button class="load-more-btn" id="load-more-btn" onclick="loadMoreBillionaires()">
                    <i class="fas fa-plus"></i>
                    Load More
                </button>
            </div>
        </div>
    </section>

    <!-- Wealth Statistics -->
    <section class="wealth-stats">
        <div class="container">
            <h2 class="section-title">Wealth Statistics</h2>
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="stat-content">
                        <h3 id="total-billionaires">0</h3>
                        <p>Total Billionaires</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-dollar-sign"></i>
                    </div>
                    <div class="stat-content">
                        <h3 id="total-wealth">$0T</h3>
                        <p>Combined Wealth</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <div class="stat-content">
                        <h3 id="avg-wealth">$0B</h3>
                        <p>Average Wealth</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-globe-africa"></i>
                    </div>
                    <div class="stat-content">
                        <h3 id="african-billionaires">0</h3>
                        <p>African Billionaires</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Industry Breakdown -->
    <section class="industry-breakdown">
        <div class="container">
            <h2 class="section-title">Wealth by Industry</h2>
            <div class="industry-grid" id="industry-breakdown">
                <!-- Industry breakdown will be loaded here -->
            </div>
        </div>
    </section>

    <!-- Country Breakdown -->
    <section class="country-breakdown">
        <div class="container">
            <h2 class="section-title">Wealth by Country</h2>
            <div class="country-grid" id="country-breakdown">
                <!-- Country breakdown will be loaded here -->
            </div>
        </div>
    </section>

    <!-- African Billionaires Spotlight -->
    <section class="african-spotlight">
        <div class="container">
            <h2 class="section-title">
                <i class="fas fa-globe-africa"></i>
                African Billionaires Spotlight
            </h2>
            <div class="spotlight-grid" id="african-billionaires-grid">
                <!-- African billionaires will be loaded here -->
            </div>
        </div>
    </section>

    <!-- Billionaire Modal -->
    <div class="billionaire-modal" id="billionaire-modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 id="modal-name">Billionaire Name</h3>
                <button class="modal-close" onclick="closeBillionaireModal()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body">
                <div class="modal-photo">
                    <img id="modal-photo" src="" alt="">
                </div>
                <div class="modal-info">
                    <div class="modal-stats">
                        <div class="stat">
                            <span class="stat-label">Net Worth</span>
                            <span class="stat-value" id="modal-net-worth">$0B</span>
                        </div>
                        <div class="stat">
                            <span class="stat-label">Wealth Change</span>
                            <span class="stat-value" id="modal-wealth-change">$0B</span>
                        </div>
                        <div class="stat">
                            <span class="stat-label">Age</span>
                            <span class="stat-value" id="modal-age">0</span>
                        </div>
                        <div class="stat">
                            <span class="stat-label">Country</span>
                            <span class="stat-value" id="modal-country">Unknown</span>
                        </div>
                    </div>
                    <div class="modal-details">
                        <h4>Source of Wealth</h4>
                        <p id="modal-source">Unknown</p>
                        <h4>Industry</h4>
                        <p id="modal-industry">Unknown</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

<?php
// Page-specific scripts
$page_specific_scripts = '
<script>
// Initialize richest page functionality
document.addEventListener("DOMContentLoaded", async function() {
    // Initialize billionaire API
    await initRichestPage();
    
    // Update last updated time
    updateLastUpdatedTime();
    
    // Set up auto-refresh
    setInterval(updateLastUpdatedTime, 60000); // Update every minute
});

// Initialize richest page
async function initRichestPage() {
    try {
        // Load billionaire data
        await loadRichestData();
        
        // Initialize filters
        initFilters();
        
        // Initialize modal
        initModal();
        
        // Initialize interactions
        initRichestInteractions();
        
        // Initialize real-time updates
        initRealTimeUpdates();
        
    } catch (error) {
        console.error("Error initializing richest page:", error);
        showError("Failed to initialize billionaire data. Please try again.");
    }
}

// Load and display richest people data
async function loadRichestData() {
    const richestContainer = document.getElementById("billionaires-container");
    const featuredContainer = document.getElementById("featured-billionaires");
    const loading = document.getElementById("billionaires-loading");
    
    if (!richestContainer) return;
    
    // Show loading state
    loading.style.display = "block";
    
    try {
        // Fetch real-time data from API
        console.log("Fetching real-time billionaire data...");
        const realData = await window.billionaireApi.getLatestBillionaires(100);
        
        if (realData && realData.length > 0) {
            console.log(`Successfully loaded ${realData.length} billionaires from API`);
            
            // Display the real data (skip first 3 for featured section)
            displayRichestPeople(realData.slice(3));
            
            // Update featured billionaires (top 3)
            updateFeaturedBillionaires(realData.slice(0, 3));
            
            // Update statistics
            updateWealthStatistics(realData);
            
            // Update industry breakdown
            updateIndustryBreakdown(realData);
            
            // Update country breakdown
            updateCountryBreakdown(realData);
            
            // Load African billionaires
            loadAfricanBillionaires(realData);
            
            // Show success message
            showNotification("Real-time billionaire data loaded successfully!", "success");
            
        } else {
            throw new Error("No data received from API");
        }
        
    } catch (error) {
        console.error("Error loading real-time billionaire data:", error);
        
        // Show error message instead of fallback
        richestContainer.innerHTML = `
            <div class="error-message">
                <div class="error-icon">⚠️</div>
                <h3>Unable to Load Real-Time Data</h3>
                <p>The billionaire API is currently unavailable. This could be due to:</p>
                <ul>
                    <li>Network connectivity issues</li>
                    <li>API service maintenance</li>
                    <li>CORS restrictions</li>
                </ul>
                <div class="error-actions">
                    <button onclick="location.reload()" class="retry-button">Retry</button>
                    <button onclick="window.billionaireApi.clearCache(); location.reload()" class="clear-cache-button">Clear Cache & Retry</button>
                </div>
                <p class="error-note">Please check your internet connection and try again.</p>
            </div>
        `;
        
        showNotification("Failed to load real-time billionaire data. Please try again.", "error");
    } finally {
        loading.style.display = "none";
    }
}

// Update featured billionaires (top 3)
function updateFeaturedBillionaires(topThree) {
    const featuredContainer = document.getElementById("featured-billionaires");
    if (!featuredContainer || !topThree || topThree.length === 0) return;
    
    featuredContainer.innerHTML = "";
    
    topThree.forEach((person, index) => {
        const featuredItem = document.createElement("div");
        featuredItem.className = `featured-item rank-${index + 1}`;
        featuredItem.innerHTML = `
            <div class="featured-rank">${person.rank || (index + 1)}</div>
            <div class="featured-photo">
                <img src="${person.photo}" alt="${person.name}">
            </div>
            <div class="featured-info">
                <h3 class="featured-name">${person.name}</h3>
                <p class="featured-source">${person.source || person.industry || "Unknown"}</p>
                <div class="featured-wealth">$${formatWealth(person.netWorth || person.netWorthRaw || 0)}</div>
                <div class="featured-change ${person.wealthChange >= 0 ? "positive" : "negative"}">
                    ${person.wealthChange >= 0 ? "+" : ""}$${formatWealth(Math.abs(person.wealthChange || 0))}
                </div>
            </div>
            <div class="featured-actions">
                <button class="action-btn view" onclick="viewBillionaireProfile(\'${person.id}\')">
                    <i class="fas fa-eye"></i>
                </button>
                <button class="action-btn share" onclick="shareBillionaire(\'${person.id}\')">
                    <i class="fas fa-share"></i>
                </button>
            </div>
        `;
        featuredContainer.appendChild(featuredItem);
    });
}

// Display richest people
function displayRichestPeople(people) {
    const richestContainer = document.getElementById("billionaires-container");
    if (!richestContainer) return;
    
    richestContainer.innerHTML = "";
    
    people.forEach((person, index) => {
        const richestItem = createRichestItem(person, index + 4); // Start from rank 4
        richestContainer.appendChild(richestItem);
    });
    
    // Add stagger animation
    const richestItems = richestContainer.querySelectorAll(".billionaire-item");
    richestItems.forEach((item, index) => {
        setTimeout(() => {
            item.classList.add("animate-slide-up");
        }, index * 100);
    });
}

// Create richest item element
function createRichestItem(person, rank) {
    const item = document.createElement("div");
    item.className = "billionaire-item";
    item.innerHTML = `
        <div class="billionaire-rank">${rank}</div>
        <div class="billionaire-photo">
            <img src="${person.photo}" alt="${person.name}">
        </div>
        <div class="billionaire-info">
            <h4 class="billionaire-name">${person.name}</h4>
            <p class="billionaire-source">${person.source || person.industry || "Unknown"}</p>
            <div class="billionaire-stats">
                <span class="net-worth">$${formatWealth(person.netWorth || person.netWorthRaw || 0)}</span>
                <span class="wealth-change ${person.wealthChange >= 0 ? "positive" : "negative"}">
                    ${person.wealthChange >= 0 ? "+" : ""}$${formatWealth(Math.abs(person.wealthChange || 0))}
                </span>
            </div>
        </div>
        <div class="billionaire-actions">
            <button class="action-btn view" onclick="viewBillionaireProfile(\'${person.id}\')">
                <i class="fas fa-eye"></i>
            </button>
            <button class="action-btn share" onclick="shareBillionaire(\'${person.id}\')">
                <i class="fas fa-share"></i>
            </button>
        </div>
    `;
    return item;
}

// Update wealth statistics
function updateWealthStatistics(billionaires) {
    const totalBillionaires = document.getElementById("total-billionaires");
    const totalWealth = document.getElementById("total-wealth");
    const avgWealth = document.getElementById("avg-wealth");
    const africanBillionaires = document.getElementById("african-billionaires");
    
    if (totalBillionaires) {
        animateCounter(totalBillionaires, billionaires.length);
    }
    
    if (totalWealth) {
        const combinedWealth = billionaires.reduce((sum, person) => sum + (person.netWorth || person.netWorthRaw || 0), 0);
        totalWealth.textContent = "$" + formatWealth(combinedWealth / 1000) + "T";
    }
    
    if (avgWealth) {
        const combinedWealth = billionaires.reduce((sum, person) => sum + (person.netWorth || person.netWorthRaw || 0), 0);
        const average = combinedWealth / billionaires.length;
        avgWealth.textContent = "$" + formatWealth(average) + "B";
    }
    
    if (africanBillionaires) {
        const africanCount = billionaires.filter(person => 
            person.country && ["ZW", "ZA", "NG", "KE", "GH", "EG", "MA", "TN", "DZ", "LY", "SD", "ET", "UG", "TZ", "RW", "BI", "MW", "ZM", "BW", "SZ", "LS", "MZ", "MG", "MU", "SC", "KM", "DJ", "SO", "ER", "SS", "CF", "TD", "NE", "ML", "BF", "CI", "LR", "SL", "GN", "GW", "GM", "SN", "MR", "CV", "AO", "CD", "CG", "GA", "GQ", "ST", "CM", "TG", "BJ"].includes(person.country)
        ).length;
        animateCounter(africanBillionaires, africanCount);
    }
}

// Update industry breakdown
function updateIndustryBreakdown(billionaires) {
    const container = document.getElementById("industry-breakdown");
    if (!container) return;
    
    const industries = {};
    billionaires.forEach(person => {
        const industry = person.sourceCategory || person.industry || "Other";
        if (!industries[industry]) {
            industries[industry] = { count: 0, wealth: 0 };
        }
        industries[industry].count++;
        industries[industry].wealth += person.netWorth || person.netWorthRaw || 0;
    });
    
    container.innerHTML = "";
    Object.entries(industries)
        .sort((a, b) => b[1].wealth - a[1].wealth)
        .slice(0, 8)
        .forEach(([industry, data]) => {
            const item = document.createElement("div");
            item.className = "industry-item";
            item.innerHTML = `
                <div class="industry-name">${industry}</div>
                <div class="industry-stats">
                    <span class="count">${data.count} billionaires</span>
                    <span class="wealth">$${formatWealth(data.wealth)}B</span>
                </div>
            `;
            container.appendChild(item);
        });
}

// Update country breakdown
function updateCountryBreakdown(billionaires) {
    const container = document.getElementById("country-breakdown");
    if (!container) return;
    
    const countries = {};
    billionaires.forEach(person => {
        const country = person.countryName || person.country || "Unknown";
        if (!countries[country]) {
            countries[country] = { count: 0, wealth: 0 };
        }
        countries[country].count++;
        countries[country].wealth += person.netWorth || person.netWorthRaw || 0;
    });
    
    container.innerHTML = "";
    Object.entries(countries)
        .sort((a, b) => b[1].wealth - a[1].wealth)
        .slice(0, 10)
        .forEach(([country, data]) => {
            const item = document.createElement("div");
            item.className = "country-item";
            item.innerHTML = `
                <div class="country-name">${country}</div>
                <div class="country-stats">
                    <span class="count">${data.count} billionaires</span>
                    <span class="wealth">$${formatWealth(data.wealth)}B</span>
                </div>
            `;
            container.appendChild(item);
        });
}

// Load African billionaires
function loadAfricanBillionaires(billionaires) {
    const container = document.getElementById("african-billionaires-grid");
    if (!container) return;
    
    const africanBillionaires = billionaires.filter(person => 
        person.country && ["ZW", "ZA", "NG", "KE", "GH", "EG", "MA", "TN", "DZ", "LY", "SD", "ET", "UG", "TZ", "RW", "BI", "MW", "ZM", "BW", "SZ", "LS", "MZ", "MG", "MU", "SC", "KM", "DJ", "SO", "ER", "SS", "CF", "TD", "NE", "ML", "BF", "CI", "LR", "SL", "GN", "GW", "GM", "SN", "MR", "CV", "AO", "CD", "CG", "GA", "GQ", "ST", "CM", "TG", "BJ"].includes(person.country)
    ).slice(0, 6);
    
    container.innerHTML = "";
    africanBillionaires.forEach(person => {
        const item = document.createElement("div");
        item.className = "spotlight-item";
        item.innerHTML = `
            <div class="spotlight-photo">
                <img src="${person.photo}" alt="${person.name}">
            </div>
            <div class="spotlight-info">
                <h4>${person.name}</h4>
                <p>${person.countryName || person.country || "Unknown"}</p>
                <div class="spotlight-wealth">$${formatWealth(person.netWorth || person.netWorthRaw || 0)}</div>
            </div>
        `;
        container.appendChild(item);
    });
}

// Format wealth
function formatWealth(amount) {
    if (amount >= 100) {
        return (amount / 100).toFixed(1) + "B";
    } else if (amount >= 1) {
        return amount.toFixed(1) + "B";
    } else {
        return (amount * 1000).toFixed(0) + "M";
    }
}

// Update last updated time
function updateLastUpdatedTime() {
    const element = document.getElementById("last-updated-time");
    if (element) {
        element.textContent = new Date().toLocaleString();
    }
}

// View billionaire profile
function viewBillionaireProfile(billionaireId) {
    // Implementation for viewing billionaire profile
    console.log("Viewing profile for:", billionaireId);
}

// Share billionaire
function shareBillionaire(billionaireId) {
    if (navigator.share) {
        navigator.share({
            title: "Check out this billionaire",
            url: window.location.href
        });
    } else {
        navigator.clipboard.writeText(window.location.href);
        showNotification("Billionaire profile URL copied to clipboard!");
    }
}

// Refresh billionaire data
function refreshBillionaireData() {
    loadRichestData();
}

// Load more billionaires
function loadMoreBillionaires() {
    console.log("Loading more billionaires...");
    // Implementation for loading more billionaires
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

// Show notification
function showNotification(message, type = "info") {
    console.log(`${type.toUpperCase()}: ${message}`);
    // Implementation for showing notifications
}

// Show error
function showError(message) {
    console.error(message);
    // Implementation for showing error messages
}
</script>
';

// Include footer
include 'includes/footer.php';
?>
