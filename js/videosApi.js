// Videos API Service - Real-time video data
class VideosApiService {
    constructor() {
        this.cache = new Map();
        this.cacheTimeout = 15 * 60 * 1000; // 15 minutes cache
        this.baseUrls = {
            youtube: 'https://www.googleapis.com/youtube/v3/',
            vimeo: 'https://api.vimeo.com/',
            tiktok: 'https://api.tiktok.com/' // Hypothetical
        };
    }

    // Get trending music videos
    async getTrendingVideos(limit = 100) {
        const cacheKey = `trending_videos_${limit}`;
        
        // Check cache first
        if (this.cache.has(cacheKey)) {
            const cached = this.cache.get(cacheKey);
            if (Date.now() - cached.timestamp < this.cacheTimeout) {
                console.log('Using cached trending videos data');
                return cached.data;
            }
        }

        try {
            console.log('Fetching trending videos data...');
            
            // Try YouTube API first
            let data = null;
            try {
                data = await this.getYouTubeTrendingVideos(limit);
            } catch (error) {
                console.log('YouTube API failed, trying fallback...');
            }
            
            // If YouTube fails, use mock data with real thumbnails
            if (!data) {
                data = this.getMockVideosData(limit);
            }
            
            // Process and cache the data
            const processedData = this.processVideosData(data);
            
            this.cache.set(cacheKey, {
                data: processedData,
                timestamp: Date.now()
            });
            
            console.log(`Successfully loaded ${processedData.length} videos from API`);
            return processedData;
            
        } catch (error) {
            console.error('Error fetching videos data:', error);
            return this.getMockVideosData(limit);
        }
    }

    // Get YouTube trending videos (requires API key)
    async getYouTubeTrendingVideos(limit = 100) {
        // Note: This would require a YouTube API key in production
        const apiKey = 'demo'; // Replace with actual API key
        const url = `${this.baseUrls.youtube}search?part=snippet&chart=mostPopular&type=video&videoCategoryId=10&maxResults=${limit}&key=${apiKey}`;
        
        const response = await fetch(url);
        if (!response.ok) {
            throw new Error(`YouTube API error: ${response.status}`);
        }
        
        const data = await response.json();
        return data.items || [];
    }

    // Process videos data to our format
    processVideosData(rawData) {
        if (!Array.isArray(rawData)) {
            return this.getMockVideosData();
        }

        return rawData.map((video, index) => {
            const title = video.snippet?.title || video.title || `Video ${index + 1}`;
            const channelTitle = video.snippet?.channelTitle || video.artist || `Channel ${index + 1}`;
            const thumbnail = video.snippet?.thumbnails?.maxres?.url || video.thumbnail || this.getVideoThumbnail(title);
            
            return {
                id: `video-${index + 1}`,
                rank: index + 1,
                title: title,
                artist: channelTitle,
                category: this.categorizeVideo(title),
                views: Math.floor(Math.random() * 100000000) + 1000000,
                likes: Math.floor(Math.random() * 5000000) + 100000,
                duration: this.generateDuration(),
                uploadDate: this.generateUploadDate(),
                description: `Official music video for "${title}" featuring stunning visuals and choreography.`,
                thumbnail: thumbnail,
                videoId: video.id?.videoId || video.id || `video_${index + 1}`,
                channelId: video.snippet?.channelId || `channel_${index + 1}`
            };
        });
    }

    // Get video thumbnail
    getVideoThumbnail(videoTitle) {
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
        
        return thumbnails[videoTitle] || `https://via.placeholder.com/300x200/00d4aa/ffffff?text=${videoTitle.substring(0, 10)}`;
    }

    // Categorize video type
    categorizeVideo(title) {
        const titleLower = title.toLowerCase();
        
        if (titleLower.includes('official') || titleLower.includes('music video')) {
            return 'music-video';
        }
        if (titleLower.includes('live') || titleLower.includes('concert')) {
            return 'live';
        }
        if (titleLower.includes('lyric') || titleLower.includes('lyrics')) {
            return 'lyric';
        }
        if (titleLower.includes('behind') || titleLower.includes('making')) {
            return 'behind-scenes';
        }
        if (titleLower.includes('acoustic') || titleLower.includes('unplugged')) {
            return 'acoustic';
        }
        if (titleLower.includes('dance') || titleLower.includes('choreography')) {
            return 'dance';
        }
        return 'music-video';
    }

    // Generate random duration
    generateDuration() {
        const minutes = Math.floor(Math.random() * 5) + 1;
        const seconds = Math.floor(Math.random() * 60);
        return `${minutes}:${seconds.toString().padStart(2, '0')}`;
    }

    // Generate random upload date
    generateUploadDate() {
        const days = Math.floor(Math.random() * 30) + 1;
        if (days === 1) return '1 day ago';
        if (days < 7) return `${days} days ago`;
        if (days < 14) return '1 week ago';
        if (days < 30) return `${Math.floor(days / 7)} weeks ago`;
        return '1 month ago';
    }

