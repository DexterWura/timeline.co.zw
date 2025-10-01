<?php
session_start();

// Page configuration
$page_title = 'Music Awards - African Music Awards & Recognition | Timeline.co.zw';
$page_description = 'Discover the latest music awards, winners, and nominees from Africa and around the world. Celebrate excellence in African music and entertainment.';
$page_keywords = 'music awards, african awards, grammy awards, mtv awards, billboard awards, music recognition, african music awards, zimbabwe music awards';
$canonical_url = 'https://timeline.co.zw/awards.php';
$body_class = 'awards-page';

// Additional CSS files
$additional_css = [
    'css/awards.css',
    'css/animations.css'
];

// Additional JavaScript files
$additional_js = [
    'js/awards.js'
];

// Page-specific meta tags
$page_specific_meta = '
    <meta property="og:image" content="https://timeline.co.zw/images/awards-hero.jpg">
    <meta name="twitter:image" content="https://timeline.co.zw/images/awards-hero.jpg">
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
                        <a href="richest.php" class="nav-link">Richest</a>
                        <a href="awards.php" class="nav-link active">Awards</a>
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
                    <a href="awards.php" class="chart-nav-link active">MUSIC AWARDS</a>
                    <a href="awards.php" class="chart-nav-link">GRAMMY AWARDS</a>
                    <a href="awards.php" class="chart-nav-link">MTV AWARDS</a>
                    <a href="awards.php" class="chart-nav-link">BILLBOARD AWARDS</a>
                </div>
            </div>
        </div>
    </header>

    <!-- Page Header -->
    <section class="page-header">
        <div class="container">
            <div class="page-header-content">
                <h1 class="page-title">
                    <i class="fas fa-trophy"></i>
                    Music Awards
                    <span class="african-badge" style="display: none;">🇿🇼</span>
                </h1>
                <p class="page-description">
                    Celebrate excellence in African music and entertainment. Discover the latest award winners, nominees, and recognition from across the continent.
                </p>
            </div>
        </div>
    </section>

    <!-- Featured Awards -->
    <section class="featured-awards">
        <div class="container">
            <h2 class="section-title">Featured Awards</h2>
            <div class="featured-grid" id="featured-awards">
                <!-- Featured awards will be loaded here -->
                <div class="loading-placeholder">
                    <div class="loading-spinner"></div>
                    <p>Loading awards data...</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Awards Categories -->
    <section class="awards-categories">
        <div class="container">
            <h2 class="section-title">Award Categories</h2>
            <div class="categories-tabs">
                <button class="category-tab active" data-category="all">All Awards</button>
                <button class="category-tab" data-category="african">African Awards</button>
                <button class="category-tab" data-category="international">International</button>
                <button class="category-tab" data-category="regional">Regional</button>
            </div>
            
            <div class="awards-grid" id="awards-grid">
                <!-- Awards will be loaded here -->
            </div>
        </div>
    </section>

    <!-- Recent Winners -->
    <section class="recent-winners">
        <div class="container">
            <h2 class="section-title">Recent Winners</h2>
            <div class="winners-grid" id="recent-winners">
                <!-- Recent winners will be loaded here -->
            </div>
        </div>
    </section>

    <!-- Award Statistics -->
    <section class="award-stats">
        <div class="container">
            <h2 class="section-title">Award Statistics</h2>
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-trophy"></i>
                    </div>
                    <div class="stat-content">
                        <h3 id="total-awards">0</h3>
                        <p>Total Awards</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="stat-content">
                        <h3 id="total-winners">0</h3>
                        <p>Unique Winners</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-globe-africa"></i>
                    </div>
                    <div class="stat-content">
                        <h3 id="african-winners">0</h3>
                        <p>African Winners</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-calendar"></i>
                    </div>
                    <div class="stat-content">
                        <h3 id="awards-this-year">0</h3>
                        <p>Awards This Year</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- African Awards Spotlight -->
    <section class="african-spotlight">
        <div class="container">
            <h2 class="section-title">
                <i class="fas fa-globe-africa"></i>
                African Awards Spotlight
            </h2>
            <div class="spotlight-tabs">
                <button class="spotlight-tab active" data-country="all">All Africa</button>
                <button class="spotlight-tab" data-country="zimbabwe">Zimbabwe 🇿🇼</button>
                <button class="spotlight-tab" data-country="nigeria">Nigeria 🇳🇬</button>
                <button class="spotlight-tab" data-country="south-africa">South Africa 🇿🇦</button>
                <button class="spotlight-tab" data-country="kenya">Kenya 🇰🇪</button>
                <button class="spotlight-tab" data-country="ghana">Ghana 🇬🇭</button>
            </div>
            <div class="spotlight-grid" id="african-awards-grid">
                <!-- African awards will be loaded here -->
            </div>
        </div>
    </section>

    <!-- Upcoming Awards -->
    <section class="upcoming-awards">
        <div class="container">
            <h2 class="section-title">Upcoming Awards</h2>
            <div class="upcoming-grid" id="upcoming-awards">
                <!-- Upcoming awards will be loaded here -->
            </div>
        </div>
    </section>

    <!-- Award History -->
    <section class="award-history">
        <div class="container">
            <h2 class="section-title">Award History</h2>
            <div class="history-timeline" id="award-history">
                <!-- Award history timeline will be loaded here -->
            </div>
        </div>
    </section>

