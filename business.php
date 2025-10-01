<?php
session_start();

// Page configuration
$page_title = 'Business Charts - African Business & Market Analysis | Timeline.co.zw';
$page_description = 'Track African business performance, market trends, and industry analysis. Discover the latest business charts, revenue data, and market insights from across the continent.';
$page_keywords = 'business charts, african business, market analysis, revenue charts, market share, industry trends, business performance, african markets';
$canonical_url = 'https://timeline.co.zw/business.php';
$body_class = 'business-page';

// Additional CSS files
$additional_css = [
    'css/business.css',
    'css/animations.css'
];

// Additional JavaScript files
$additional_js = [
    'js/business.js'
];

// Page-specific meta tags
$page_specific_meta = '
    <meta property="og:image" content="https://timeline.co.zw/images/business-hero.jpg">
    <meta name="twitter:image" content="https://timeline.co.zw/images/business-hero.jpg">
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
                        <a href="awards.php" class="nav-link">Awards</a>
                        <a href="business.php" class="nav-link active">Business</a>
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
                    <a href="business.php" class="chart-nav-link active">BUSINESS CHARTS</a>
                    <a href="business.php" class="chart-nav-link">REVENUE CHARTS</a>
                    <a href="business.php" class="chart-nav-link">MARKET SHARE</a>
                    <a href="business.php" class="chart-nav-link">INDUSTRY TRENDS</a>
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
                    Business Charts
                    <span class="african-badge" style="display: none;">🇿🇼</span>
                </h1>
                <p class="page-description">
                    Track African business performance, market trends, and industry analysis. Discover insights into the continent's growing business landscape.
                </p>
            </div>
        </div>
    </section>

    <!-- Featured Business Metrics -->
    <section class="featured-metrics">
        <div class="container">
            <h2 class="section-title">Key Business Metrics</h2>
            <div class="metrics-grid" id="featured-metrics">
                <!-- Featured metrics will be loaded here -->
                <div class="loading-placeholder">
                    <div class="loading-spinner"></div>
                    <p>Loading business data...</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Business Charts Section -->
    <section class="business-charts">
        <div class="container">
            <div class="charts-header">
                <h2 class="section-title">Business Performance Charts</h2>
                <div class="charts-controls">
                    <div class="filter-group">
                        <label for="industry-filter">Industry:</label>
                        <select id="industry-filter" class="filter-select">
                            <option value="all">All Industries</option>
                            <option value="technology">Technology</option>
                            <option value="finance">Finance</option>
                            <option value="retail">Retail</option>
                            <option value="manufacturing">Manufacturing</option>
                            <option value="agriculture">Agriculture</option>
                            <option value="mining">Mining</option>
                            <option value="telecommunications">Telecommunications</option>
                            <option value="energy">Energy</option>
                        </select>
                    </div>
                    <div class="filter-group">
                        <label for="region-filter">Region:</label>
                        <select id="region-filter" class="filter-select">
                            <option value="all">All Regions</option>
                            <option value="zimbabwe">Zimbabwe</option>
                            <option value="south-africa">South Africa</option>
                            <option value="nigeria">Nigeria</option>
                            <option value="kenya">Kenya</option>
                            <option value="ghana">Ghana</option>
                            <option value="east-africa">East Africa</option>
                            <option value="west-africa">West Africa</option>
                            <option value="southern-africa">Southern Africa</option>
                        </select>
                    </div>
                    <div class="filter-group">
                        <label for="timeframe-filter">Timeframe:</label>
                        <select id="timeframe-filter" class="filter-select">
                            <option value="1y">1 Year</option>
                            <option value="2y">2 Years</option>
                            <option value="5y">5 Years</option>
                            <option value="10y">10 Years</option>
                        </select>
                    </div>
                    <button class="refresh-btn" onclick="refreshBusinessData()">
                        <i class="fas fa-sync-alt"></i>
                        Refresh
                    </button>
                </div>
            </div>

            <!-- Charts Grid -->
            <div class="charts-grid" id="business-charts-grid">
                <!-- Business charts will be loaded here -->
            </div>
        </div>
    </section>

    <!-- Market Analysis -->
    <section class="market-analysis">
        <div class="container">
            <h2 class="section-title">Market Analysis</h2>
            <div class="analysis-tabs">
                <button class="analysis-tab active" data-analysis="revenue">Revenue Analysis</button>
                <button class="analysis-tab" data-analysis="growth">Growth Trends</button>
                <button class="analysis-tab" data-analysis="market-share">Market Share</button>
                <button class="analysis-tab" data-analysis="competition">Competition</button>
            </div>
            
            <div class="analysis-content" id="analysis-content">
                <!-- Analysis content will be loaded here -->
            </div>
        </div>
    </section>

    <!-- Industry Leaders -->
    <section class="industry-leaders">
        <div class="container">
            <h2 class="section-title">Industry Leaders</h2>
            <div class="leaders-grid" id="industry-leaders">
                <!-- Industry leaders will be loaded here -->
            </div>
        </div>
    </section>

    <!-- Business Statistics -->
    <section class="business-stats">
        <div class="container">
            <h2 class="section-title">Business Statistics</h2>
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-building"></i>
                    </div>
                    <div class="stat-content">
                        <h3 id="total-companies">0</h3>
                        <p>Total Companies</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-dollar-sign"></i>
                    </div>
                    <div class="stat-content">
                        <h3 id="total-revenue">$0B</h3>
                        <p>Combined Revenue</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <div class="stat-content">
                        <h3 id="avg-growth">0%</h3>
                        <p>Average Growth</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-globe-africa"></i>
                    </div>
                    <div class="stat-content">
                        <h3 id="african-companies">0</h3>
                        <p>African Companies</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- African Business Spotlight -->
    <section class="african-spotlight">
        <div class="container">
            <h2 class="section-title">
                <i class="fas fa-globe-africa"></i>
                African Business Spotlight
            </h2>
            <div class="spotlight-tabs">
                <button class="spotlight-tab active" data-country="all">All Africa</button>
                <button class="spotlight-tab" data-country="zimbabwe">Zimbabwe 🇿🇼</button>
                <button class="spotlight-tab" data-country="nigeria">Nigeria 🇳🇬</button>
                <button class="spotlight-tab" data-country="south-africa">South Africa 🇿🇦</button>
                <button class="spotlight-tab" data-country="kenya">Kenya 🇰🇪</button>
                <button class="spotlight-tab" data-country="ghana">Ghana 🇬🇭</button>
            </div>
            <div class="spotlight-grid" id="african-business-grid">
                <!-- African business content will be loaded here -->
            </div>
        </div>
    </section>

    <!-- Market Trends -->
    <section class="market-trends">
        <div class="container">
            <h2 class="section-title">Market Trends</h2>
            <div class="trends-grid" id="market-trends">
                <!-- Market trends will be loaded here -->
            </div>
        </div>
    </section>

    <!-- Investment Opportunities -->
    <section class="investment-opportunities">
        <div class="container">
            <h2 class="section-title">Investment Opportunities</h2>
            <div class="opportunities-grid" id="investment-opportunities">
                <!-- Investment opportunities will be loaded here -->
            </div>
        </div>
    </section>

