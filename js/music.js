// Music page specific functionality
document.addEventListener('DOMContentLoaded', async function() {
    await initMusicPage();
});

async function initMusicPage() {
    await loadMusicData();
    initFilters();
    initModal();
    initMusicInteractions();
    initRealTimeUpdates();
}

// Load and display music data
async function loadMusicData() {
    const songsContainer = document.getElementById('songs-container');
    if (!songsContainer) return;
    
    // Show loading state
    songsContainer.innerHTML = '<div class="loading"><div class="loading-spinner"></div><p>Loading real-time music data...</p></div>';
    
    try {
        // Fetch real-time data from API
        console.log('Fetching real-time music data...');
        const realData = await window.musicApi.getBillboardHot100();
        
        if (realData && realData.length > 0) {
            console.log(`Successfully loaded ${realData.length} songs from API`);
            
            // Display the real data (skip first 3 for featured section)
            displaySongs(realData.slice(3));
            
            // Update featured songs (top 3)
            updateFeaturedSongs(realData.slice(0, 3));
            
            // Show success message
            showNotification('Real-time music data loaded successfully!', 'success');
            
        } else {
            throw new Error('No data received from API');
        }
        
    } catch (error) {
        console.error('Error loading real-time music data:', error);
        
        // Show error message instead of fallback
        songsContainer.innerHTML = `
            <div class="error-message">
                <div class="error-icon">⚠️</div>
                <h3>Unable to Load Real-Time Data</h3>
                <p>The music API is currently unavailable. This could be due to:</p>
                <ul>
                    <li>Network connectivity issues</li>
                    <li>API service maintenance</li>
                    <li>CORS restrictions</li>
                </ul>
                <div class="error-actions">
                    <button onclick="location.reload()" class="retry-button">Retry</button>
                    <button onclick="window.musicApi.clearCache(); location.reload()" class="clear-cache-button">Clear Cache & Retry</button>
                </div>
                <p class="error-note">Please check your internet connection and try again.</p>
            </div>
        `;
        
        showNotification('Failed to load real-time music data. Please try again.', 'error');
    }
}

// Update featured songs (top 3)
function updateFeaturedSongs(topThree) {
    const featuredContainer = document.querySelector('.songs-grid');
    if (!featuredContainer || !topThree || topThree.length === 0) return;
    
    const featuredItems = featuredContainer.querySelectorAll('.song-card');
    
    topThree.forEach((song, index) => {
        if (featuredItems[index]) {
            const item = featuredItems[index];
            
            // Update rank
            const rankElement = item.querySelector('.rank-badge');
            if (rankElement) rankElement.textContent = song.rank || (index + 1);
            
            // Update title
            const titleElement = item.querySelector('.song-title');
            if (titleElement) titleElement.textContent = song.title || 'Unknown';
            
            // Update artist
            const artistElement = item.querySelector('.song-artist');
            if (artistElement) artistElement.textContent = song.artist || 'Unknown';
            
            // Update weeks
            const weeksElement = item.querySelector('.weeks');
            if (weeksElement) weeksElement.textContent = `${song.weeks} weeks`;
            
            // Update peak
            const peakElement = item.querySelector('.peak');
            if (peakElement) peakElement.textContent = `Peak: #${song.peak}`;
            
            // Update genre
            const genreElement = item.querySelector('.detail-value');
            if (genreElement) genreElement.textContent = song.genre || 'Unknown';
            
            // Update streams
            const streamsElement = item.querySelectorAll('.detail-value')[1];
            if (streamsElement) streamsElement.textContent = formatStreams(song.streams || 0);
            
            // Update last week
            const lastWeekElement = item.querySelectorAll('.detail-value')[2];
            if (lastWeekElement) lastWeekElement.textContent = `#${song.lastWeek}`;
            
            // Update artwork
            const artworkElement = item.querySelector('.artwork-img');
            if (artworkElement) {
                artworkElement.src = song.artwork || `https://ui-avatars.com/api/?name=${encodeURIComponent(song.title)}&size=200&background=00d4aa&color=fff&bold=true`;
                artworkElement.alt = song.title || 'Song';
            }
        }
    });
}