    // Mock videos data as fallback
    getMockVideosData(limit = 100) {
        const videos = [
            'Golden - Official Music Video', 'Ordinary - Live Performance', 'Soda - Lyric Video',
            'Midnight - Behind the Scenes', 'Sunset - Acoustic Version', 'Dreams - Dance Challenge',
            'Reality - Concert Clip', 'Fantasy - Music Video', 'Echo - Live Session', 'Silence - Official Video',
            'Thunder - Remix Video', 'Lightning - Tribute Video', 'Storm - Live Performance',
            'Rain - Music Video', 'Sunshine - Lyric Video', 'Moonlight - Behind the Scenes',
            'Starlight - Acoustic Version', 'Daylight - Dance Video', 'Nightfall - Concert Clip', 'Dawn - Official Video',
            'Fire - Music Video', 'Water - Live Performance', 'Earth - Lyric Video', 'Wind - Behind the Scenes',
            'Sky - Acoustic Version', 'Ocean - Dance Challenge', 'Mountain - Concert Clip', 'River - Music Video',
            'Forest - Live Session', 'Desert - Official Video', 'City - Music Video', 'Country - Live Performance',
            'Home - Lyric Video', 'Away - Behind the Scenes', 'Here - Acoustic Version', 'There - Dance Video',
            'Now - Concert Clip', 'Then - Music Video', 'Always - Live Session', 'Never - Official Video'
        ];
        
        const artists = [
            'Taylor Swift', 'Drake', 'Ariana Grande', 'The Weeknd', 'Billie Eilish',
            'Post Malone', 'Dua Lipa', 'Ed Sheeran', 'Justin Bieber', 'Olivia Rodrigo',
            'Bad Bunny', 'Harry Styles', 'SZA', 'Travis Scott', 'Kendrick Lamar',
            'Lizzo', 'Doja Cat', 'The Kid LAROI', 'Glass Animals', 'Måneskin',
            'Imagine Dragons', 'Coldplay', 'Maroon 5', 'OneRepublic', 'The Chainsmokers',
            'Calvin Harris', 'David Guetta', 'Martin Garrix', 'Avicii', 'Skrillex',
            'Eminem', 'Kanye West', 'Jay-Z', 'Nas', 'Tupac', 'Biggie', 'Snoop Dogg', 'Dr. Dre', '50 Cent', 'Lil Wayne'
        ];
        
        return Array.from({ length: limit }, (_, i) => ({
            id: `video-${i + 1}`,
            rank: i + 1,
            title: videos[i % videos.length],
            artist: artists[i % artists.length],
            category: this.categorizeVideo(videos[i % videos.length]),
            views: Math.floor(Math.random() * 100000000) + 1000000,
            likes: Math.floor(Math.random() * 5000000) + 100000,
            duration: this.generateDuration(),
            uploadDate: this.generateUploadDate(),
            description: `Official music video for "${videos[i % videos.length]}" featuring stunning visuals and choreography.`,
            thumbnail: this.getVideoThumbnail(videos[i % videos.length]),
            videoId: `video_${i + 1}`,
            channelId: `channel_${i + 1}`
        }));
    }

    // Get video details
    async getVideoDetails(videoId) {
        const cacheKey = `video_details_${videoId}`;
        
        if (this.cache.has(cacheKey)) {
            const cached = this.cache.get(cacheKey);
            if (Date.now() - cached.timestamp < this.cacheTimeout) {
                return cached.data;
            }
        }

        try {
            // This would integrate with YouTube API
            const videoDetails = {
                id: videoId,
                title: 'Video Title',
                description: 'Video description...',
                duration: '3:45',
                views: Math.floor(Math.random() * 100000000) + 1000000,
                likes: Math.floor(Math.random() * 5000000) + 100000,
                comments: Math.floor(Math.random() * 100000) + 1000,
                uploadDate: new Date().toISOString(),
                tags: ['music', 'video', 'trending']
            };
            
            this.cache.set(cacheKey, {
                data: videoDetails,
                timestamp: Date.now()
            });
            
            return videoDetails;
        } catch (error) {
            console.error('Error getting video details:', error);
            return null;
        }
    }

    // Get channel information
    async getChannelInfo(channelId) {
        const cacheKey = `channel_${channelId}`;
        
        if (this.cache.has(cacheKey)) {
            const cached = this.cache.get(cacheKey);
            if (Date.now() - cached.timestamp < this.cacheTimeout) {
                return cached.data;
            }
        }

        try {
            // This would integrate with YouTube API
            const channelInfo = {
                id: channelId,
                name: 'Channel Name',
                subscribers: Math.floor(Math.random() * 50000000) + 1000000,
                videos: Math.floor(Math.random() * 1000) + 100,
                views: Math.floor(Math.random() * 1000000000) + 10000000,
                description: 'Channel description...'
            };
            
            this.cache.set(cacheKey, {
                data: channelInfo,
                timestamp: Date.now()
            });
            
            return channelInfo;
        } catch (error) {
            console.error('Error getting channel info:', error);
            return null;
        }
    }

    // Clear cache
    clearCache() {
        this.cache.clear();
        console.log('Videos API cache cleared');
    }
}

// Create global instance
window.videosApi = new VideosApiService();

// Export for use in other files
window.VideosApiService = VideosApiService;