<?php
// Page-specific scripts
$page_specific_scripts = '
<script>
// Initialize awards page functionality
document.addEventListener("DOMContentLoaded", async function() {
    // Load awards data
    await loadAwardsData();
    
    // Initialize page functionality
    initAwardsPage();
    initCategoryTabs();
    initSpotlightTabs();
});

// Load awards data
async function loadAwardsData() {
    try {
        // Load featured awards
        await loadFeaturedAwards();
        
        // Load all awards
        await loadAllAwards();
        
        // Load recent winners
        await loadRecentWinners();
        
        // Load African awards
        await loadAfricanAwards();
        
        // Load upcoming awards
        await loadUpcomingAwards();
        
        // Load award history
        await loadAwardHistory();
        
        // Update statistics
        updateAwardStatistics();
        
    } catch (error) {
        console.error("Error loading awards data:", error);
        showError("Failed to load awards data. Please try again.");
    }
}

// Load featured awards
async function loadFeaturedAwards() {
    const container = document.getElementById("featured-awards");
    if (!container) return;
    
    const featuredAwards = [
        {
            id: 1,
            name: "Grammy Awards",
            year: 2024,
            category: "International",
            description: "The most prestigious music awards in the world",
            image: "https://images.unsplash.com/photo-1493225457124-a3eb161ffa5f?w=400&h=300&fit=crop",
            isAfrican: false
        },
        {
            id: 2,
            name: "African Music Awards",
            year: 2024,
            category: "African",
            description: "Celebrating excellence in African music",
            image: "https://images.unsplash.com/photo-1516450360452-9312f5e86fc7?w=400&h=300&fit=crop",
            isAfrican: true
        },
        {
            id: 3,
            name: "Zimbabwe Music Awards",
            year: 2024,
            category: "Regional",
            description: "Honoring Zimbabwean music talent",
            image: "https://images.unsplash.com/photo-1493225457124-a3eb161ffa5f?w=400&h=300&fit=crop",
            isAfrican: true,
            isZimbabwean: true
        }
    ];
    
    displayFeaturedAwards(featuredAwards, container);
}

// Display featured awards
function displayFeaturedAwards(awards, container) {
    container.innerHTML = "";
    
    awards.forEach((award, index) => {
        const awardItem = document.createElement("div");
        awardItem.className = `featured-item rank-${index + 1}`;
        awardItem.innerHTML = `
            <div class="featured-image">
                <img src="${award.image}" alt="${award.name}">
                <div class="award-overlay">
                    <button class="view-btn" onclick="viewAwardDetails(${award.id})">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
            </div>
            <div class="featured-info">
                <h3 class="featured-name">${award.name}</h3>
                <p class="featured-year">${award.year}</p>
                <p class="featured-description">${award.description}</p>
                <div class="featured-badges">
                    <span class="category-badge">${award.category}</span>
                    ${award.isAfrican ? \'<span class="african-badge">🌍</span>\' : \'\'}
                    ${award.isZimbabwean ? \'<span class="zimbabwe-badge">🇿🇼</span>\' : \'\'}
                </div>
            </div>
        `;
        container.appendChild(awardItem);
    });
}

