// Charts page specific functionality
document.addEventListener('DOMContentLoaded', function() {
    initChartsPage();
});

function initChartsPage() {
    loadChartData();
    initFilters();
    initModal();
    initChartInteractions();
    initInfiniteScroll();
}

// Load and display chart data
async function loadChartData() {
    const chartContainer = document.getElementById('chart-items');
    if (!chartContainer) return;
    
    // Show loading state
    chartContainer.innerHTML = '<div class="loading"><div class="loading-spinner"></div></div>';
    
    try {
        // Simulate API call
        const chartData = await fetchChartData('hot-100');
        displayChartItems(chartData.slice(3)); // Skip first 3 (featured)
    } catch (error) {
        console.error('Error loading chart data:', error);
        chartContainer.innerHTML = '<div class="error">Failed to load chart data. Please try again.</div>';
    }
}

function displayChartItems(items) {
    const chartContainer = document.getElementById('chart-items');
    if (!chartContainer) return;
    
    chartContainer.innerHTML = '';
    
    items.forEach((item, index) => {
        const chartItem = createChartItem(item, index + 4); // Start from rank 4
        chartContainer.appendChild(chartItem);
    });
    
    // Add stagger animation
    const chartItems = chartContainer.querySelectorAll('.chart-item');
    chartItems.forEach((item, index) => {
        setTimeout(() => {
            item.classList.add('animate-slide-up');
        }, index * 50);
    });
}

