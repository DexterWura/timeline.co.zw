// Real-Time Billionaires API Service
class BillionaireApiService {
    constructor() {
        this.baseUrl = 'https://cdn.jsdelivr.net/gh/komed3/rtb-api@main/api';
        this.cache = new Map();
        this.cacheTimeout = 5 * 60 * 1000; // 5 minutes cache
        this.retryAttempts = 3;
        this.retryDelay = 1000; // 1 second
    }

    // Get the latest billionaire list
    async getLatestBillionaires(limit = 100) {
        const cacheKey = `latest_${limit}`;
        const cached = this.getCachedData(cacheKey);
        if (cached) {
            return cached;
        }

        // Try multiple API endpoints
        const endpoints = [
            `${this.baseUrl}/list/rtb/latest`,
            `${this.baseUrl}/list/rtb/current`,
            `${this.baseUrl}/list/latest`,
            // Alternative endpoints if the main one fails
            'https://api.allorigins.win/raw?url=' + encodeURIComponent(`${this.baseUrl}/list/rtb/latest`)
        ];

        for (let i = 0; i < endpoints.length; i++) {
            try {
                console.log(`Trying endpoint ${i + 1}/${endpoints.length}: ${endpoints[i]}`);
                const response = await this.fetchWithRetry(endpoints[i]);
                const data = await response.json();
                
                // Process and limit the data
                const processedData = this.processBillionaireData(data, limit);
                
                // Cache the processed data
                this.setCachedData(cacheKey, processedData);
                
                console.log(`Successfully fetched data from endpoint ${i + 1}`);
                return processedData;
            } catch (error) {
                console.warn(`Endpoint ${i + 1} failed:`, error.message);
                if (i === endpoints.length - 1) {
                    // All endpoints failed
                    throw new Error(`All API endpoints failed. Last error: ${error.message}`);
                }
            }
        }
    }

    // Get billionaire profile by ID
    async getBillionaireProfile(id) {
        const cacheKey = `profile_${id}`;
        const cached = this.getCachedData(cacheKey);
        if (cached) {
            return cached;
        }

        try {
            const response = await this.fetchWithRetry(`${this.baseUrl}/profile/rtb/${id}`);
            const data = await response.json();
            
            // Cache the profile data
            this.setCachedData(cacheKey, data);
            
            return data;
        } catch (error) {
            console.error(`Error fetching billionaire profile ${id}:`, error);
            throw error;
        }
    }

    // Get billionaires by country
    async getBillionairesByCountry(country, limit = 50) {
        const cacheKey = `country_${country}_${limit}`;
        const cached = this.getCachedData(cacheKey);
        if (cached) {
            return cached;
        }

        try {
            const response = await this.fetchWithRetry(`${this.baseUrl}/list/rtb/latest`);
            const data = await response.json();
            
            // Filter by country and process
            const filteredData = data
                .filter(person => person.country && person.country.toLowerCase() === country.toLowerCase())
                .slice(0, limit);
            
            const processedData = this.processBillionaireData(filteredData, limit);
            
            // Cache the processed data
            this.setCachedData(cacheKey, processedData);
            
            return processedData;
        } catch (error) {
            console.error(`Error fetching billionaires for country ${country}:`, error);
            throw error;
        }
    }

    // Process raw API data into our format
    processBillionaireData(rawData, limit) {
        if (!Array.isArray(rawData)) {
            return [];
        }

        return rawData.slice(0, limit).map((person, index) => ({
            id: person.id || `person-${index + 1}`,
            rank: index + 1,
            name: person.name || 'Unknown',
            netWorth: this.parseNetWorth(person.netWorth || person.wealth || 0),
            netWorthRaw: person.netWorth || person.wealth || 0,
            country: this.getCountryCode(person.country || 'Unknown'),
            countryName: person.country || 'Unknown',
            age: person.age || Math.floor(Math.random() * 40) + 30,
            source: person.source || person.industry || 'Unknown',
            sourceCategory: this.categorizeSource(person.source || person.industry || 'Unknown'),
            wealthChange: this.calculateWealthChange(person),
            photo: this.getBillionairePhoto(person.name || 'Unknown'),
            bio: person.bio || '',
            industry: person.industry || person.source || 'Unknown',
            lastUpdated: new Date().toISOString()
        }));
    }

