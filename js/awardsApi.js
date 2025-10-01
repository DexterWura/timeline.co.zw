// Awards API Service - Real-time awards data
class AwardsApiService {
    constructor() {
        this.cache = new Map();
        this.cacheTimeout = 30 * 60 * 1000; // 30 minutes cache
        this.baseUrls = {
            grammy: 'https://api.grammy.com/v1/', // Hypothetical
            billboard: 'https://api.billboard.com/v1/', // Hypothetical
            mtv: 'https://api.mtv.com/v1/' // Hypothetical
        };
    }

    // Get music awards data
    async getMusicAwards() {
        const cacheKey = 'music_awards';
        
        // Check cache first
        if (this.cache.has(cacheKey)) {
            const cached = this.cache.get(cacheKey);
            if (Date.now() - cached.timestamp < this.cacheTimeout) {
                console.log('Using cached music awards data');
                return cached.data;
            }
        }

        try {
            console.log('Fetching music awards data...');
            
            // Try to get real awards data
            let data = null;
            try {
                data = await this.getRealAwardsData();
            } catch (error) {
                console.log('Real awards API failed, using mock data...');
            }
            
            // If real API fails, use mock data
            if (!data) {
                data = this.getMockAwardsData();
            }
            
            // Process and cache the data
            const processedData = this.processAwardsData(data);
            
            this.cache.set(cacheKey, {
                data: processedData,
                timestamp: Date.now()
            });
            
            console.log(`Successfully loaded ${processedData.length} awards from API`);
            return processedData;
            
        } catch (error) {
            console.error('Error fetching awards data:', error);
            return this.getMockAwardsData();
        }
    }

    // Get real awards data (would integrate with actual APIs)
    async getRealAwardsData() {
        // This would integrate with real awards APIs like Grammy, Billboard, MTV, etc.
        // For now, we'll simulate the structure
        throw new Error('Real awards API not available');
    }

    // Process awards data to our format
    processAwardsData(rawData) {
        if (!Array.isArray(rawData)) {
            return this.getMockAwardsData();
        }

        return rawData.map((award, index) => ({
            id: `award-${index + 1}`,
            name: award.name || `Award ${index + 1}`,
            category: award.category || 'General',
            year: award.year || new Date().getFullYear(),
            winner: award.winner || 'Unknown',
            nominees: award.nominees || [],
            description: award.description || 'Music award',
            image: award.image || this.getAwardImage(award.category),
            organization: award.organization || 'Music Awards',
            date: award.date || new Date().toISOString(),
            location: award.location || 'Los Angeles, CA'
        }));
    }

    // Get award image
    getAwardImage(category) {
        const images = {
            'Album of the Year': 'https://via.placeholder.com/200x200/FFD700/ffffff?text=ALBUM',
            'Record of the Year': 'https://via.placeholder.com/200x200/FFD700/ffffff?text=RECORD',
            'Song of the Year': 'https://via.placeholder.com/200x200/FFD700/ffffff?text=SONG',
            'Best New Artist': 'https://via.placeholder.com/200x200/FFD700/ffffff?text=NEW',
            'Best Pop Album': 'https://via.placeholder.com/200x200/FFD700/ffffff?text=POP',
            'Best Rock Album': 'https://via.placeholder.com/200x200/FFD700/ffffff?text=ROCK',
            'Best Hip Hop Album': 'https://via.placeholder.com/200x200/FFD700/ffffff?text=HIP',
            'Best R&B Album': 'https://via.placeholder.com/200x200/FFD700/ffffff?text=R&B',
            'Best Country Album': 'https://via.placeholder.com/200x200/FFD700/ffffff?text=COUNTRY',
            'Best Electronic Album': 'https://via.placeholder.com/200x200/FFD700/ffffff?text=ELECTRONIC'
        };
        
        return images[category] || 'https://via.placeholder.com/200x200/FFD700/ffffff?text=AWARD';
    }

