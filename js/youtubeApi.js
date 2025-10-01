// YouTube Data API v3 Service
class YouTubeApiService {
    constructor() {
        this.apiEnabled = window.YOUTUBE_API_ENABLED || false;
        this.proxyUrl = 'api/youtube-proxy.php';
        this.cache = new Map();
        this.cacheTimeout = 10 * 60 * 1000; // 10 minutes cache
        this.retryAttempts = 3;
        this.retryDelay = 1000;
        
        // Regional settings
        this.userRegion = window.USER_LOCATION?.country || 'ZW';
        this.userLanguage = window.USER_LOCATION?.language || 'en';
        this.africanCountries = window.AFRICAN_COUNTRIES || [];
        
        // Content categories
        this.musicCategories = {
            'ZW': '10', // Zimbabwe - Music
            'ZA': '10', // South Africa - Music
            'NG': '10', // Nigeria - Music
            'KE': '10', // Kenya - Music
            'GH': '10', // Ghana - Music
            'EG': '10', // Egypt - Music
            'MA': '10', // Morocco - Music
            'global': '10' // Global - Music
        };
    }

    // Get trending videos for a specific region
    async getTrendingVideos(region = null, category = '10', maxResults = 50) {
        if (!this.apiEnabled) {
            throw new Error('YouTube API not configured. Please run the installation wizard.');
        }

        const regionCode = region || this.userRegion;
        const cacheKey = `trending_${regionCode}_${category}_${maxResults}`;
        const cached = this.getCachedData(cacheKey);
        if (cached) {
            return cached;
        }

        try {
            const response = await this.fetchWithRetry(this.proxyUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    action: 'trending',
                    region: regionCode,
                    maxResults: maxResults
                })
            });
            const data = await response.json();
            
            if (data.error) {
                throw new Error(data.error.message);
            }

            const processedData = this.processVideoData(data.items || []);
            this.setCachedData(cacheKey, processedData);
            
            return processedData;
        } catch (error) {
            console.error('Error fetching trending videos:', error);
            throw error;
        }
    }

    // Get trending music videos
    async getTrendingMusic(region = null, maxResults = 50) {
        if (!this.apiEnabled) {
            throw new Error('YouTube API not configured. Please run the installation wizard.');
        }

        const regionCode = region || this.userRegion;
        const cacheKey = `trending_music_${regionCode}_${maxResults}`;
        const cached = this.getCachedData(cacheKey);
        if (cached) {
            return cached;
        }

        try {
            const response = await this.fetchWithRetry(this.proxyUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    action: 'trending_music',
                    region: regionCode,
                    maxResults: maxResults
                })
            });
            const data = await response.json();
            
            if (data.error) {
                throw new Error(data.error.message);
            }

            const processedData = this.processVideoData(data.items || []);
            this.setCachedData(cacheKey, processedData);
            
            return processedData;
        } catch (error) {
            console.error('Error fetching trending music:', error);
            throw error;
        }
    }

    // Search for videos by query
    async searchVideos(query, region = null, maxResults = 25) {
        const regionCode = region || this.userRegion;
        const cacheKey = `search_${query}_${regionCode}_${maxResults}`;
        const cached = this.getCachedData(cacheKey);
        if (cached) {
            return cached;
        }

        try {
            const params = new URLSearchParams({
                part: 'snippet',
                q: query,
                type: 'video',
                regionCode: regionCode,
                relevanceLanguage: this.userLanguage,
                maxResults: maxResults.toString(),
                key: this.apiKey
            });

            const response = await this.fetchWithRetry(`${this.baseUrl}/search?${params}`);
            const data = await response.json();
            
            if (data.error) {
                throw new Error(data.error.message);
            }

            // Get detailed video information
            const videoIds = data.items.map(item => item.id.videoId).join(',');
            const detailedVideos = await this.getVideoDetails(videoIds);
            
            this.setCachedData(cacheKey, detailedVideos);
            return detailedVideos;
        } catch (error) {
            console.error('Error searching videos:', error);
            throw error;
        }
    }

    // Get detailed video information
    async getVideoDetails(videoIds) {
        if (!videoIds) return [];
        
        const ids = Array.isArray(videoIds) ? videoIds.join(',') : videoIds;
        const cacheKey = `details_${ids}`;
        const cached = this.getCachedData(cacheKey);
        if (cached) {
            return cached;
        }

        try {
            const params = new URLSearchParams({
                part: 'snippet,statistics,contentDetails',
                id: ids,
                key: this.apiKey
            });

            const response = await this.fetchWithRetry(`${this.baseUrl}/videos?${params}`);
            const data = await response.json();
            
            if (data.error) {
                throw new Error(data.error.message);
            }

            const processedData = this.processVideoData(data.items || []);
            this.setCachedData(cacheKey, processedData);
            
            return processedData;
        } catch (error) {
            console.error('Error fetching video details:', error);
            throw error;
        }
    }

    // Get African music content
    async getAfricanMusic(maxResults = 50) {
        const africanQueries = [
            'African music',
            'Afrobeats',
            'Amapiano',
            'Zimbabwe music',
            'South African music',
            'Nigerian music',
            'Kenyan music',
            'Ghana music'
        ];

        const allVideos = [];
        
        for (const query of africanQueries) {
            try {
                const videos = await this.searchVideos(query, 'ZW', Math.ceil(maxResults / africanQueries.length));
                allVideos.push(...videos);
            } catch (error) {
                console.warn(`Failed to search for "${query}":`, error);
            }
        }

        // Remove duplicates and sort by view count
        const uniqueVideos = this.removeDuplicateVideos(allVideos);
        return uniqueVideos.slice(0, maxResults);
    }

    // Get Zimbabwean music specifically
    async getZimbabweanMusic(maxResults = 50) {
        const zimbabweQueries = [
            'Zimbabwe music',
            'Zim music',
            'Zimdancehall',
            'Jit',
            'Chimurenga',
            'Sungura',
            'Zimbabwe artists',
            'Oliver Mtukudzi',
            'Thomas Mapfumo',
            'Winky D',
            'Jah Prayzah'
        ];

        const allVideos = [];
        
        for (const query of zimbabweQueries) {
            try {
                const videos = await this.searchVideos(query, 'ZW', Math.ceil(maxResults / zimbabweQueries.length));
                allVideos.push(...videos);
            } catch (error) {
                console.warn(`Failed to search for "${query}":`, error);
            }
        }

        const uniqueVideos = this.removeDuplicateVideos(allVideos);
        return uniqueVideos.slice(0, maxResults);
    }

    // Get top 100 music based on region
    async getTop100Music(region = null) {
        const regionCode = region || this.userRegion;
        
        if (this.africanCountries.includes(regionCode)) {
            // For African countries, prioritize local content
            const localContent = await this.getTrendingMusic(regionCode, 50);
            const globalContent = await this.getTrendingMusic('global', 50);
            
            // Combine and prioritize local content
            const combined = [...localContent, ...globalContent];
            const unique = this.removeDuplicateVideos(combined);
            
            return unique.slice(0, 100);
        } else {
            // For non-African countries, show global content
            return this.getTrendingMusic('global', 100);
        }
    }

    // Process raw video data
    processVideoData(videos) {
        return videos.map(video => ({
            id: video.id,
            title: video.snippet?.title || 'Unknown Title',
            description: video.snippet?.description || '',
            channelTitle: video.snippet?.channelTitle || 'Unknown Channel',
            channelId: video.snippet?.channelId || '',
            publishedAt: video.snippet?.publishedAt || '',
            thumbnail: {
                default: video.snippet?.thumbnails?.default?.url || '',
                medium: video.snippet?.thumbnails?.medium?.url || '',
                high: video.snippet?.thumbnails?.high?.url || '',
                standard: video.snippet?.thumbnails?.standard?.url || '',
                maxres: video.snippet?.thumbnails?.maxres?.url || ''
            },
            statistics: {
                viewCount: parseInt(video.statistics?.viewCount || 0),
                likeCount: parseInt(video.statistics?.likeCount || 0),
                commentCount: parseInt(video.statistics?.commentCount || 0)
            },
            duration: video.contentDetails?.duration || '',
            categoryId: video.snippet?.categoryId || '',
            tags: video.snippet?.tags || [],
            isAfrican: this.isAfricanContent(video),
            isZimbabwean: this.isZimbabweanContent(video),
            url: `https://www.youtube.com/watch?v=${video.id}`,
            embedUrl: `https://www.youtube.com/embed/${video.id}`
        }));
    }

    // Check if content is African
    isAfricanContent(video) {
        const title = (video.snippet?.title || '').toLowerCase();
        const description = (video.snippet?.description || '').toLowerCase();
        const channelTitle = (video.snippet?.channelTitle || '').toLowerCase();
        
        const africanKeywords = [
            'africa', 'african', 'afrobeats', 'amapiano', 'zimbabwe', 'south africa', 'nigeria', 'kenya', 'ghana',
            'zimbabwean', 'nigerian', 'kenyan', 'ghanaian', 'south african', 'zimdancehall', 'chimurenga', 'sungura',
            'jit', 'kwaito', 'bongo', 'highlife', 'fuji', 'juju', 'mbalax', 'soukous', 'rumba', 'kizomba'
        ];
        
        const text = `${title} ${description} ${channelTitle}`;
        return africanKeywords.some(keyword => text.includes(keyword));
    }

    // Check if content is Zimbabwean
    isZimbabweanContent(video) {
        const title = (video.snippet?.title || '').toLowerCase();
        const description = (video.snippet?.description || '').toLowerCase();
        const channelTitle = (video.snippet?.channelTitle || '').toLowerCase();
        
        const zimbabweKeywords = [
            'zimbabwe', 'zimbabwean', 'zim', 'zimdancehall', 'chimurenga', 'sungura', 'jit', 'harare', 'bulawayo',
            'oliver mtukudzi', 'thomas mapfumo', 'winky d', 'jah prayzah', 'sandra ndebele', 'amara brown',
            'tocky vibes', 'freeman', 'sean timba', 'gemma griffiths', 'tammy moyo'
        ];
        
        const text = `${title} ${description} ${channelTitle}`;
        return zimbabweKeywords.some(keyword => text.includes(keyword));
    }

    // Remove duplicate videos
    removeDuplicateVideos(videos) {
        const seen = new Set();
        return videos.filter(video => {
            if (seen.has(video.id)) {
                return false;
            }
            seen.add(video.id);
            return true;
        });
    }

    // Format view count
    formatViewCount(count) {
        if (count >= 1000000000) {
            return (count / 1000000000).toFixed(1) + 'B';
        } else if (count >= 1000000) {
            return (count / 1000000).toFixed(1) + 'M';
        } else if (count >= 1000) {
            return (count / 1000).toFixed(1) + 'K';
        }
        return count.toString();
    }

    // Format duration
    formatDuration(duration) {
        const match = duration.match(/PT(?:(\d+)H)?(?:(\d+)M)?(?:(\d+)S)?/);
        if (!match) return '0:00';
        
        const hours = parseInt(match[1] || 0);
        const minutes = parseInt(match[2] || 0);
        const seconds = parseInt(match[3] || 0);
        
        if (hours > 0) {
            return `${hours}:${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
        } else {
            return `${minutes}:${seconds.toString().padStart(2, '0')}`;
        }
    }

    // Fetch with retry logic
    async fetchWithRetry(url, options = {}, attempt = 1) {
        try {
            const defaultOptions = {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                mode: 'cors'
            };

            const fetchOptions = { ...defaultOptions, ...options };

            const response = await fetch(url, fetchOptions);

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            return response;
        } catch (error) {
            if (attempt < this.retryAttempts) {
                console.warn(`YouTube API attempt ${attempt} failed, retrying in ${this.retryDelay}ms...`);
                await this.delay(this.retryDelay * attempt);
                return this.fetchWithRetry(url, options, attempt + 1);
            }
            throw error;
        }
    }

    // Delay utility
    delay(ms) {
        return new Promise(resolve => setTimeout(resolve, ms));
    }

    // Cache management
    getCachedData(key) {
        const cached = this.cache.get(key);
        if (cached && Date.now() - cached.timestamp < this.cacheTimeout) {
            return cached.data;
        }
        this.cache.delete(key);
        return null;
    }

    setCachedData(key, data) {
        this.cache.set(key, {
            data: data,
            timestamp: Date.now()
        });
    }

    // Clear cache
    clearCache() {
        this.cache.clear();
    }

    // Get cache stats
    getCacheStats() {
        return {
            size: this.cache.size,
            keys: Array.from(this.cache.keys())
        };
    }
}

// Create global instance
window.youtubeApi = new YouTubeApiService();

// Export for use in other files
window.YouTubeApiService = YouTubeApiService;
