// Business page specific functionality
document.addEventListener('DOMContentLoaded', function() {
    initBusinessPage();
});

function initBusinessPage() {
    loadBusinessData();
    initFilters();
    initBusinessModal();
    initBusinessInteractions();
}

// Load and display business data
async function loadBusinessData() {
    try {
        // Simulate API call
        const businessData = await fetchChartData('business');
        displayBusinessCharts(businessData);
        displayMarketLeaders(businessData);
    } catch (error) {
        console.error('Error loading business data:', error);
    }
}

function displayBusinessCharts(data) {
    const chartsGrid = document.getElementById('charts-grid');
    if (!chartsGrid) return;
    
    chartsGrid.innerHTML = '';
    
    const chartTypes = [
        {
            title: 'Revenue Growth',
            type: 'Line Chart',
            value: '$15.4B',
            change: '+12.3%',
            changeType: 'positive'
        },
        {
            title: 'Market Share',
            type: 'Pie Chart',
            value: '68.2%',
            change: '+2.1%',
            changeType: 'positive'
        },
        {
            title: 'User Engagement',
            type: 'Bar Chart',
            value: '2.4B',
            change: '+8.7%',
            changeType: 'positive'
        },
        {
            title: 'Subscription Growth',
            type: 'Area Chart',
            value: '487M',
            change: '+15.2%',
            changeType: 'positive'
        }
    ];
    
    chartTypes.forEach((chart, index) => {
        const chartCard = createChartCard(chart, index);
        chartsGrid.appendChild(chartCard);
    });
    
    // Add stagger animation
    const chartCards = chartsGrid.querySelectorAll('.chart-card');
    chartCards.forEach((card, index) => {
        setTimeout(() => {
            card.classList.add('animate-slide-up');
        }, index * 100);
    });
}

function createChartCard(chart, index) {
    const chartCard = document.createElement('div');
    chartCard.className = 'chart-card';
    
    const chartIcons = ['fas fa-chart-line', 'fas fa-chart-pie', 'fas fa-chart-bar', 'fas fa-chart-area'];
    
    chartCard.innerHTML = `
        <div class="chart-header">
            <h3 class="chart-title">${chart.title}</h3>
            <span class="chart-type">${chart.type}</span>
        </div>
        <div class="chart-visual">
            <div class="chart-placeholder">
                <i class="${chartIcons[index % chartIcons.length]}"></i>
                <p>Chart Visualization</p>
            </div>
        </div>
        <div class="chart-stats">
            <div class="chart-value">${chart.value}</div>
            <div class="chart-change ${chart.changeType}">${chart.change}</div>
        </div>
    `;
    
    return chartCard;
}

function displayMarketLeaders(data) {
    const leadersList = document.getElementById('leaders-list');
    if (!leadersList) return;
    
    leadersList.innerHTML = '';
    
    const leaders = [
        {
            name: 'Spotify',
            category: 'Music Streaming',
            marketShare: '31.2%',
            revenue: '$11.7B',
            change: '+5.2%',
            changeType: 'positive'
        },
        {
            name: 'Apple Music',
            category: 'Music Streaming',
            marketShare: '15.8%',
            revenue: '$6.2B',
            change: '+2.1%',
            changeType: 'positive'
        },
        {
            name: 'Amazon Music',
            category: 'Music Streaming',
            marketShare: '13.4%',
            revenue: '$4.8B',
            change: '+8.7%',
            changeType: 'positive'
        },
        {
            name: 'YouTube Music',
            category: 'Music Streaming',
            marketShare: '8.9%',
            revenue: '$3.1B',
            change: '+12.3%',
            changeType: 'positive'
        },
        {
            name: 'Tencent Music',
            category: 'Music Streaming',
            marketShare: '7.2%',
            revenue: '$2.9B',
            change: '-1.2%',
            changeType: 'negative'
        }
    ];
    
    leaders.forEach((leader, index) => {
        const leaderItem = createLeaderItem(leader, index + 1);
        leadersList.appendChild(leaderItem);
    });
    
    // Add stagger animation
    const leaderItems = leadersList.querySelectorAll('.leader-item');
    leaderItems.forEach((item, index) => {
        setTimeout(() => {
            item.classList.add('animate-slide-up');
        }, index * 100);
    });
}