<?php
// Page-specific scripts
$page_specific_scripts = '
<script>
// Initialize business page functionality
document.addEventListener("DOMContentLoaded", async function() {
    // Load business data
    await loadBusinessData();
    
    // Initialize page functionality
    initBusinessPage();
    initAnalysisTabs();
    initSpotlightTabs();
    initFilters();
});

// Load business data
async function loadBusinessData() {
    try {
        // Load featured metrics
        await loadFeaturedMetrics();
        
        // Load business charts
        await loadBusinessCharts();
        
        // Load market analysis
        await loadMarketAnalysis();
        
        // Load industry leaders
        await loadIndustryLeaders();
        
        // Load African business spotlight
        await loadAfricanBusiness();
        
        // Load market trends
        await loadMarketTrends();
        
        // Load investment opportunities
        await loadInvestmentOpportunities();
        
        // Update statistics
        updateBusinessStatistics();
        
    } catch (error) {
        console.error("Error loading business data:", error);
        showError("Failed to load business data. Please try again.");
    }
}

// Load featured metrics
async function loadFeaturedMetrics() {
    const container = document.getElementById("featured-metrics");
    if (!container) return;
    
    const metrics = [
        {
            id: 1,
            title: "African GDP Growth",
            value: "3.8%",
            change: "+0.2%",
            trend: "up",
            description: "Year-over-year growth"
        },
        {
            id: 2,
            title: "Tech Investment",
            value: "$4.2B",
            change: "+15%",
            trend: "up",
            description: "Total tech investment in Africa"
        },
        {
            id: 3,
            title: "E-commerce Growth",
            value: "25%",
            change: "+5%",
            trend: "up",
            description: "Online retail growth rate"
        },
        {
            id: 4,
            title: "Startup Funding",
            value: "$1.8B",
            change: "+22%",
            trend: "up",
            description: "African startup funding"
        }
    ];
    
    displayFeaturedMetrics(metrics, container);
}