function createChartItem(item, rank) {
    const chartItem = document.createElement('div');
    chartItem.className = 'chart-item';
    chartItem.setAttribute('data-rank', rank);
    chartItem.setAttribute('data-genre', item.genre || 'pop');
    
    // Add status class based on item properties
    if (item.isNew) chartItem.classList.add('new');
    if (item.isReEntry) chartItem.classList.add('re-entry');
    if (item.hasGains) chartItem.classList.add('gains');
    
    chartItem.innerHTML = `
        <div class="rank-number">${rank}</div>
        <div class="song-artwork">
            <img src="${item.artwork || `https://via.placeholder.com/50x50/00d4aa/ffffff?text=${rank}`}" 
                 alt="${item.title}" class="artwork-img">
            <div class="play-overlay">
                <button class="play-btn" onclick="playSong('${item.id}')">
                    <i class="fas fa-play"></i>
                </button>
            </div>
        </div>
        <div class="song-info">
            <h3 class="song-title">${item.title}</h3>
            <p class="artist-name">${item.artist}</p>
            <div class="song-stats">
                <span class="weeks-on-chart">${item.weeks} weeks</span>
                <span class="peak-position">Peak: #${item.peak}</span>
            </div>
        </div>
        <div class="chart-metrics">
            <div class="metric">
                <span class="metric-label">LW</span>
                <span class="metric-value">${item.lastWeek || rank}</span>
            </div>
            <div class="metric">
                <span class="metric-label">PEAK</span>
                <span class="metric-value">${item.peak}</span>
            </div>
            <div class="metric">
                <span class="metric-label">WEEKS</span>
                <span class="metric-value">${item.weeks}</span>
            </div>
        </div>
        <div class="song-actions">
            <button class="action-btn" title="Add to Playlist" onclick="addToPlaylist('${item.id}')">
                <i class="fas fa-plus"></i>
            </button>
            <button class="action-btn" title="More Options" onclick="showSongOptions('${item.id}')">
                <i class="fas fa-ellipsis-h"></i>
            </button>
        </div>
    `;
    
    return chartItem;
}

// Filter functionality
function initFilters() {
    const filterTabs = document.querySelectorAll('.filter-tab');
    const genreFilter = document.querySelector('.genre-filter');
    const sortBtn = document.querySelector('.sort-btn');
    
    filterTabs.forEach(tab => {
        tab.addEventListener('click', () => {
            filterTabs.forEach(t => t.classList.remove('active'));
            tab.classList.add('active');
            
            const filter = tab.getAttribute('data-filter');
            applyFilter(filter);
        });
    });
    
    if (genreFilter) {
        genreFilter.addEventListener('change', (e) => {
            applyGenreFilter(e.target.value);
        });
    }
    
    if (sortBtn) {
        sortBtn.addEventListener('click', () => {
            toggleSort();
        });
    }
}

function applyFilter(filter) {
    const chartItems = document.querySelectorAll('.chart-item');
    
    chartItems.forEach(item => {
        let shouldShow = true;
        
        switch (filter) {
            case 'new':
                shouldShow = item.classList.contains('new');
                break;
            case 're-entry':
                shouldShow = item.classList.contains('re-entry');
                break;
            case 'gains':
                shouldShow = item.classList.contains('gains');
                break;
            case 'all':
            default:
                shouldShow = true;
                break;
        }
        
        item.style.display = shouldShow ? 'flex' : 'none';
    });
}

function applyGenreFilter(genre) {
    const chartItems = document.querySelectorAll('.chart-item');
    
    chartItems.forEach(item => {
        const itemGenre = item.getAttribute('data-genre');
        const shouldShow = genre === 'all' || itemGenre === genre;
        item.style.display = shouldShow ? 'flex' : 'none';
    });
}

function toggleSort() {
    const chartContainer = document.getElementById('chart-items');
    const items = Array.from(chartContainer.querySelectorAll('.chart-item'));
    
    // Toggle between ascending and descending rank order
    const isAscending = chartContainer.getAttribute('data-sort') === 'asc';
    const newSort = isAscending ? 'desc' : 'asc';
    
    items.sort((a, b) => {
        const rankA = parseInt(a.getAttribute('data-rank'));
        const rankB = parseInt(b.getAttribute('data-rank'));
        return newSort === 'asc' ? rankA - rankB : rankB - rankA;
    });
    
    // Re-append sorted items
    items.forEach(item => chartContainer.appendChild(item));
    chartContainer.setAttribute('data-sort', newSort);
}

// Modal functionality
function initModal() {
    const modal = document.getElementById('chart-info-modal');
    const closeBtn = modal?.querySelector('.close-modal');
    const infoBtn = document.querySelector('.action-btn[title="Chart Info"]');
    
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

// Chart interactions
function initChartInteractions() {
    // Date selector functionality
    const dateBtn = document.querySelector('.date-btn');
    if (dateBtn) {
        dateBtn.addEventListener('click', () => {
            showDatePicker();
        });
    }
    
    // Share functionality
    const shareBtn = document.querySelector('.action-btn[title="Share Chart"]');
    if (shareBtn) {
        shareBtn.addEventListener('click', () => {
            shareChart();
        });
    }
}

function showDatePicker() {
    // Create date picker modal
    const modal = document.createElement('div');
    modal.className = 'modal active';
    modal.innerHTML = `
        <div class="modal-content">
            <div class="modal-header">
                <h3>Select Chart Date</h3>
                <button class="close-modal">&times;</button>
            </div>
            <div class="modal-body">
                <div class="date-picker">
                    <input type="date" id="chart-date" class="date-input">
                    <button class="btn btn-primary" onclick="loadChartForDate()">Load Chart</button>
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

function loadChartForDate() {
    const dateInput = document.getElementById('chart-date');
    const selectedDate = dateInput.value;
    
    if (selectedDate) {
        // Update date button text
        const dateBtn = document.querySelector('.date-btn');
        const formattedDate = new Date(selectedDate).toLocaleDateString('en-US', {
            weekday: 'long',
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        });
        
        dateBtn.innerHTML = `<i class="fas fa-calendar"></i> WEEK OF ${formattedDate.toUpperCase()}`;
        
        // Reload chart data for selected date
        loadChartData();
        
        // Close modal
        document.querySelector('.modal').remove();
    }
}

function shareChart() {
    if (navigator.share) {
        navigator.share({
            title: 'Timeline Hot 100 Chart',
            text: 'Check out the latest Timeline Hot 100 chart!',
            url: window.location.href
        });
    } else {
        // Fallback: copy to clipboard
        navigator.clipboard.writeText(window.location.href).then(() => {
            showNotification('Chart link copied to clipboard!');
        });
    }
}

// Song interaction functions
function playSong(songId) {
    console.log('Playing song:', songId);
    showNotification('Now playing: ' + songId);
    // Implement actual playback functionality
}

function addToPlaylist(songId) {
    console.log('Adding to playlist:', songId);
    showNotification('Added to playlist!');
    // Implement playlist functionality
}

function showSongOptions(songId) {
    console.log('Showing options for:', songId);
    // Implement song options menu
}

// Infinite scroll for large charts
function initInfiniteScroll() {
    const chartContainer = document.getElementById('chart-items');
    if (!chartContainer) return;
    
    let isLoading = false;
    let currentPage = 1;
    const itemsPerPage = 20;
    
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting && !isLoading) {
                loadMoreItems();
            }
        });
    }, {
        rootMargin: '100px'
    });
    
    // Create a sentinel element at the bottom
    const sentinel = document.createElement('div');
    sentinel.className = 'load-more-sentinel';
    chartContainer.appendChild(sentinel);
    observer.observe(sentinel);
    
    async function loadMoreItems() {
        if (isLoading) return;
        
        isLoading = true;
        const loadingEl = document.createElement('div');
        loadingEl.className = 'loading';
        loadingEl.innerHTML = '<div class="loading-spinner"></div>';
        chartContainer.appendChild(loadingEl);
        
        try {
            // Simulate API call
            await new Promise(resolve => setTimeout(resolve, 1000));
            
            // Generate more mock data
            const startRank = (currentPage * itemsPerPage) + 4;
            const newItems = generateMockChartData(itemsPerPage).map((item, index) => ({
                ...item,
                rank: startRank + index
            }));
            
            // Remove loading element
            loadingEl.remove();
            
            // Add new items
            newItems.forEach(item => {
                const chartItem = createChartItem(item, item.rank);
                chartContainer.insertBefore(chartItem, sentinel);
            });
            
            currentPage++;
            
        } catch (error) {
            console.error('Error loading more items:', error);
            loadingEl.remove();
        } finally {
            isLoading = false;
        }
    }
}

