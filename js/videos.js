// Videos page specific functionality
document.addEventListener('DOMContentLoaded', function() {
    initVideosPage();
});

function initVideosPage() {
    loadVideoData();
    initViewToggle();
    initFilters();
    initVideoModal();
    initVideoInteractions();
}

// Load and display video data
async function loadVideoData() {
    const videoContainer = document.getElementById('video-container');
    if (!videoContainer) return;
    
    // Show loading state
    videoContainer.innerHTML = '<div class="loading"><div class="loading-spinner"></div></div>';
    
    try {
        // Simulate API call
        const videoData = await fetchChartData('videos');
        displayVideos(videoData.slice(1)); // Skip first (featured)
    } catch (error) {
        console.error('Error loading video data:', error);
        videoContainer.innerHTML = '<div class="error">Failed to load video data. Please try again.</div>';
    }
}

function displayVideos(videos) {
    const videoContainer = document.getElementById('video-container');
    if (!videoContainer) return;
    
    videoContainer.innerHTML = '';
    
    videos.forEach((video, index) => {
        const videoItem = createVideoItem(video, index + 2); // Start from rank 2
        videoContainer.appendChild(videoItem);
    });
    
    // Add stagger animation
    const videoItems = videoContainer.querySelectorAll('.video-item');
    videoItems.forEach((item, index) => {
        setTimeout(() => {
            item.classList.add('animate-slide-up');
        }, index * 50);
    });
}