// Display featured metrics
function displayFeaturedMetrics(metrics, container) {
    container.innerHTML = "";
    
    metrics.forEach(metric => {
        const metricItem = document.createElement("div");
        metricItem.className = "metric-item";
        metricItem.innerHTML = `
            <div class="metric-header">
                <h3 class="metric-title">${metric.title}</h3>
                <div class="metric-trend ${metric.trend}">
                    <i class="fas fa-arrow-${metric.trend === "up" ? "up" : "down"}"></i>
                    <span>${metric.change}</span>
                </div>
            </div>
            <div class="metric-value">${metric.value}</div>
            <div class="metric-description">${metric.description}</div>
        `;
        container.appendChild(metricItem);
    });
}

// Load business charts
async function loadBusinessCharts() {
    const container = document.getElementById("business-charts-grid");
    if (!container) return;
    
    const charts = [
        {
            id: 1,
            title: "Revenue Growth",
            type: "line",
            data: "Revenue growth data for African companies",
            description: "Quarterly revenue growth trends"
        },
        {
            id: 2,
            title: "Market Share",
            type: "pie",
            data: "Market share distribution",
            description: "Market share by industry"
        },
        {
            id: 3,
            title: "Profit Margins",
            type: "bar",
            data: "Profit margin analysis",
            description: "Profit margins by sector"
        },
        {
            id: 4,
            title: "Investment Flow",
            type: "area",
            data: "Investment flow trends",
            description: "Foreign and local investment"
        }
    ];
    
    displayBusinessCharts(charts, container);
}

// Display business charts
function displayBusinessCharts(charts, container) {
    container.innerHTML = "";
    
    charts.forEach(chart => {
        const chartItem = document.createElement("div");
        chartItem.className = "chart-item";
        chartItem.innerHTML = `
            <div class="chart-header">
                <h4 class="chart-title">${chart.title}</h4>
                <div class="chart-type">${chart.type.toUpperCase()}</div>
            </div>
            <div class="chart-content">
                <div class="chart-placeholder">
                    <i class="fas fa-chart-${chart.type === "line" ? "line" : chart.type === "pie" ? "pie" : "bar"}"></i>
                    <p>${chart.description}</p>
                </div>
            </div>
            <div class="chart-actions">
                <button class="action-btn" onclick="viewChartDetails(${chart.id})">
                    <i class="fas fa-eye"></i>
                    View Details
                </button>
            </div>
        `;
        container.appendChild(chartItem);
    });
}