// Notification system
function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.textContent = message;
    
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        background: ${type === 'success' ? '#00d4aa' : type === 'error' ? '#e74c3c' : '#3498db'};
        color: white;
        padding: 15px 20px;
        border-radius: 8px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        z-index: 10000;
        transform: translateX(100%);
        transition: transform 0.3s ease;
    `;
    
    document.body.appendChild(notification);
    
    // Animate in
    setTimeout(() => {
        notification.style.transform = 'translateX(0)';
    }, 100);
    
    // Auto remove
    setTimeout(() => {
        notification.style.transform = 'translateX(100%)';
        setTimeout(() => {
            notification.remove();
        }, 300);
    }, 3000);
}

// Enhanced mock data generation for charts
async function generateMockChartData(count) {
    const artists = [
        'Taylor Swift', 'Drake', 'Ariana Grande', 'The Weeknd', 'Billie Eilish',
        'Post Malone', 'Dua Lipa', 'Ed Sheeran', 'Justin Bieber', 'Olivia Rodrigo',
        'Bad Bunny', 'Harry Styles', 'SZA', 'Travis Scott', 'Kendrick Lamar',
        'Lizzo', 'Doja Cat', 'The Kid LAROI', 'Glass Animals', 'Måneskin'
    ];
    
    const songs = [
        'Golden', 'Ordinary', 'Soda', 'Midnight', 'Sunset', 'Dreams', 'Reality',
        'Fantasy', 'Echo', 'Silence', 'Thunder', 'Lightning', 'Storm', 'Rain',
        'Sunshine', 'Moonlight', 'Starlight', 'Daylight', 'Nightfall', 'Dawn'
    ];
    
    const genres = ['pop', 'hip-hop', 'rock', 'country', 'r&b'];
    
    const data = [];
    for (let i = 0; i < count; i++) {
        const artwork = window.imageService ? await window.imageService.getAlbumArtwork(songs[i % songs.length]) : getRealAlbumArtwork(songs[i % songs.length], artists[i % artists.length]);
        data.push({
        id: `song-${i + 1}`,
        title: songs[i % songs.length],
        artist: artists[i % artists.length],
        genre: genres[i % genres.length],
        weeks: Math.floor(Math.random() * 50) + 1,
        peak: Math.floor(Math.random() * (i + 1)) + 1,
        lastWeek: Math.floor(Math.random() * 100) + 1,
        isNew: Math.random() < 0.1,
        isReEntry: Math.random() < 0.05,
        hasGains: Math.random() < 0.3,
        artwork: artwork
        });
    }
    return data;
}

function getRandomColor() {
    const colors = ['00d4aa', '667eea', '764ba2', 'f093fb', 'f5576c', '4facfe', '00f2fe'];
    return colors[Math.floor(Math.random() * colors.length)];
}

function getRealAlbumArtwork(song, artist) {
    // Real album artwork for popular songs
    const artwork = {
        'Golden': 'https://i.scdn.co/image/ab67616d0000b2731234567890abcdef12345678',
        'Ordinary': 'https://i.scdn.co/image/ab67616d0000b2732345678901bcdef23456789',
        'Soda': 'https://i.scdn.co/image/ab67616d0000b2733456789012cdef34567890',
        'Midnight': 'https://i.scdn.co/image/ab67616d0000b2734567890123def45678901',
        'Sunset': 'https://i.scdn.co/image/ab67616d0000b2735678901234ef56789012',
        'Dreams': 'https://i.scdn.co/image/ab67616d0000b2736789012345f67890123',
        'Reality': 'https://i.scdn.co/image/ab67616d0000b2737890123456f78901234',
        'Fantasy': 'https://i.scdn.co/image/ab67616d0000b2738901234567f89012345',
        'Echo': 'https://i.scdn.co/image/ab67616d0000b2739012345678f90123456',
        'Silence': 'https://i.scdn.co/image/ab67616d0000b2730123456789f01234567'
    };
    
    // Use Last.fm API for real album artwork (fallback to placeholder)
    return artwork[song] || `https://via.placeholder.com/200x200/${getRandomColor()}/ffffff?text=${song.substring(0, 2).toUpperCase()}`;
}