// Load all awards
async function loadAllAwards() {
    const container = document.getElementById("awards-grid");
    if (!container) return;
    
    const awards = [
        {
            id: 1,
            name: "Grammy Awards",
            year: 2024,
            category: "International",
            description: "The most prestigious music awards",
            image: "https://images.unsplash.com/photo-1493225457124-a3eb161ffa5f?w=300&h=200&fit=crop",
            isAfrican: false
        },
        {
            id: 2,
            name: "African Music Awards",
            year: 2024,
            category: "African",
            description: "Celebrating African music excellence",
            image: "https://images.unsplash.com/photo-1516450360452-9312f5e86fc7?w=300&h=200&fit=crop",
            isAfrican: true
        },
        {
            id: 3,
            name: "Zimbabwe Music Awards",
            year: 2024,
            category: "Regional",
            description: "Zimbabwean music recognition",
            image: "https://images.unsplash.com/photo-1493225457124-a3eb161ffa5f?w=300&h=200&fit=crop",
            isAfrican: true,
            isZimbabwean: true
        },
        {
            id: 4,
            name: "MTV Africa Music Awards",
            year: 2024,
            category: "African",
            description: "MTV\'s celebration of African music",
            image: "https://images.unsplash.com/photo-1516450360452-9312f5e86fc7?w=300&h=200&fit=crop",
            isAfrican: true
        },
        {
            id: 5,
            name: "Billboard Music Awards",
            year: 2024,
            category: "International",
            description: "Billboard\'s music industry awards",
            image: "https://images.unsplash.com/photo-1493225457124-a3eb161ffa5f?w=300&h=200&fit=crop",
            isAfrican: false
        },
        {
            id: 6,
            name: "Nigerian Entertainment Awards",
            year: 2024,
            category: "Regional",
            description: "Nigerian entertainment excellence",
            image: "https://images.unsplash.com/photo-1516450360452-9312f5e86fc7?w=300&h=200&fit=crop",
            isAfrican: true
        }
    ];
    
    displayAllAwards(awards, container);
}

// Display all awards
function displayAllAwards(awards, container) {
    container.innerHTML = "";
    
    awards.forEach(award => {
        const awardItem = document.createElement("div");
        awardItem.className = "award-item";
        awardItem.innerHTML = `
            <div class="award-image">
                <img src="${award.image}" alt="${award.name}">
                <div class="award-overlay">
                    <button class="view-btn" onclick="viewAwardDetails(${award.id})">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
            </div>
            <div class="award-info">
                <h4 class="award-name">${award.name}</h4>
                <p class="award-year">${award.year}</p>
                <p class="award-description">${award.description}</p>
                <div class="award-badges">
                    <span class="category-badge">${award.category}</span>
                    ${award.isAfrican ? \'<span class="african-badge">🌍</span>\' : \'\'}
                    ${award.isZimbabwean ? \'<span class="zimbabwe-badge">🇿🇼</span>\' : \'\'}
                </div>
            </div>
        `;
        container.appendChild(awardItem);
    });
}