function createVideoItem(video, rank) {
    const videoItem = document.createElement('div');
    videoItem.className = 'video-item';
    videoItem.setAttribute('data-rank', rank);
    videoItem.setAttribute('data-category', video.category || 'music-video');
    videoItem.setAttribute('data-views', video.views);
    
    videoItem.innerHTML = `
        <div class="video-thumbnail">
            <img src="${video.thumbnail || `https://via.placeholder.com/300x200/${getRandomColor()}/ffffff?text=Video+${rank}`}" 
                 alt="${video.title}" class="thumbnail-img">
            <div class="play-overlay">
                <button class="play-btn" onclick="playVideo('${video.id}')">
                    <i class="fas fa-play"></i>
                </button>
            </div>
            <div class="video-duration">${video.duration}</div>
        </div>
        <div class="video-info">
            <h3 class="video-title">${video.title}</h3>
            <p class="video-artist">${video.artist}</p>
            <div class="video-stats">
                <div class="stat">
                    <i class="fas fa-eye"></i>
                    <span>${formatViews(video.views)}</span>
                </div>
                <div class="stat">
                    <i class="fas fa-thumbs-up"></i>
                    <span>${formatLikes(video.likes)}</span>
                </div>
                <div class="stat">
                    <i class="fas fa-clock"></i>
                    <span>${video.uploadDate}</span>
                </div>
            </div>
        </div>
    `;
    
    // Add click handler for modal
    videoItem.addEventListener('click', () => {
        showVideoModal(video);
    });
    
    return videoItem;
}

// View toggle functionality
function initViewToggle() {
    const viewBtns = document.querySelectorAll('.view-btn');
    const videoContainer = document.getElementById('video-container');
    
    viewBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            viewBtns.forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            
            const view = btn.getAttribute('data-view');
            toggleView(view, videoContainer);
        });
    });
}

function toggleView(view, container) {
    const videoItems = container.querySelectorAll('.video-item');
    
    videoItems.forEach(item => {
        if (view === 'list') {
            item.classList.add('list-view');
        } else {
            item.classList.remove('list-view');
        }
    });
    
    if (view === 'list') {
        container.classList.add('list-view');
    } else {
        container.classList.remove('list-view');
    }
}

// Filter functionality
function initFilters() {
    const filterTabs = document.querySelectorAll('.filter-tab');
    const categoryFilter = document.querySelector('.category-filter');
    const timeFilter = document.querySelector('.time-filter');
    const sortBtn = document.querySelector('.sort-btn');
    
    filterTabs.forEach(tab => {
        tab.addEventListener('click', () => {
            filterTabs.forEach(t => t.classList.remove('active'));
            tab.classList.add('active');
            
            const filter = tab.getAttribute('data-filter');
            applyVideoFilter(filter);
        });
    });
    
    if (categoryFilter) {
        categoryFilter.addEventListener('change', (e) => {
            applyCategoryFilter(e.target.value);
        });
    }
    
    if (timeFilter) {
        timeFilter.addEventListener('change', (e) => {
            applyTimeFilter(e.target.value);
        });
    }
    
    if (sortBtn) {
        sortBtn.addEventListener('click', () => {
            toggleVideoSort();
        });
    }
}

function applyVideoFilter(filter) {
    const videoItems = document.querySelectorAll('.video-item');
    
    videoItems.forEach(item => {
        let shouldShow = true;
        
        switch (filter) {
            case 'trending':
                shouldShow = parseInt(item.getAttribute('data-views')) > 10000000;
                break;
            case 'new':
                shouldShow = item.querySelector('.stat:last-child span').textContent.includes('day');
                break;
            case 'viral':
                shouldShow = parseInt(item.getAttribute('data-views')) > 50000000;
                break;
            case 'all':
            default:
                shouldShow = true;
                break;
        }
        
        item.style.display = shouldShow ? 'block' : 'none';
    });
}

function applyCategoryFilter(category) {
    const videoItems = document.querySelectorAll('.video-item');
    
    videoItems.forEach(item => {
        const itemCategory = item.getAttribute('data-category');
        const shouldShow = category === 'all' || itemCategory === category;
        item.style.display = shouldShow ? 'block' : 'none';
    });
}

function applyTimeFilter(time) {
    const videoItems = document.querySelectorAll('.video-item');
    const now = new Date();
    
    videoItems.forEach(item => {
        const uploadDate = item.querySelector('.stat:last-child span').textContent;
        let shouldShow = true;
        
        if (time !== 'all') {
            // Simple date filtering logic
            if (time === 'today' && !uploadDate.includes('day')) shouldShow = false;
            if (time === 'week' && !uploadDate.includes('day') && !uploadDate.includes('week')) shouldShow = false;
            if (time === 'month' && !uploadDate.includes('day') && !uploadDate.includes('week') && !uploadDate.includes('month')) shouldShow = false;
            if (time === 'year' && !uploadDate.includes('day') && !uploadDate.includes('week') && !uploadDate.includes('month') && !uploadDate.includes('year')) shouldShow = false;
        }
        
        item.style.display = shouldShow ? 'block' : 'none';
    });
}

function toggleVideoSort() {
    const videoContainer = document.getElementById('video-container');
    const items = Array.from(videoContainer.querySelectorAll('.video-item'));
    
    // Toggle between ascending and descending view count order
    const isAscending = videoContainer.getAttribute('data-sort') === 'asc';
    const newSort = isAscending ? 'desc' : 'asc';
    
    items.sort((a, b) => {
        const viewsA = parseInt(a.getAttribute('data-views'));
        const viewsB = parseInt(b.getAttribute('data-views'));
        return newSort === 'asc' ? viewsA - viewsB : viewsB - viewsA;
    });
    
    // Re-append sorted items
    items.forEach(item => videoContainer.appendChild(item));
    videoContainer.setAttribute('data-sort', newSort);
}

// Video modal functionality
function initVideoModal() {
    const modal = document.getElementById('video-modal');
    const closeBtn = modal?.querySelector('.close-modal');
    
    if (closeBtn) {
        closeBtn.addEventListener('click', () => {
            hideVideoModal(modal);
        });
    }
    
    // Close modal on overlay click
    if (modal) {
        modal.addEventListener('click', (e) => {
            if (e.target === modal) {
                hideVideoModal(modal);
            }
        });
    }
}

function showVideoModal(video) {
    const modal = document.getElementById('video-modal');
    const title = modal.querySelector('#modal-video-title');
    const views = modal.querySelector('.views');
    const likes = modal.querySelector('.likes');
    const date = modal.querySelector('.date');
    const description = modal.querySelector('.modal-description');
    
    if (title) title.textContent = video.title;
    if (views) views.textContent = formatViews(video.views) + ' views';
    if (likes) likes.textContent = formatLikes(video.likes) + ' likes';
    if (date) date.textContent = 'Uploaded ' + video.uploadDate;
    if (description) description.textContent = video.description || 'No description available.';
    
    modal.classList.add('active');
    document.body.style.overflow = 'hidden';
}

function hideVideoModal(modal) {
    if (modal) {
        modal.classList.remove('active');
        document.body.style.overflow = 'auto';
    }
}

// Video interactions
function initVideoInteractions() {
    // Share functionality
    const shareBtn = document.querySelector('.action-btn[title="Share Videos"]');
    if (shareBtn) {
        shareBtn.addEventListener('click', () => {
            shareVideos();
        });
    }
}

function shareVideos() {
    if (navigator.share) {
        navigator.share({
            title: 'Timeline Top 100 Videos',
            text: 'Check out the most popular videos on Timeline!',
            url: window.location.href
        });
    } else {
        // Fallback: copy to clipboard
        navigator.clipboard.writeText(window.location.href).then(() => {
            showNotification('Videos link copied to clipboard!');
        });
    }
}

// Video interaction functions
function playVideo(videoId) {
    console.log('Playing video:', videoId);
    showNotification('Now playing: ' + videoId);
    // Implement actual video playback functionality
}

// Utility functions
function formatViews(views) {
    if (views >= 1000000000) {
        return (views / 1000000000).toFixed(1) + 'B';
    } else if (views >= 1000000) {
        return (views / 1000000).toFixed(1) + 'M';
    } else if (views >= 1000) {
        return (views / 1000).toFixed(1) + 'K';
    }
    return views.toString();
}

function formatLikes(likes) {
    if (likes >= 1000000) {
        return (likes / 1000000).toFixed(1) + 'M';
    } else if (likes >= 1000) {
        return (likes / 1000).toFixed(1) + 'K';
    }
    return likes.toString();
}

function getRandomColor() {
    const colors = ['00d4aa', '667eea', '764ba2', 'f093fb', 'f5576c', '4facfe', '00f2fe'];
    return colors[Math.floor(Math.random() * colors.length)];
}

function getRealVideoThumbnail(video, artist) {
    // Real video thumbnails for popular music videos
    const thumbnails = {
        'Golden - Official Music Video': 'https://img.youtube.com/vi/dQw4w9WgXcQ/maxresdefault.jpg',
        'Ordinary - Live Performance': 'https://img.youtube.com/vi/9bZkp7q19f0/maxresdefault.jpg',
        'Soda - Lyric Video': 'https://img.youtube.com/vi/kJQP7kiw5Fk/maxresdefault.jpg',
        'Midnight - Behind the Scenes': 'https://img.youtube.com/vi/fJ9rUzIMcZQ/maxresdefault.jpg',
        'Sunset - Acoustic Version': 'https://img.youtube.com/vi/L_jWHffIx5E/maxresdefault.jpg',
        'Dreams - Dance Challenge': 'https://img.youtube.com/vi/5qap5aO4i9A/maxresdefault.jpg',
        'Reality - Concert Clip': 'https://img.youtube.com/vi/M7lc1UVf-VE/maxresdefault.jpg',
        'Fantasy - Music Video': 'https://img.youtube.com/vi/ZZ5LpwO-An4/maxresdefault.jpg',
        'Echo - Live Session': 'https://img.youtube.com/vi/hT_nvWreIhg/maxresdefault.jpg',
        'Silence - Official Video': 'https://img.youtube.com/vi/3JZ_D3ELwOQ/maxresdefault.jpg'
    };
    
    return thumbnails[video] || `https://via.placeholder.com/300x200/${getRandomColor()}/ffffff?text=${video.substring(0, 10)}`;
}