// Load market analysis
async function loadMarketAnalysis(type = "revenue") {
    const container = document.getElementById("analysis-content");
    if (!container) return;
    
    const analysisData = {
        revenue: {
            title: "Revenue Analysis",
            content: "African companies have shown consistent revenue growth across multiple sectors, with technology and telecommunications leading the way.",
            insights: [
                "Technology sector revenue up 15% year-over-year",
                "Telecommunications showing steady 8% growth",
                "Agriculture sector recovering with 12% growth",
                "Manufacturing sector stable at 5% growth"
            ]
        },
        growth: {
            title: "Growth Trends",
            content: "The African business landscape is experiencing unprecedented growth, driven by digital transformation and increased investment.",
            insights: [
                "Digital transformation accelerating across industries",
                "Fintech sector growing at 25% annually",
                "E-commerce adoption increasing rapidly",
                "Green energy investments surging"
            ]
        },
        "market-share": {
            title: "Market Share Analysis",
            content: "Market share distribution shows increasing competition and diversification across African markets.",
            insights: [
                "Tech giants expanding African presence",
                "Local companies gaining market share",
                "New entrants disrupting traditional sectors",
                "Cross-border business increasing"
            ]
        },
        competition: {
            title: "Competition Analysis",
            content: "Competitive landscape is evolving with both local and international players vying for market dominance.",
            insights: [
                "Increased competition in fintech space",
                "Traditional banks adapting to digital",
                "Startups challenging established players",
                "Partnerships and acquisitions increasing"
            ]
        }
    };
    
    const data = analysisData[type] || analysisData.revenue;
    displayMarketAnalysis(data, container);
}

// Display market analysis
function displayMarketAnalysis(data, container) {
    container.innerHTML = `
        <div class="analysis-header">
            <h3>${data.title}</h3>
        </div>
        <div class="analysis-body">
            <p class="analysis-summary">${data.content}</p>
            <div class="analysis-insights">
                <h4>Key Insights:</h4>
                <ul>
                    ${data.insights.map(insight => `<li>${insight}</li>`).join("")}
                </ul>
            </div>
        </div>
    `;
}

// Load industry leaders
async function loadIndustryLeaders() {
    const container = document.getElementById("industry-leaders");
    if (!container) return;
    
    const leaders = [
        {
            id: 1,
            name: "MTN Group",
            industry: "Telecommunications",
            revenue: "$15.2B",
            country: "South Africa",
            growth: "+8%",
            isAfrican: true
        },
        {
            id: 2,
            name: "Dangote Group",
            industry: "Manufacturing",
            revenue: "$4.1B",
            country: "Nigeria",
            growth: "+12%",
            isAfrican: true
        },
        {
            id: 3,
            name: "Safaricom",
            industry: "Telecommunications",
            revenue: "$2.8B",
            country: "Kenya",
            growth: "+15%",
            isAfrican: true
        },
        {
            id: 4,
            name: "Econet Wireless",
            industry: "Telecommunications",
            revenue: "$1.2B",
            country: "Zimbabwe",
            growth: "+5%",
            isAfrican: true,
            isZimbabwean: true
        }
    ];
    
    displayIndustryLeaders(leaders, container);
}

// Display industry leaders
function displayIndustryLeaders(leaders, container) {
    container.innerHTML = "";
    
    leaders.forEach(leader => {
        const leaderItem = document.createElement("div");
        leaderItem.className = "leader-item";
        leaderItem.innerHTML = `
            <div class="leader-info">
                <h4 class="leader-name">${leader.name}</h4>
                <p class="leader-industry">${leader.industry}</p>
                <p class="leader-country">${leader.country}</p>
            </div>
            <div class="leader-stats">
                <div class="stat">
                    <span class="stat-label">Revenue</span>
                    <span class="stat-value">${leader.revenue}</span>
                </div>
                <div class="stat">
                    <span class="stat-label">Growth</span>
                    <span class="stat-value growth">${leader.growth}</span>
                </div>
            </div>
            <div class="leader-badges">
                ${leader.isAfrican ? \'<span class="african-badge">🌍</span>\' : \'\'}
                ${leader.isZimbabwean ? \'<span class="zimbabwe-badge">🇿🇼</span>\' : \'\'}
            </div>
        `;
        container.appendChild(leaderItem);
    });
}