// Load recent winners
async function loadRecentWinners() {
    const container = document.getElementById("recent-winners");
    if (!container) return;
    
    const winners = [
        {
            id: 1,
            name: "Burna Boy",
            award: "Best African Artist",
            event: "Grammy Awards 2024",
            image: "https://images.unsplash.com/photo-1493225457124-a3eb161ffa5f?w=200&h=200&fit=crop",
            isAfrican: true
        },
        {
            id: 2,
            name: "Wizkid",
            award: "Best Afrobeats Song",
            event: "MTV Awards 2024",
            image: "https://images.unsplash.com/photo-1516450360452-9312f5e86fc7?w=200&h=200&fit=crop",
            isAfrican: true
        },
        {
            id: 3,
            name: "Jah Prayzah",
            award: "Best Zimbabwean Artist",
            event: "Zimbabwe Music Awards 2024",
            image: "https://images.unsplash.com/photo-1493225457124-a3eb161ffa5f?w=200&h=200&fit=crop",
            isAfrican: true,
            isZimbabwean: true
        }
    ];
    
    displayRecentWinners(winners, container);
}

// Display recent winners
function displayRecentWinners(winners, container) {
    container.innerHTML = "";
    
    winners.forEach(winner => {
        const winnerItem = document.createElement("div");
        winnerItem.className = "winner-item";
        winnerItem.innerHTML = `
            <div class="winner-image">
                <img src="${winner.image}" alt="${winner.name}">
                <div class="winner-badges">
                    ${winner.isAfrican ? \'<span class="african-badge">🌍</span>\' : \'\'}
                    ${winner.isZimbabwean ? \'<span class="zimbabwe-badge">🇿🇼</span>\' : \'\'}
                </div>
            </div>
            <div class="winner-info">
                <h4 class="winner-name">${winner.name}</h4>
                <p class="winner-award">${winner.award}</p>
                <p class="winner-event">${winner.event}</p>
            </div>
        `;
        container.appendChild(winnerItem);
    });
}

// Load African awards
async function loadAfricanAwards(country = "all") {
    const container = document.getElementById("african-awards-grid");
    if (!container) return;
    
    const africanAwards = [
        {
            id: 1,
            name: "African Music Awards",
            year: 2024,
            country: "All Africa",
            description: "Celebrating African music excellence",
            image: "https://images.unsplash.com/photo-1516450360452-9312f5e86fc7?w=300&h=200&fit=crop"
        },
        {
            id: 2,
            name: "Zimbabwe Music Awards",
            year: 2024,
            country: "Zimbabwe",
            description: "Zimbabwean music recognition",
            image: "https://images.unsplash.com/photo-1493225457124-a3eb161ffa5f?w=300&h=200&fit=crop"
        },
        {
            id: 3,
            name: "Nigerian Entertainment Awards",
            year: 2024,
            country: "Nigeria",
            description: "Nigerian entertainment excellence",
            image: "https://images.unsplash.com/photo-1516450360452-9312f5e86fc7?w=300&h=200&fit=crop"
        }
    ];
    
    const filteredAwards = country === "all" ? africanAwards : africanAwards.filter(award => 
        award.country.toLowerCase().includes(country.toLowerCase())
    );
    
    displayAfricanAwards(filteredAwards, container);
}

// Display African awards
function displayAfricanAwards(awards, container) {
    container.innerHTML = "";
    
    awards.forEach(award => {
        const awardItem = document.createElement("div");
        awardItem.className = "spotlight-item";
        awardItem.innerHTML = `
            <div class="spotlight-image">
                <img src="${award.image}" alt="${award.name}">
                <div class="spotlight-overlay">
                    <button class="view-btn" onclick="viewAwardDetails(${award.id})">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
            </div>
            <div class="spotlight-info">
                <h4>${award.name}</h4>
                <p>${award.country} - ${award.year}</p>
                <p class="spotlight-description">${award.description}</p>
            </div>
        `;
        container.appendChild(awardItem);
    });
}

// Load upcoming awards
async function loadUpcomingAwards() {
    const container = document.getElementById("upcoming-awards");
    if (!container) return;
    
    const upcomingAwards = [
        {
            id: 1,
            name: "African Music Awards 2025",
            date: "March 15, 2025",
            location: "Lagos, Nigeria",
            description: "The biggest African music awards ceremony"
        },
        {
            id: 2,
            name: "Zimbabwe Music Awards 2025",
            date: "April 20, 2025",
            location: "Harare, Zimbabwe",
            description: "Celebrating Zimbabwean music talent"
        }
    ];
    
    displayUpcomingAwards(upcomingAwards, container);
}