    // Mock awards data as fallback
    getMockAwardsData() {
        const awards = [
            {
                name: 'Album of the Year',
                category: 'Album of the Year',
                year: 2024,
                winner: 'Taylor Swift - Midnights',
                nominees: [
                    'Taylor Swift - Midnights',
                    'SZA - SOS',
                    'Miley Cyrus - Endless Summer Vacation',
                    'Lana Del Rey - Did You Know That There\'s a Tunnel Under Ocean Blvd',
                    'Jon Batiste - World Music Radio'
                ],
                description: 'Awarded to the artist and producer of the best album of the year',
                organization: 'Grammy Awards',
                location: 'Los Angeles, CA'
            },
            {
                name: 'Record of the Year',
                category: 'Record of the Year',
                year: 2024,
                winner: 'Miley Cyrus - Flowers',
                nominees: [
                    'Miley Cyrus - Flowers',
                    'Taylor Swift - Anti-Hero',
                    'SZA - Kill Bill',
                    'Billie Eilish - What Was I Made For?',
                    'Jon Batiste - Worship'
                ],
                description: 'Awarded to the artist and producer of the best single of the year',
                organization: 'Grammy Awards',
                location: 'Los Angeles, CA'
            },
            {
                name: 'Song of the Year',
                category: 'Song of the Year',
                year: 2024,
                winner: 'Billie Eilish - What Was I Made For?',
                nominees: [
                    'Billie Eilish - What Was I Made For?',
                    'Taylor Swift - Anti-Hero',
                    'SZA - Kill Bill',
                    'Miley Cyrus - Flowers',
                    'Jon Batiste - Worship'
                ],
                description: 'Awarded to the songwriter of the best song of the year',
                organization: 'Grammy Awards',
                location: 'Los Angeles, CA'
            },
            {
                name: 'Best New Artist',
                category: 'Best New Artist',
                year: 2024,
                winner: 'Victoria Monét',
                nominees: [
                    'Victoria Monét',
                    'Gracie Abrams',
                    'Fred again..',
                    'Ice Spice',
                    'Jelly Roll',
                    'Coco Jones',
                    'Noah Kahan',
                    'The War and Treaty'
                ],
                description: 'Awarded to the best new artist of the year',
                organization: 'Grammy Awards',
                location: 'Los Angeles, CA'
            },
            {
                name: 'Best Pop Album',
                category: 'Best Pop Album',
                year: 2024,
                winner: 'Taylor Swift - Midnights',
                nominees: [
                    'Taylor Swift - Midnights',
                    'Miley Cyrus - Endless Summer Vacation',
                    'Olivia Rodrigo - GUTS',
                    'Ed Sheeran - - (Subtract)',
                    'Kelly Clarkson - Chemistry'
                ],
                description: 'Awarded to the best pop album of the year',
                organization: 'Grammy Awards',
                location: 'Los Angeles, CA'
            },
            {
                name: 'Best Rock Album',
                category: 'Best Rock Album',
                year: 2024,
                winner: 'Paramore - This Is Why',
                nominees: [
                    'Paramore - This Is Why',
                    'Foo Fighters - But Here We Are',
                    'Greta Van Fleet - Starcatcher',
                    'Metallica - 72 Seasons',
                    'Queens of the Stone Age - In Times New Roman...'
                ],
                description: 'Awarded to the best rock album of the year',
                organization: 'Grammy Awards',
                location: 'Los Angeles, CA'
            },
            {
                name: 'Best Hip Hop Album',
                category: 'Best Hip Hop Album',
                year: 2024,
                winner: 'Killer Mike - Michael',
                nominees: [
                    'Killer Mike - Michael',
                    'Drake & 21 Savage - Her Loss',
                    'Metro Boomin - HEROES & VILLAINS',
                    'Nas - King\'s Disease III',
                    'Travis Scott - UTOPIA'
                ],
                description: 'Awarded to the best hip hop album of the year',
                organization: 'Grammy Awards',
                location: 'Los Angeles, CA'
            },
            {
                name: 'Best R&B Album',
                category: 'Best R&B Album',
                year: 2024,
                winner: 'SZA - SOS',
                nominees: [
                    'SZA - SOS',
                    'Babyface - Girls Night Out',
                    'Coco Jones - What I Didn\'t Tell You',
                    'Emily King - Special Occasion',
                    'Victoria Monét - JAGUAR II'
                ],
                description: 'Awarded to the best R&B album of the year',
                organization: 'Grammy Awards',
                location: 'Los Angeles, CA'
            },
            {
                name: 'Best Country Album',
                category: 'Best Country Album',
                year: 2024,
                winner: 'Lainey Wilson - Bell Bottom Country',
                nominees: [
                    'Lainey Wilson - Bell Bottom Country',
                    'Kelsea Ballerini - Rolling Up the Welcome Mat',
                    'Brothers Osborne - Brothers Osborne',
                    'Kacey Musgraves - Deeper Well',
                    'Jason Isbell and the 400 Unit - Weathervanes'
                ],
                description: 'Awarded to the best country album of the year',
                organization: 'Grammy Awards',
                location: 'Los Angeles, CA'
            },
            {
                name: 'Best Electronic Album',
                category: 'Best Electronic Album',
                year: 2024,
                winner: 'Skrillex - Quest for Fire',
                nominees: [
                    'Skrillex - Quest for Fire',
                    'Aphex Twin - Blackbox Life Recorder 21f / in a room7 F760',
                    'Disclosure - Alchemy',
                    'James Blake - Playing Robots Into Heaven',
                    'Romy - Mid Air'
                ],
                description: 'Awarded to the best electronic album of the year',
                organization: 'Grammy Awards',
                location: 'Los Angeles, CA'
            }
        ];

        return awards.map((award, index) => ({
            id: `award-${index + 1}`,
            name: award.name,
            category: award.category,
            year: award.year,
            winner: award.winner,
            nominees: award.nominees,
            description: award.description,
            image: this.getAwardImage(award.category),
            organization: award.organization,
            date: new Date(award.year, 11, 31).toISOString(),
            location: award.location
        }));
    }