// Load African business spotlight
async function loadAfricanBusiness(country = "all") {
    const container = document.getElementById("african-business-grid");
    if (!container) return;
    
    const businesses = [
        {
            id: 1,
            name: "Econet Wireless Zimbabwe",
            industry: "Telecommunications",
            country: "Zimbabwe",
            description: "Leading telecommunications provider in Zimbabwe",
            isZimbabwean: true
        },
        {
            id: 2,
            name: "MTN Nigeria",
            industry: "Telecommunications",
            country: "Nigeria",
            description: "Major telecom operator in Nigeria",
            isAfrican: true
        },
        {
            id: 3,
            name: "Safaricom Kenya",
            industry: "Telecommunications",
            country: "Kenya",
            description: "Mobile network operator in Kenya",
            isAfrican: true
        }
    ];
    
    const filteredBusinesses = country === "all" ? businesses : businesses.filter(business => 
        business.country.toLowerCase().includes(country.toLowerCase())
    );
    
    displayAfricanBusiness(filteredBusinesses, container);
}

// Display African business
function displayAfricanBusiness(businesses, container) {
    container.innerHTML = "";
    
    businesses.forEach(business => {
        const businessItem = document.createElement("div");
        businessItem.className = "spotlight-item";
        businessItem.innerHTML = `
            <div class="spotlight-info">
                <h4>${business.name}</h4>
                <p>${business.industry} - ${business.country}</p>
                <p class="spotlight-description">${business.description}</p>
            </div>
            <div class="spotlight-badges">
                ${business.isAfrican ? \'<span class="african-badge">🌍</span>\' : \'\'}
                ${business.isZimbabwean ? \'<span class="zimbabwe-badge">🇿🇼</span>\' : \'\'}
            </div>
        `;
        container.appendChild(businessItem);
    });
}

// Load market trends
async function loadMarketTrends() {
    const container = document.getElementById("market-trends");
    if (!container) return;
    
    const trends = [
        {
            id: 1,
            title: "Digital Transformation",
            description: "Companies are rapidly adopting digital technologies",
            impact: "High",
            timeframe: "1-2 years"
        },
        {
            id: 2,
            title: "Green Energy Transition",
            description: "Shift towards renewable energy sources",
            impact: "Medium",
            timeframe: "3-5 years"
        },
        {
            id: 3,
            title: "E-commerce Growth",
            description: "Online retail expanding across Africa",
            impact: "High",
            timeframe: "1-3 years"
        }
    ];
    
    displayMarketTrends(trends, container);
}

// Display market trends
function displayMarketTrends(trends, container) {
    container.innerHTML = "";
    
    trends.forEach(trend => {
        const trendItem = document.createElement("div");
        trendItem.className = "trend-item";
        trendItem.innerHTML = `
            <div class="trend-header">
                <h4 class="trend-title">${trend.title}</h4>
                <span class="trend-impact ${trend.impact.toLowerCase()}">${trend.impact}</span>
            </div>
            <div class="trend-body">
                <p class="trend-description">${trend.description}</p>
                <div class="trend-timeframe">
                    <i class="fas fa-clock"></i>
                    <span>${trend.timeframe}</span>
                </div>
            </div>
        `;
        container.appendChild(trendItem);
    });
}