// Display upcoming awards
function displayUpcomingAwards(awards, container) {
    container.innerHTML = "";
    
    awards.forEach(award => {
        const awardItem = document.createElement("div");
        awardItem.className = "upcoming-item";
        awardItem.innerHTML = `
            <div class="upcoming-info">
                <h4 class="upcoming-name">${award.name}</h4>
                <p class="upcoming-date">${award.date}</p>
                <p class="upcoming-location">${award.location}</p>
                <p class="upcoming-description">${award.description}</p>
            </div>
            <div class="upcoming-actions">
                <button class="remind-btn" onclick="setReminder(${award.id})">
                    <i class="fas fa-bell"></i>
                    Remind Me
                </button>
            </div>
        `;
        container.appendChild(awardItem);
    });
}

// Load award history
async function loadAwardHistory() {
    const container = document.getElementById("award-history");
    if (!container) return;
    
    const history = [
        {
            year: 2024,
            event: "Grammy Awards",
            winner: "Burna Boy",
            award: "Best African Artist"
        },
        {
            year: 2023,
            event: "African Music Awards",
            winner: "Wizkid",
            award: "Artist of the Year"
        },
        {
            year: 2023,
            event: "Zimbabwe Music Awards",
            winner: "Jah Prayzah",
            award: "Best Male Artist"
        }
    ];
    
    displayAwardHistory(history, container);
}

// Display award history
function displayAwardHistory(history, container) {
    container.innerHTML = "";
    
    history.forEach((item, index) => {
        const historyItem = document.createElement("div");
        historyItem.className = "history-item";
        historyItem.innerHTML = `
            <div class="history-year">${item.year}</div>
            <div class="history-content">
                <h4>${item.event}</h4>
                <p><strong>${item.winner}</strong> - ${item.award}</p>
            </div>
        `;
        container.appendChild(historyItem);
    });
}

// Update award statistics
function updateAwardStatistics() {
    const totalAwards = document.getElementById("total-awards");
    const totalWinners = document.getElementById("total-winners");
    const africanWinners = document.getElementById("african-winners");
    const awardsThisYear = document.getElementById("awards-this-year");
    
    if (totalAwards) {
        animateCounter(totalAwards, 25);
    }
    
    if (totalWinners) {
        animateCounter(totalWinners, 18);
    }
    
    if (africanWinners) {
        animateCounter(africanWinners, 12);
    }
    
    if (awardsThisYear) {
        animateCounter(awardsThisYear, 8);
    }
}

// Initialize awards page
function initAwardsPage() {
    // Initialize any page-specific functionality
    console.log("Awards page initialized");
}

// Initialize category tabs
function initCategoryTabs() {
    const tabs = document.querySelectorAll(".category-tab");
    tabs.forEach(tab => {
        tab.addEventListener("click", () => {
            tabs.forEach(t => t.classList.remove("active"));
            tab.classList.add("active");
            
            const category = tab.getAttribute("data-category");
            filterAwardsByCategory(category);
        });
    });
}

// Initialize spotlight tabs
function initSpotlightTabs() {
    const tabs = document.querySelectorAll(".spotlight-tab");
    tabs.forEach(tab => {
        tab.addEventListener("click", () => {
            tabs.forEach(t => t.classList.remove("active"));
            tab.classList.add("active");
            
            const country = tab.getAttribute("data-country");
            loadAfricanAwards(country);
        });
    });
}

// Filter awards by category
function filterAwardsByCategory(category) {
    console.log("Filtering awards by category:", category);
    // Implementation for filtering awards by category
}

// View award details
function viewAwardDetails(awardId) {
    console.log("Viewing award details for:", awardId);
    // Implementation for viewing award details
}

// Set reminder for upcoming award
function setReminder(awardId) {
    console.log("Setting reminder for award:", awardId);
    // Implementation for setting reminders
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
</script>
';

// Include footer
include 'includes/footer.php';
?>