function displaySongs(songs) {
    const songsContainer = document.getElementById('songs-container');
    if (!songsContainer) return;
    
    songsContainer.innerHTML = '';
    
    songs.forEach((song, index) => {
        const songItem = createSongItem(song, index + 4); // Start from rank 4
        songsContainer.appendChild(songItem);
    });
    
    // Add stagger animation
    const songItems = songsContainer.querySelectorAll('.song-item');
    songItems.forEach((item, index) => {
        setTimeout(() => {
            item.classList.add('animate-slide-up');
        }, index * 50);
    });
}

function createSongItem(song, rank) {
    const songItem = document.createElement('div');
    songItem.className = 'song-item';
    songItem.setAttribute('data-rank', rank);
    songItem.setAttribute('data-genre', song.genre || 'pop');
    songItem.setAttribute('data-artist', song.artist || 'unknown');
    songItem.setAttribute('data-streams', song.streams || 0);
    
    // Add trend indicators
    if (song.isNew) {
        songItem.classList.add('new-entry');
    } else if (song.isReEntry) {
        songItem.classList.add('re-entry');
    } else if (song.hasGains) {
        songItem.classList.add('gains');
    }
    
    // Get real artwork or fallback
    const artworkUrl = song.artwork || `https://ui-avatars.com/api/?name=${encodeURIComponent(song.title)}&size=200&background=00d4aa&color=fff&bold=true`;
    
    songItem.innerHTML = `
        <div class="rank-badge">${rank}</div>
        <div class="song-artwork">
            <img src="${artworkUrl}" 
                 alt="${song.title}" class="artwork-img"
                 onerror="this.src='https://ui-avatars.com/api/?name=${encodeURIComponent(song.title)}&size=200&background=00d4aa&color=fff&bold=true'">
            <div class="play-indicator">
                <i class="fas fa-play"></i>
            </div>
        </div>
        <div class="song-info">
            <h3 class="song-title">${song.title || 'Unknown'}</h3>
            <p class="song-artist">${song.artist || 'Unknown'}</p>
            <div class="song-stats">
                <span class="weeks">${song.weeks} weeks</span>
                <span class="peak">Peak: #${song.peak}</span>
                <span class="last-week">Last Week: #${song.lastWeek}</span>
            </div>
        </div>
        <div class="song-metrics">
            <div class="metric">
                <span class="metric-label">Streams</span>
                <span class="metric-value">${formatStreams(song.streams || 0)}</span>
            </div>
            <div class="metric">
                <span class="metric-label">Genre</span>
                <span class="metric-value">${song.genre || 'Unknown'}</span>
            </div>
            <div class="metric">
                <span class="metric-label">Plays</span>
                <span class="metric-value">${formatPlays(song.playCount || 0)}</span>
            </div>
        </div>
        <div class="song-actions">
            <button class="action-btn" title="Play Song" onclick="playSong('${song.id}')">
                <i class="fas fa-play"></i>
            </button>
            <button class="action-btn" title="Add to Playlist" onclick="addToPlaylist('${song.id}')">
                <i class="fas fa-plus"></i>
            </button>
            <button class="action-btn" title="Share Song" onclick="shareSong('${song.id}')">
                <i class="fas fa-share"></i>
            </button>
        </div>
    `;
    
    return songItem;
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
            applyMusicFilter(filter);
        });
    });
    
    if (genreFilter) {
        genreFilter.addEventListener('change', (e) => {
            applyGenreFilter(e.target.value);
        });
    }
    
    if (sortBtn) {
        sortBtn.addEventListener('click', () => {
            toggleMusicSort();
        });
    }
}

