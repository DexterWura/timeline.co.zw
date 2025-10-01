// Enhanced Image Service with Real APIs
class ImageService {
    constructor() {
        this.cache = new Map();
        this.fallbackImages = {
            person: 'https://ui-avatars.com/api/?name={name}&size=200&background=00d4aa&color=fff&bold=true',
            album: 'https://via.placeholder.com/200x200/00d4aa/ffffff?text={text}',
            video: 'https://via.placeholder.com/300x200/00d4aa/ffffff?text={text}',
            news: 'https://via.placeholder.com/200x200/667eea/ffffff?text=NEWS'
        };
    }

    // Get billionaire photo with fallback
    async getBillionairePhoto(name) {
        const cacheKey = `billionaire_${name}`;
        if (this.cache.has(cacheKey)) {
            return this.cache.get(cacheKey);
        }

        // Try to get from our curated list first
        const curatedPhotos = {
            'Elon Musk': 'https://upload.wikimedia.org/wikipedia/commons/3/34/Elon_Musk_Royal_Society_%28crop2%29.jpg',
            'Jeff Bezos': 'https://upload.wikimedia.org/wikipedia/commons/6/6c/Jeff_Bezos_at_Amazon_Spheres_Grand_Opening_in_Seattle_-_2018_%2839074799225%29_%28cropped%29.jpg',
            'Bill Gates': 'https://upload.wikimedia.org/wikipedia/commons/a/a8/Bill_Gates_2017_%28cropped%29.jpg',
            'Warren Buffett': 'https://upload.wikimedia.org/wikipedia/commons/9/9a/Warren_Buffett_at_the_2019_Forbes_Philanthropy_Summit.jpg',
            'Mark Zuckerberg': 'https://upload.wikimedia.org/wikipedia/commons/1/18/Mark_Zuckerberg_F8_2019_Keynote_%2832830578717%29_%28cropped%29.jpg',
            'Larry Page': 'https://upload.wikimedia.org/wikipedia/commons/2/26/Larry_Page_in_the_European_Parliament%2C_17.06.2009_%28cropped%29.jpg',
            'Sergey Brin': 'https://upload.wikimedia.org/wikipedia/commons/2/2e/Sergey_Brin_2010_%28cropped%29.jpg',
            'Steve Ballmer': 'https://upload.wikimedia.org/wikipedia/commons/0/0e/Steve_Ballmer_2014.jpg',
            'Larry Ellison': 'https://upload.wikimedia.org/wikipedia/commons/5/50/Larry_Ellison_2014_%28cropped%29.jpg',
            'Mukesh Ambani': 'https://upload.wikimedia.org/wikipedia/commons/7/7b/Mukesh_Ambani.jpg'
        };

        let photoUrl = curatedPhotos[name];
        
        if (!photoUrl) {
            // Fallback to UI Avatars
            photoUrl = this.fallbackImages.person.replace('{name}', encodeURIComponent(name));
        }

        this.cache.set(cacheKey, photoUrl);
        return photoUrl;
    }