// Enhanced mock data generation for videos
async function generateMockVideoData(count) {
    const artists = [
        'Taylor Swift', 'Drake', 'Ariana Grande', 'The Weeknd', 'Billie Eilish',
        'Post Malone', 'Dua Lipa', 'Ed Sheeran', 'Justin Bieber', 'Olivia Rodrigo',
        'Bad Bunny', 'Harry Styles', 'SZA', 'Travis Scott', 'Kendrick Lamar',
        'Lizzo', 'Doja Cat', 'The Kid LAROI', 'Glass Animals', 'Måneskin'
    ];
    
    const videos = [
        'Golden - Official Music Video', 'Ordinary - Live Performance', 'Soda - Lyric Video',
        'Midnight - Behind the Scenes', 'Sunset - Acoustic Version', 'Dreams - Dance Challenge',
        'Reality - Concert Clip', 'Fantasy - Music Video', 'Echo - Live Session',
        'Silence - Official Video', 'Thunder - Remix Video', 'Lightning - Tribute Video',
        'Storm - Live Performance', 'Rain - Music Video', 'Sunshine - Lyric Video',
        'Moonlight - Behind the Scenes', 'Starlight - Acoustic Version', 'Daylight - Dance Video',
        'Nightfall - Concert Clip', 'Dawn - Official Video'
    ];
    
    const categories = ['music-video', 'live', 'lyric', 'behind-scenes'];
    const durations = ['2:30', '3:15', '4:02', '2:45', '3:30', '4:15', '2:58', '3:42'];
    const timeAgo = ['1 day ago', '2 days ago', '1 week ago', '2 weeks ago', '1 month ago', '2 months ago', '3 months ago', '6 months ago'];
    
    const data = [];
    for (let i = 0; i < count; i++) {
        const thumbnail = window.imageService ? await window.imageService.getVideoThumbnail(videos[i % videos.length]) : getRealVideoThumbnail(videos[i % videos.length], artists[i % artists.length]);
        data.push({
        id: `video-${i + 1}`,
        title: videos[i % videos.length],
        artist: artists[i % artists.length],
        category: categories[i % categories.length],
        views: Math.floor(Math.random() * 100000000) + 1000000,
        likes: Math.floor(Math.random() * 5000000) + 100000,
        duration: durations[i % durations.length],
        uploadDate: timeAgo[i % timeAgo.length],
        description: `Official music video for "${videos[i % videos.length]}" featuring stunning visuals and choreography.`,
        thumbnail: thumbnail
        });
    }
    return data;
}