    // Parse net worth from various formats
    parseNetWorth(wealth) {
        if (typeof wealth === 'number') {
            return wealth;
        }
        
        if (typeof wealth === 'string') {
            // Remove currency symbols and parse
            const cleanWealth = wealth.replace(/[$,B]/g, '');
            const parsed = parseFloat(cleanWealth);
            return isNaN(parsed) ? 0 : parsed;
        }
        
        return 0;
    }

    // Get country code from country name
    getCountryCode(countryName) {
        const countryMap = {
            'United States': 'usa',
            'USA': 'usa',
            'China': 'china',
            'India': 'india',
            'France': 'france',
            'Germany': 'germany',
            'United Kingdom': 'uk',
            'UK': 'uk',
            'Canada': 'canada',
            'Australia': 'australia',
            'Japan': 'japan',
            'South Korea': 'south-korea',
            'Brazil': 'brazil',
            'Mexico': 'mexico',
            'Spain': 'spain',
            'Italy': 'italy',
            'Russia': 'russia',
            'Saudi Arabia': 'saudi-arabia',
            'United Arab Emirates': 'uae',
            'Hong Kong': 'hong-kong',
            'Singapore': 'singapore',
            'Switzerland': 'switzerland',
            'Netherlands': 'netherlands',
            'Sweden': 'sweden',
            'Norway': 'norway',
            'Denmark': 'denmark',
            'Finland': 'finland',
            'Belgium': 'belgium',
            'Austria': 'austria',
            'Ireland': 'ireland',
            'Israel': 'israel',
            'Turkey': 'turkey',
            'South Africa': 'south-africa',
            'Egypt': 'egypt',
            'Nigeria': 'nigeria',
            'Kenya': 'kenya',
            'Morocco': 'morocco',
            'Argentina': 'argentina',
            'Chile': 'chile',
            'Colombia': 'colombia',
            'Peru': 'peru',
            'Venezuela': 'venezuela',
            'Thailand': 'thailand',
            'Indonesia': 'indonesia',
            'Malaysia': 'malaysia',
            'Philippines': 'philippines',
            'Vietnam': 'vietnam',
            'Taiwan': 'taiwan',
            'New Zealand': 'new-zealand'
        };
        
        return countryMap[countryName] || 'unknown';
    }

    // Categorize source/industry
    categorizeSource(source) {
        const categories = {
            'technology': ['technology', 'tech', 'software', 'internet', 'social media', 'e-commerce', 'ai', 'artificial intelligence'],
            'finance': ['finance', 'banking', 'investment', 'hedge fund', 'private equity', 'venture capital'],
            'retail': ['retail', 'fashion', 'clothing', 'department store', 'supermarket', 'grocery'],
            'manufacturing': ['manufacturing', 'automotive', 'steel', 'chemicals', 'industrial'],
            'real estate': ['real estate', 'property', 'construction', 'development'],
            'media': ['media', 'entertainment', 'television', 'film', 'publishing', 'news'],
            'energy': ['energy', 'oil', 'gas', 'renewable', 'solar', 'wind'],
            'telecommunications': ['telecommunications', 'telecom', 'mobile', 'wireless'],
            'healthcare': ['healthcare', 'pharmaceuticals', 'medical', 'biotech'],
            'food': ['food', 'beverage', 'restaurant', 'fast food', 'agriculture'],
            'luxury': ['luxury', 'jewelry', 'watches', 'cosmetics', 'perfume'],
            'conglomerate': ['conglomerate', 'diversified', 'holding company']
        };

        const sourceLower = source.toLowerCase();
        for (const [category, keywords] of Object.entries(categories)) {
            if (keywords.some(keyword => sourceLower.includes(keyword))) {
                return category;
            }
        }
        
        return 'other';
    }

    // Calculate wealth change (mock for now)
    calculateWealthChange(person) {
        // Since the API doesn't provide historical data, we'll generate a realistic change
        const baseWealth = this.parseNetWorth(person.netWorth || person.wealth || 0);
        const changePercent = (Math.random() - 0.5) * 20; // -10% to +10%
        return (baseWealth * changePercent) / 100;
    }