function createLeaderItem(leader, rank) {
    const leaderItem = document.createElement('div');
    leaderItem.className = 'leader-item';
    
    const rankClass = rank <= 3 ? 'top-3' : '';
    
    leaderItem.innerHTML = `
        <div class="leader-rank ${rankClass}">${rank}</div>
        <div class="leader-info">
            <h4 class="leader-name">${leader.name}</h4>
            <p class="leader-category">${leader.category}</p>
            <div class="leader-metrics">
                <div class="leader-metric">
                    <span class="leader-metric-value">${leader.marketShare}</span>
                    <span class="leader-metric-label">Market Share</span>
                </div>
                <div class="leader-metric">
                    <span class="leader-metric-value">${leader.revenue}</span>
                    <span class="leader-metric-label">Revenue</span>
                </div>
            </div>
        </div>
        <div class="leader-change ${leader.changeType}">${leader.change}</div>
    `;
    
    return leaderItem;
}

// Filter functionality
function initFilters() {
    const filterTabs = document.querySelectorAll('.filter-tab');
    const timeframeFilter = document.querySelector('.timeframe-filter');
    const sortBtn = document.querySelector('.sort-btn');
    
    filterTabs.forEach(tab => {
        tab.addEventListener('click', () => {
            filterTabs.forEach(t => t.classList.remove('active'));
            tab.classList.add('active');
            
            const filter = tab.getAttribute('data-filter');
            applyBusinessFilter(filter);
        });
    });
    
    if (timeframeFilter) {
        timeframeFilter.addEventListener('change', (e) => {
            applyTimeframeFilter(e.target.value);
        });
    }
    
    if (sortBtn) {
        sortBtn.addEventListener('click', () => {
            toggleBusinessSort();
        });
    }
}

function applyBusinessFilter(filter) {
    const chartCards = document.querySelectorAll('.chart-card');
    const leaderItems = document.querySelectorAll('.leader-item');
    
    // Filter charts
    chartCards.forEach(card => {
        let shouldShow = true;
        
        switch (filter) {
            case 'revenue':
                shouldShow = card.querySelector('.chart-title').textContent.includes('Revenue');
                break;
            case 'streaming':
                shouldShow = card.querySelector('.chart-title').textContent.includes('User') || 
                            card.querySelector('.chart-title').textContent.includes('Subscription');
                break;
            case 'sales':
                shouldShow = card.querySelector('.chart-title').textContent.includes('Market');
                break;
            case 'all':
            default:
                shouldShow = true;
                break;
        }
        
        card.style.display = shouldShow ? 'block' : 'none';
    });
    
    // Filter leaders
    leaderItems.forEach(item => {
        let shouldShow = true;
        
        switch (filter) {
            case 'revenue':
                shouldShow = true; // All leaders have revenue data
                break;
            case 'streaming':
                shouldShow = item.querySelector('.leader-category').textContent.includes('Streaming');
                break;
            case 'sales':
                shouldShow = false; // No sales-specific leaders
                break;
            case 'all':
            default:
                shouldShow = true;
                break;
        }
        
        item.style.display = shouldShow ? 'flex' : 'none';
    });
}

function applyTimeframeFilter(timeframe) {
    // Update period button text
    const periodBtn = document.querySelector('.period-btn');
    const timeframes = {
        'quarterly': 'Q4 2025',
        'yearly': '2025',
        'monthly': 'October 2025'
    };
    
    if (periodBtn) {
        periodBtn.innerHTML = `<i class="fas fa-calendar"></i> ${timeframes[timeframe]}`;
    }
    
    // Reload data for selected timeframe
    loadBusinessData();
}

function toggleBusinessSort() {
    const leadersList = document.getElementById('leaders-list');
    const items = Array.from(leadersList.querySelectorAll('.leader-item'));
    
    // Toggle between ascending and descending market share order
    const isAscending = leadersList.getAttribute('data-sort') === 'asc';
    const newSort = isAscending ? 'desc' : 'asc';
    
    items.sort((a, b) => {
        const marketShareA = parseFloat(a.querySelector('.leader-metric-value').textContent);
        const marketShareB = parseFloat(b.querySelector('.leader-metric-value').textContent);
        return newSort === 'asc' ? marketShareA - marketShareB : marketShareB - marketShareA;
    });
    
    // Re-append sorted items
    items.forEach(item => leadersList.appendChild(item));
    leadersList.setAttribute('data-sort', newSort);
}