// Load investment opportunities
async function loadInvestmentOpportunities() {
    const container = document.getElementById("investment-opportunities");
    if (!container) return;
    
    const opportunities = [
        {
            id: 1,
            title: "Fintech Startups",
            description: "Digital payment solutions and mobile banking",
            potential: "High",
            sector: "Technology"
        },
        {
            id: 2,
            title: "Renewable Energy",
            description: "Solar and wind energy projects",
            potential: "High",
            sector: "Energy"
        },
        {
            id: 3,
            title: "AgriTech",
            description: "Technology solutions for agriculture",
            potential: "Medium",
            sector: "Agriculture"
        }
    ];
    
    displayInvestmentOpportunities(opportunities, container);
}

// Display investment opportunities
function displayInvestmentOpportunities(opportunities, container) {
    container.innerHTML = "";
    
    opportunities.forEach(opportunity => {
        const opportunityItem = document.createElement("div");
        opportunityItem.className = "opportunity-item";
        opportunityItem.innerHTML = `
            <div class="opportunity-header">
                <h4 class="opportunity-title">${opportunity.title}</h4>
                <span class="opportunity-potential ${opportunity.potential.toLowerCase()}">${opportunity.potential}</span>
            </div>
            <div class="opportunity-body">
                <p class="opportunity-description">${opportunity.description}</p>
                <div class="opportunity-sector">
                    <i class="fas fa-industry"></i>
                    <span>${opportunity.sector}</span>
                </div>
            </div>
        `;
        container.appendChild(opportunityItem);
    });
}

// Update business statistics
function updateBusinessStatistics() {
    const totalCompanies = document.getElementById("total-companies");
    const totalRevenue = document.getElementById("total-revenue");
    const avgGrowth = document.getElementById("avg-growth");
    const africanCompanies = document.getElementById("african-companies");
    
    if (totalCompanies) {
        animateCounter(totalCompanies, 1250);
    }
    
    if (totalRevenue) {
        totalRevenue.textContent = "$45.2B";
    }
    
    if (avgGrowth) {
        avgGrowth.textContent = "8.5%";
    }
    
    if (africanCompanies) {
        animateCounter(africanCompanies, 890);
    }
}

// Initialize business page
function initBusinessPage() {
    console.log("Business page initialized");
}

// Initialize analysis tabs
function initAnalysisTabs() {
    const tabs = document.querySelectorAll(".analysis-tab");
    tabs.forEach(tab => {
        tab.addEventListener("click", () => {
            tabs.forEach(t => t.classList.remove("active"));
            tab.classList.add("active");
            
            const analysis = tab.getAttribute("data-analysis");
            loadMarketAnalysis(analysis);
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
            loadAfricanBusiness(country);
        });
    });
}

// Initialize filters
function initFilters() {
    const industryFilter = document.getElementById("industry-filter");
    const regionFilter = document.getElementById("region-filter");
    const timeframeFilter = document.getElementById("timeframe-filter");
    
    if (industryFilter) {
        industryFilter.addEventListener("change", () => {
            filterByIndustry(industryFilter.value);
        });
    }
    
    if (regionFilter) {
        regionFilter.addEventListener("change", () => {
            filterByRegion(regionFilter.value);
        });
    }
    
    if (timeframeFilter) {
        timeframeFilter.addEventListener("change", () => {
            filterByTimeframe(timeframeFilter.value);
        });
    }
}

// Filter by industry
function filterByIndustry(industry) {
    console.log("Filtering by industry:", industry);
    // Implementation for filtering by industry
}

// Filter by region
function filterByRegion(region) {
    console.log("Filtering by region:", region);
    // Implementation for filtering by region
}

// Filter by timeframe
function filterByTimeframe(timeframe) {
    console.log("Filtering by timeframe:", timeframe);
    // Implementation for filtering by timeframe
}

// View chart details
function viewChartDetails(chartId) {
    console.log("Viewing chart details for:", chartId);
    // Implementation for viewing chart details
}

// Refresh business data
function refreshBusinessData() {
    loadBusinessData();
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