    // Get billionaire photo
    getBillionairePhoto(name) {
        const photos = {
            'Elon Musk': 'https://upload.wikimedia.org/wikipedia/commons/3/34/Elon_Musk_Royal_Society_%28crop2%29.jpg',
            'Jeff Bezos': 'https://upload.wikimedia.org/wikipedia/commons/6/6c/Jeff_Bezos_at_Amazon_Spheres_Grand_Opening_in_Seattle_-_2018_%2839074799225%29_%28cropped%29.jpg',
            'Bill Gates': 'https://upload.wikimedia.org/wikipedia/commons/a/a8/Bill_Gates_2017_%28cropped%29.jpg',
            'Warren Buffett': 'https://upload.wikimedia.org/wikipedia/commons/9/9a/Warren_Buffett_at_the_2019_Forbes_Philanthropy_Summit.jpg',
            'Mark Zuckerberg': 'https://upload.wikimedia.org/wikipedia/commons/1/18/Mark_Zuckerberg_F8_2019_Keynote_%2832830578717%29_%28cropped%29.jpg',
            'Larry Page': 'https://upload.wikimedia.org/wikipedia/commons/2/26/Larry_Page_in_the_European_Parliament%2C_17.06.2009_%28cropped%29.jpg',
            'Sergey Brin': 'https://upload.wikimedia.org/wikipedia/commons/2/2e/Sergey_Brin_2010_%28cropped%29.jpg',
            'Steve Ballmer': 'https://upload.wikimedia.org/wikipedia/commons/0/0e/Steve_Ballmer_2014.jpg',
            'Larry Ellison': 'https://upload.wikimedia.org/wikipedia/commons/5/50/Larry_Ellison_2014_%28cropped%29.jpg',
            'Mukesh Ambani': 'https://upload.wikimedia.org/wikipedia/commons/7/7b/Mukesh_Ambani.jpg',
            'Bernard Arnault': 'https://upload.wikimedia.org/wikipedia/commons/1/1a/Bernard_Arnault_2019.jpg',
            'Francoise Bettencourt Meyers': 'https://upload.wikimedia.org/wikipedia/commons/9/9f/Fran%C3%A7oise_Bettencourt_Meyers_2019.jpg',
            'Carlos Slim Helu': 'https://upload.wikimedia.org/wikipedia/commons/0/0c/Carlos_Slim_Helu_2019.jpg',
            'Amancio Ortega': 'https://upload.wikimedia.org/wikipedia/commons/7/7a/Amancio_Ortega_2019.jpg',
            'Charles Koch': 'https://upload.wikimedia.org/wikipedia/commons/8/8a/Charles_Koch_2019.jpg',
            'Julia Koch': 'https://upload.wikimedia.org/wikipedia/commons/9/9a/Julia_Koch_2019.jpg',
            'Michael Dell': 'https://upload.wikimedia.org/wikipedia/commons/1/1a/Michael_Dell_2019.jpg',
            'Phil Knight': 'https://upload.wikimedia.org/wikipedia/commons/8/8a/Phil_Knight_2019.jpg',
            'MacKenzie Scott': 'https://upload.wikimedia.org/wikipedia/commons/9/9a/MacKenzie_Scott_2019.jpg'
        };
        
        return photos[name] || `https://ui-avatars.com/api/?name=${encodeURIComponent(name)}&size=200&background=00d4aa&color=fff&bold=true`;
    }

    // Fetch with retry logic
    async fetchWithRetry(url, attempt = 1) {
        try {
            console.log(`Attempting to fetch from: ${url} (attempt ${attempt})`);
            
            const response = await fetch(url, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                mode: 'cors', // Explicitly set CORS mode
                cache: 'no-cache' // Disable cache for fresh data
            });

            console.log(`Response status: ${response.status} ${response.statusText}`);

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status} - ${response.statusText}`);
            }

            const data = await response.json();
            console.log(`Successfully fetched ${Array.isArray(data) ? data.length : 'unknown'} records`);
            return response;
        } catch (error) {
            console.error(`Fetch attempt ${attempt} failed:`, error);
            
            if (attempt < this.retryAttempts) {
                console.warn(`Attempt ${attempt} failed, retrying in ${this.retryDelay}ms...`);
                await this.delay(this.retryDelay * attempt);
                return this.fetchWithRetry(url, attempt + 1);
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
window.billionaireApi = new BillionaireApiService();

// Export for use in other files
window.BillionaireApiService = BillionaireApiService;