// Business modal functionality
function initBusinessModal() {
    const modal = document.getElementById('business-info-modal');
    const closeBtn = modal?.querySelector('.close-modal');
    const infoBtn = document.querySelector('.action-btn[title="Business Info"]');
    
    if (infoBtn) {
        infoBtn.addEventListener('click', () => {
            showModal(modal);
        });
    }
    
    if (closeBtn) {
        closeBtn.addEventListener('click', () => {
            hideModal(modal);
        });
    }
    
    // Close modal on overlay click
    if (modal) {
        modal.addEventListener('click', (e) => {
            if (e.target === modal) {
                hideModal(modal);
            }
        });
    }
}

function showModal(modal) {
    if (modal) {
        modal.classList.add('active');
        document.body.style.overflow = 'hidden';
    }
}

function hideModal(modal) {
    if (modal) {
        modal.classList.remove('active');
        document.body.style.overflow = 'auto';
    }
}

// Business interactions
function initBusinessInteractions() {
    // Period selector functionality
    const periodBtn = document.querySelector('.period-btn');
    if (periodBtn) {
        periodBtn.addEventListener('click', () => {
            showPeriodPicker();
        });
    }
    
    // Share functionality
    const shareBtn = document.querySelector('.action-btn[title="Share Charts"]');
    if (shareBtn) {
        shareBtn.addEventListener('click', () => {
            shareBusinessCharts();
        });
    }
}

function showPeriodPicker() {
    // Create period picker modal
    const modal = document.createElement('div');
    modal.className = 'modal active';
    modal.innerHTML = `
        <div class="modal-content">
            <div class="modal-header">
                <h3>Select Period</h3>
                <button class="close-modal">&times;</button>
            </div>
            <div class="modal-body">
                <div class="period-picker">
                    <select id="business-period" class="period-select">
                        <option value="Q4 2025">Q4 2025</option>
                        <option value="Q3 2025">Q3 2025</option>
                        <option value="Q2 2025">Q2 2025</option>
                        <option value="Q1 2025">Q1 2025</option>
                    </select>
                    <button class="btn btn-primary" onclick="loadBusinessForPeriod()">Load Data</button>
                </div>
            </div>
        </div>
    `;
    
    document.body.appendChild(modal);
    
    // Close functionality
    modal.querySelector('.close-modal').addEventListener('click', () => {
        modal.remove();
    });
    
    modal.addEventListener('click', (e) => {
        if (e.target === modal) {
            modal.remove();
        }
    });
}

function loadBusinessForPeriod() {
    const periodSelect = document.getElementById('business-period');
    const selectedPeriod = periodSelect.value;
    
    if (selectedPeriod) {
        // Update period button text
        const periodBtn = document.querySelector('.period-btn');
        periodBtn.innerHTML = `<i class="fas fa-calendar"></i> ${selectedPeriod}`;
        
        // Reload business data for selected period
        loadBusinessData();
        
        // Close modal
        document.querySelector('.modal').remove();
    }
}

function shareBusinessCharts() {
    if (navigator.share) {
        navigator.share({
            title: 'Timeline Business Charts',
            text: 'Check out the latest music industry business data on Timeline!',
            url: window.location.href
        });
    } else {
        // Fallback: copy to clipboard
        navigator.clipboard.writeText(window.location.href).then(() => {
            showNotification('Business charts link copied to clipboard!');
        });
    }
}

// Enhanced mock data generation for business
function generateMockBusinessData() {
    return [
        {
            metric: 'Streaming Revenue',
            value: '$12.3B',
            change: '+15.2%',
            period: '2025',
            category: 'revenue'
        },
        {
            metric: 'Physical Sales',
            value: '$1.8B',
            change: '-8.5%',
            period: '2025',
            category: 'sales'
        },
        {
            metric: 'Digital Downloads',
            value: '$0.9B',
            change: '-22.1%',
            period: '2025',
            category: 'sales'
        },
        {
            metric: 'Sync Revenue',
            value: '$0.4B',
            change: '+5.7%',
            period: '2025',
            category: 'revenue'
        }
    ];
}