function applyMusicFilter(filter) {
    const songItems = document.querySelectorAll('.song-item');
    
    songItems.forEach(item => {
        const itemGenre = item.getAttribute('data-genre');
        let shouldShow = true;
        
        switch (filter) {
            case 'pop':
                shouldShow = itemGenre === 'pop';
                break;
            case 'hip-hop':
                shouldShow = itemGenre === 'hip-hop';
                break;
            case 'rock':
                shouldShow = itemGenre === 'rock';
                break;
            case 'country':
                shouldShow = itemGenre === 'country';
                break;
            case 'r&b':
                shouldShow = itemGenre === 'r&b';
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
    const songItems = document.querySelectorAll('.song-item');
    
    songItems.forEach(item => {
        const itemGenre = item.getAttribute('data-genre');
        const shouldShow = genre === 'all' || itemGenre === genre;
        item.style.display = shouldShow ? 'flex' : 'none';
    });
}

function toggleMusicSort() {
    const songsContainer = document.getElementById('songs-container');
    const items = Array.from(songsContainer.querySelectorAll('.song-item'));
    
    // Toggle between ascending and descending rank order
    const isAscending = songsContainer.getAttribute('data-sort') === 'asc';
    const newSort = isAscending ? 'desc' : 'asc';
    
    items.sort((a, b) => {
        const rankA = parseInt(a.getAttribute('data-rank'));
        const rankB = parseInt(b.getAttribute('data-rank'));
        return newSort === 'asc' ? rankA - rankB : rankB - rankA;
    });
    
    // Re-append sorted items
    items.forEach(item => songsContainer.appendChild(item));
    songsContainer.setAttribute('data-sort', newSort);
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

// Music interactions
function initMusicInteractions() {
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
            shareMusicChart();
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
                <h3>Select Date</h3>
                <button class="close-modal">&times;</button>
            </div>
            <div class="modal-body">
                <div class="date-picker">
                    <input type="date" id="music-date" class="date-input">
                    <button class="btn btn-primary" onclick="loadMusicForDate()">Load Data</button>
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

function loadMusicForDate() {
    const dateInput = document.getElementById('music-date');
    const selectedDate = dateInput.value;
    
    if (selectedDate) {
        // Update date button text
        const dateBtn = document.querySelector('.date-btn');
        const formattedDate = new Date(selectedDate).toLocaleDateString('en-US', {
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        });
        
        dateBtn.innerHTML = `<i class="fas fa-calendar"></i> AS OF ${formattedDate.toUpperCase()}`;
        
        // Reload music data for selected date
        loadMusicData();
        
        // Close modal
        document.querySelector('.modal').remove();
    }
}

function shareMusicChart() {
    if (navigator.share) {
        navigator.share({
            title: 'Timeline Hot 100 Music Chart',
            text: 'Check out the latest Hot 100 music chart on Timeline!',
            url: window.location.href
        });
    } else {
        // Fallback: copy to clipboard
        navigator.clipboard.writeText(window.location.href).then(() => {
            showNotification('Music chart link copied to clipboard!');
        });
    }
}

// Music interaction functions
function playSong(songId) {
    console.log('Playing song:', songId);
    showNotification('Playing song: ' + songId);
    // Implement song playback functionality
}

function addToPlaylist(songId) {
    console.log('Adding to playlist:', songId);
    showNotification('Added to playlist: ' + songId);
    // Implement playlist functionality
}

function shareSong(songId) {
    console.log('Sharing song:', songId);
    showNotification('Sharing song: ' + songId);
    // Implement song sharing functionality
}

// Real-time updates functionality
function initRealTimeUpdates() {
    // Add refresh button to the page header
    addRefreshButton();
    
    // Set up automatic refresh every 10 minutes
    setInterval(async () => {
        console.log('Auto-refreshing music data...');
        await refreshMusicData();
    }, 10 * 60 * 1000); // 10 minutes
    
    // Add keyboard shortcut for refresh (Ctrl+R or Cmd+R)
    document.addEventListener('keydown', (e) => {
        if ((e.ctrlKey || e.metaKey) && e.key === 'r') {
            e.preventDefault();
            refreshMusicData();
        }
    });
}

// Add refresh button to the page
function addRefreshButton() {
    const pageHeader = document.querySelector('.music-header .music-controls');
    if (!pageHeader) return;
    
    const refreshButton = document.createElement('button');
    refreshButton.className = 'refresh-button';
    refreshButton.innerHTML = `
        <i class="fas fa-sync-alt"></i>
        <span>Refresh Data</span>
    `;
    refreshButton.onclick = refreshMusicData;
    
    // Add styles for refresh button
    if (!document.querySelector('#refresh-button-styles')) {
        const styles = document.createElement('style');
        styles.id = 'refresh-button-styles';
        styles.textContent = `
            .refresh-button {
                display: flex;
                align-items: center;
                gap: 8px;
                padding: 10px 20px;
                background: linear-gradient(135deg, #00d4aa, #00b894);
                color: white;
                border: none;
                border-radius: 25px;
                cursor: pointer;
                font-weight: 600;
                transition: all 0.3s ease;
                box-shadow: 0 5px 15px rgba(0, 212, 170, 0.3);
            }
            .refresh-button:hover {
                transform: translateY(-2px);
                box-shadow: 0 8px 25px rgba(0, 212, 170, 0.4);
            }
            .refresh-button:active {
                transform: translateY(0);
            }
            .refresh-button i {
                transition: transform 0.3s ease;
            }
            .refresh-button.loading i {
                animation: spin 1s linear infinite;
            }
            @keyframes spin {
                from { transform: rotate(0deg); }
                to { transform: rotate(360deg); }
            }
        `;
        document.head.appendChild(styles);
    }
    
    pageHeader.appendChild(refreshButton);
}

// Refresh music data
async function refreshMusicData() {
    const refreshButton = document.querySelector('.refresh-button');
    if (refreshButton) {
        refreshButton.classList.add('loading');
        refreshButton.disabled = true;
    }
    
    try {
        // Clear cache to force fresh data
        window.musicApi.clearCache();
        
        // Reload data
        await loadMusicData();
        
        showNotification('Music data refreshed successfully!', 'success');
        
    } catch (error) {
        console.error('Error refreshing data:', error);
        showNotification('Failed to refresh data. Please try again.', 'error');
    } finally {
        if (refreshButton) {
            refreshButton.classList.remove('loading');
            refreshButton.disabled = false;
        }
    }
}

// Notification system
function showNotification(message, type = 'info') {
    // Remove existing notifications
    const existingNotifications = document.querySelectorAll('.notification');
    existingNotifications.forEach(notification => notification.remove());
    
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.innerHTML = `
        <div class="notification-content">
            <span class="notification-icon">
                ${type === 'success' ? '✅' : type === 'warning' ? '⚠️' : type === 'error' ? '❌' : 'ℹ️'}
            </span>
            <span class="notification-message">${message}</span>
            <button class="notification-close" onclick="this.parentElement.parentElement.remove()">×</button>
        </div>
    `;
    
    // Add styles if not already present
    if (!document.querySelector('#notification-styles')) {
        const styles = document.createElement('style');
        styles.id = 'notification-styles';
        styles.textContent = `
            .notification {
                position: fixed;
                top: 20px;
                right: 20px;
                z-index: 10000;
                max-width: 400px;
                padding: 15px 20px;
                border-radius: 10px;
                box-shadow: 0 10px 30px rgba(0,0,0,0.2);
                animation: slideInRight 0.3s ease-out;
                font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            }
            .notification-success {
                background: linear-gradient(135deg, #00d4aa, #00b894);
                color: white;
            }
            .notification-warning {
                background: linear-gradient(135deg, #f39c12, #e67e22);
                color: white;
            }
            .notification-error {
                background: linear-gradient(135deg, #e74c3c, #c0392b);
                color: white;
            }
            .notification-info {
                background: linear-gradient(135deg, #3498db, #2980b9);
                color: white;
            }
            .notification-content {
                display: flex;
                align-items: center;
                gap: 10px;
            }
            .notification-icon {
                font-size: 1.2rem;
            }
            .notification-message {
                flex: 1;
                font-weight: 500;
            }
            .notification-close {
                background: none;
                border: none;
                color: white;
                font-size: 1.5rem;
                cursor: pointer;
                padding: 0;
                width: 20px;
                height: 20px;
                display: flex;
                align-items: center;
                justify-content: center;
                border-radius: 50%;
                transition: background-color 0.2s ease;
            }
            .notification-close:hover {
                background-color: rgba(255,255,255,0.2);
            }
            @keyframes slideInRight {
                from {
                    transform: translateX(100%);
                    opacity: 0;
                }
                to {
                    transform: translateX(0);
                    opacity: 1;
                }
            }
        `;
        document.head.appendChild(styles);
    }
    
    // Add error message styles if not already present
    if (!document.querySelector('#error-message-styles')) {
        const errorStyles = document.createElement('style');
        errorStyles.id = 'error-message-styles';
        errorStyles.textContent = `
            .error-message {
                text-align: center;
                padding: 40px 20px;
                background: white;
                border-radius: 15px;
                box-shadow: 0 10px 30px rgba(0,0,0,0.1);
                max-width: 500px;
                margin: 0 auto;
            }
            .error-icon {
                font-size: 4rem;
                margin-bottom: 20px;
            }
            .error-message h3 {
                color: #e74c3c;
                margin-bottom: 15px;
                font-size: 1.5rem;
            }
            .error-message p {
                color: #666;
                margin-bottom: 15px;
                line-height: 1.6;
            }
            .error-message ul {
                text-align: left;
                color: #666;
                margin: 20px 0;
                padding-left: 20px;
            }
            .error-message li {
                margin-bottom: 8px;
            }
            .error-actions {
                display: flex;
                gap: 15px;
                justify-content: center;
                margin: 25px 0;
            }
            .retry-button, .clear-cache-button {
                padding: 12px 24px;
                border: none;
                border-radius: 25px;
                cursor: pointer;
                font-weight: 600;
                transition: all 0.3s ease;
            }
            .retry-button {
                background: linear-gradient(135deg, #00d4aa, #00b894);
                color: white;
            }
            .clear-cache-button {
                background: linear-gradient(135deg, #f39c12, #e67e22);
                color: white;
            }
            .retry-button:hover, .clear-cache-button:hover {
                transform: translateY(-2px);
                box-shadow: 0 5px 15px rgba(0,0,0,0.2);
            }
            .error-note {
                font-size: 0.9rem;
                color: #999;
                font-style: italic;
            }
        `;
        document.head.appendChild(errorStyles);
    }
    
    // Add to page
    document.body.appendChild(notification);
    
    // Auto remove after 5 seconds
    setTimeout(() => {
        if (notification.parentElement) {
            notification.style.animation = 'slideInRight 0.3s ease-out reverse';
            setTimeout(() => {
                if (notification.parentElement) {
                    notification.remove();
                }
            }, 300);
        }
    }, 5000);
}

// Utility functions
function formatStreams(streams) {
    if (streams >= 1000000000) {
        return (streams / 1000000000).toFixed(1) + 'B';
    } else if (streams >= 1000000) {
        return (streams / 1000000).toFixed(1) + 'M';
    } else if (streams >= 1000) {
        return (streams / 1000).toFixed(1) + 'K';
    }
    return streams.toString();
}

function formatPlays(plays) {
    if (plays >= 1000000) {
        return (plays / 1000000).toFixed(1) + 'M';
    } else if (plays >= 1000) {
        return (plays / 1000).toFixed(1) + 'K';
    }
    return plays.toString();
}