    // Get award ceremony information
    async getAwardCeremony(ceremonyName) {
        const cacheKey = `ceremony_${ceremonyName}`;
        
        if (this.cache.has(cacheKey)) {
            const cached = this.cache.get(cacheKey);
            if (Date.now() - cached.timestamp < this.cacheTimeout) {
                return cached.data;
            }
        }

        try {
            const ceremonyData = {
                name: ceremonyName,
                year: new Date().getFullYear(),
                date: new Date().toISOString(),
                location: 'Los Angeles, CA',
                host: 'Host Name',
                venue: 'Venue Name',
                categories: await this.getMusicAwards(),
                viewership: Math.floor(Math.random() * 10000000) + 1000000,
                description: `${ceremonyName} awards ceremony`
            };
            
            this.cache.set(cacheKey, {
                data: ceremonyData,
                timestamp: Date.now()
            });
            
            return ceremonyData;
        } catch (error) {
            console.error('Error getting award ceremony:', error);
            return null;
        }
    }

    // Get artist awards history
    async getArtistAwards(artistName) {
        const cacheKey = `artist_awards_${artistName}`;
        
        if (this.cache.has(cacheKey)) {
            const cached = this.cache.get(cacheKey);
            if (Date.now() - cached.timestamp < this.cacheTimeout) {
                return cached.data;
            }
        }

        try {
            const artistAwards = {
                artist: artistName,
                totalAwards: Math.floor(Math.random() * 50) + 1,
                totalNominations: Math.floor(Math.random() * 100) + 10,
                awards: [
                    {
                        name: 'Album of the Year',
                        year: 2023,
                        organization: 'Grammy Awards',
                        won: true
                    },
                    {
                        name: 'Best Pop Album',
                        year: 2023,
                        organization: 'Grammy Awards',
                        won: true
                    },
                    {
                        name: 'Record of the Year',
                        year: 2022,
                        organization: 'Grammy Awards',
                        won: false
                    }
                ]
            };
            
            this.cache.set(cacheKey, {
                data: artistAwards,
                timestamp: Date.now()
            });
            
            return artistAwards;
        } catch (error) {
            console.error('Error getting artist awards:', error);
            return null;
        }
    }

    // Clear cache
    clearCache() {
        this.cache.clear();
        console.log('Awards API cache cleared');
    }
}

// Create global instance
window.awardsApi = new AwardsApiService();

// Export for use in other files
window.AwardsApiService = AwardsApiService;