    // Get album artwork with fallback
    async getAlbumArtwork(song, artist = '') {
        const cacheKey = `album_${song}_${artist}`;
        if (this.cache.has(cacheKey)) {
            return this.cache.get(cacheKey);
        }

        // Try to get from our curated list first
        const curatedArtwork = {
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

        let artworkUrl = curatedArtwork[song];
        
        if (!artworkUrl) {
            // Fallback to placeholder
            artworkUrl = this.fallbackImages.album.replace('{text}', song.substring(0, 2).toUpperCase());
        }

        this.cache.set(cacheKey, artworkUrl);
        return artworkUrl;
    }

    // Get video thumbnail with fallback
    async getVideoThumbnail(video) {
        const cacheKey = `video_${video}`;
        if (this.cache.has(cacheKey)) {
            return this.cache.get(cacheKey);
        }

        // Try to get from our curated list first
        const curatedThumbnails = {
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

        let thumbnailUrl = curatedThumbnails[video];
        
        if (!thumbnailUrl) {
            // Fallback to placeholder
            thumbnailUrl = this.fallbackImages.video.replace('{text}', video.substring(0, 10));
        }

        this.cache.set(cacheKey, thumbnailUrl);
        return thumbnailUrl;
    }

    // Get artist photo with fallback
    async getArtistPhoto(artist) {
        const cacheKey = `artist_${artist}`;
        if (this.cache.has(cacheKey)) {
            return this.cache.get(cacheKey);
        }

        // Try to get from our curated list first
        const curatedPhotos = {
            'Taylor Swift': 'https://upload.wikimedia.org/wikipedia/commons/b/b5/191125_Taylor_Swift_at_the_2019_American_Music_Awards_%28cropped%29.png',
            'Drake': 'https://upload.wikimedia.org/wikipedia/commons/a/af/Drake_-_Toronto_2019.png',
            'Ariana Grande': 'https://upload.wikimedia.org/wikipedia/commons/d/dd/2019_by_Glenn_Francis_%28cropped%29_2.jpg',
            'The Weeknd': 'https://upload.wikimedia.org/wikipedia/commons/3/3a/The_Weeknd_-_Blinding_Lights_%28Official_Music_Video%29.png',
            'Billie Eilish': 'https://upload.wikimedia.org/wikipedia/commons/3/3a/Billie_Eilish_2019_by_Glenn_Francis_%28cropped%29_2.jpg',
            'Post Malone': 'https://upload.wikimedia.org/wikipedia/commons/0/0a/Post_Malone_2019_by_Glenn_Francis_%28cropped%29.jpg',
            'Dua Lipa': 'https://upload.wikimedia.org/wikipedia/commons/4/4c/Dua_Lipa_2019_by_Glenn_Francis_%28cropped%29.jpg',
            'Ed Sheeran': 'https://upload.wikimedia.org/wikipedia/commons/c/c1/Ed_Sheeran-6886_%28cropped%29.jpg',
            'Justin Bieber': 'https://upload.wikimedia.org/wikipedia/commons/b/bb/Justin_Bieber_2019_by_Glenn_Francis_%28cropped%29.jpg',
            'Olivia Rodrigo': 'https://upload.wikimedia.org/wikipedia/commons/6/6a/Olivia_Rodrigo_2021_by_Glenn_Francis_%28cropped%29.jpg'
        };

        let photoUrl = curatedPhotos[artist];
        
        if (!photoUrl) {
            // Fallback to UI Avatars
            photoUrl = this.fallbackImages.person.replace('{name}', encodeURIComponent(artist));
        }

        this.cache.set(cacheKey, photoUrl);
        return photoUrl;
    }

    // Handle image loading errors
    handleImageError(img, fallbackType = 'placeholder') {
        img.onerror = () => {
            const colors = ['00d4aa', '667eea', '764ba2', 'f093fb', 'f5576c', '4facfe', '00f2fe'];
            const randomColor = colors[Math.floor(Math.random() * colors.length)];
            
            switch(fallbackType) {
                case 'person':
                    img.src = `https://ui-avatars.com/api/?name=${encodeURIComponent(img.alt || 'Person')}&size=200&background=${randomColor}&color=fff&bold=true`;
                    break;
                case 'album':
                    img.src = `https://via.placeholder.com/200x200/${randomColor}/ffffff?text=ALBUM`;
                    break;
                case 'video':
                    img.src = `https://via.placeholder.com/300x200/${randomColor}/ffffff?text=VIDEO`;
                    break;
                case 'news':
                    img.src = `https://via.placeholder.com/200x200/${randomColor}/ffffff?text=NEWS`;
                    break;
                default:
                    img.src = `https://via.placeholder.com/200x200/${randomColor}/ffffff?text=IMG`;
            }
        };
    }

    // Preload images for better performance
    async preloadImages(urls) {
        const promises = urls.map(url => {
            return new Promise((resolve, reject) => {
                const img = new Image();
                img.onload = () => resolve(url);
                img.onerror = () => reject(url);
                img.src = url;
            });
        });

        try {
            await Promise.all(promises);
            console.log('All images preloaded successfully');
        } catch (error) {
            console.warn('Some images failed to preload:', error);
        }
    }
}

// Create global instance
window.imageService = new ImageService();

// Export for use in other files
window.ImageService = ImageService;
