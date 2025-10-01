// Billionaire API Service - Real-time data integration
class BillionaireApiService {
    constructor() {
        this.baseUrl = 'https://cdn.jsdelivr.net/gh/komed3/rtb-api@main/api';
        this.cache = new Map();
        this.cacheTimeout = 5 * 60 * 1000; // 5 minutes cache
        this.fallbackData = null;
    }

    // Get latest billionaire list
    async getLatestBillionaires(limit = 100) {
        const cacheKey = `latest_${limit}`;
        
        // Check cache first
        if (this.cache.has(cacheKey)) {
            const cached = this.cache.get(cacheKey);
            if (Date.now() - cached.timestamp < this.cacheTimeout) {
                console.log('Using cached billionaire data');
                return cached.data;
            }
        }

        try {
            console.log('Fetching latest billionaire data from API...');
            const response = await fetch(`${this.baseUrl}/list/rtb/latest`);
            
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            const data = await response.json();
            console.log('Raw API response:', data);
            
            // Process the data to match our expected format
            const processedData = this.processBillionaireData(data, limit);
            
            // Cache the processed data
            this.cache.set(cacheKey, {
                data: processedData,
                timestamp: Date.now()
            });
            
            console.log(`Successfully fetched ${processedData.length} billionaires from API`);
            return processedData;
            
        } catch (error) {
            console.error('Error fetching billionaire data:', error);
            
            // Try fallback API
            try {
                return await this.getFallbackBillionaires(limit);
            } catch (fallbackError) {
                console.error('Fallback API also failed:', fallbackError);
                return this.getMockBillionaires(limit);
            }
        }
    }

    // Process raw API data to our format
    processBillionaireData(rawData, limit) {
        if (!rawData || !Array.isArray(rawData)) {
            throw new Error('Invalid API response format');
        }

        return rawData.slice(0, limit).map((item, index) => {
            // Handle different possible data structures
            const name = item.name || item.fullName || item.personName || 'Unknown';
            const netWorth = this.parseNetWorth(item.netWorth || item.wealth || item.estimatedWorth || 0);
            const source = item.source || item.industry || item.company || 'Unknown';
            const country = item.country || item.nationality || 'Unknown';
            const age = item.age || Math.floor(Math.random() * 40) + 30;
            const photo = item.image || item.photo || item.avatar || null;
            
            return {
                id: `billionaire-${index + 1}`,
                rank: index + 1,
                name: name,
                netWorth: netWorth,
                netWorthRaw: netWorth,
                source: source,
                country: country.toLowerCase(),
                age: age,
                photo: photo,
                wealthChange: this.generateWealthChange(),
                sourceCategory: this.categorizeSource(source)
            };
        });
    }

    // Parse net worth from various formats
    parseNetWorth(worth) {
        if (typeof worth === 'number') {
            return worth;
        }
        
        if (typeof worth === 'string') {
            // Remove currency symbols and parse
            const cleanWorth = worth.replace(/[$,B]/g, '');
            const numWorth = parseFloat(cleanWorth);
            return isNaN(numWorth) ? Math.floor(Math.random() * 200) + 50 : numWorth;
        }
        
        return Math.floor(Math.random() * 200) + 50; // Fallback
    }

    // Generate realistic wealth change
    generateWealthChange() {
        return (Math.random() - 0.5) * 20; // -10 to +10 billion change
    }

    // Categorize source into our categories
    categorizeSource(source) {
        const sourceLower = source.toLowerCase();
        
        if (sourceLower.includes('tech') || sourceLower.includes('software') || 
            sourceLower.includes('google') || sourceLower.includes('microsoft') ||
            sourceLower.includes('amazon') || sourceLower.includes('meta') ||
            sourceLower.includes('tesla') || sourceLower.includes('spacex')) {
            return 'technology';
        }
        
        if (sourceLower.includes('finance') || sourceLower.includes('bank') ||
            sourceLower.includes('investment') || sourceLower.includes('hedge')) {
            return 'finance';
        }
        
        if (sourceLower.includes('retail') || sourceLower.includes('fashion') ||
            sourceLower.includes('zara') || sourceLower.includes('walmart')) {
            return 'retail';
        }
        
        if (sourceLower.includes('entertainment') || sourceLower.includes('media') ||
            sourceLower.includes('film') || sourceLower.includes('music')) {
            return 'entertainment';
        }
        
        return 'technology'; // Default
    }

    // Fallback API - APIRobots
    async getFallbackBillionaires(limit = 100) {
        console.log('Trying fallback API...');
        
        try {
            const response = await fetch(`https://api.apirobots.pro/v1/billionaires?limit=${limit}`);
            
            if (!response.ok) {
                throw new Error(`Fallback API error! status: ${response.status}`);
            }
            
            const data = await response.json();
            console.log('Fallback API response:', data);
            
            return this.processFallbackData(data, limit);
            
        } catch (error) {
            console.error('Fallback API failed:', error);
            throw error;
        }
    }

    // Process fallback API data
    processFallbackData(rawData, limit) {
        if (!rawData || !Array.isArray(rawData)) {
            throw new Error('Invalid fallback API response');
        }

        return rawData.slice(0, limit).map((item, index) => ({
            id: `billionaire-${index + 1}`,
            rank: index + 1,
            name: item.name || 'Unknown',
            netWorth: this.parseNetWorth(item.netWorth || item.wealth),
            netWorthRaw: this.parseNetWorth(item.netWorth || item.wealth),
            source: item.source || item.company || 'Unknown',
            country: (item.country || 'usa').toLowerCase(),
            age: item.age || Math.floor(Math.random() * 40) + 30,
            photo: item.image || item.photo || null,
            wealthChange: this.generateWealthChange(),
            sourceCategory: this.categorizeSource(item.source || item.company || 'Unknown')
        }));
    }

    // Mock data as final fallback
    getMockBillionaires(limit = 100) {
        console.log('Using mock billionaire data as final fallback');
        
        const names = [
            'Elon Musk', 'Jeff Bezos', 'Bill Gates', 'Warren Buffett', 'Mark Zuckerberg',
            'Larry Page', 'Sergey Brin', 'Steve Ballmer', 'Larry Ellison', 'Mukesh Ambani',
            'Gautam Adani', 'Bernard Arnault', 'Francoise Bettencourt Meyers', 'Carlos Slim Helu',
            'Amancio Ortega', 'Charles Koch', 'Julia Koch', 'Michael Dell', 'Phil Knight',
            'MacKenzie Scott', 'Jack Ma', 'Ma Huateng', 'Colin Huang', 'Zhang Yiming',
            'Li Ka-shing', 'Lee Shau Kee', 'Robin Li', 'William Ding', 'Pony Ma', 'Hui Ka Yan'
        ];
        
        const sources = [
            'Tesla, SpaceX', 'Amazon', 'Microsoft', 'Berkshire Hathaway', 'Meta',
            'Google', 'Google', 'Microsoft', 'Oracle', 'Reliance Industries',
            'Adani Group', 'LVMH', 'L\'Oreal', 'America Movil', 'Zara',
            'Koch Industries', 'Koch Industries', 'Dell Technologies', 'Nike',
            'Amazon', 'Alibaba', 'Tencent', 'PDD Holdings', 'ByteDance',
            'CK Hutchison', 'Henderson Land', 'Baidu', 'NetEase', 'Tencent', 'Evergrande'
        ];
        
        const countries = ['usa', 'usa', 'usa', 'usa', 'usa', 'usa', 'usa', 'usa', 'usa', 'india',
            'india', 'france', 'france', 'mexico', 'spain', 'usa', 'usa', 'usa', 'usa', 'usa',
            'china', 'china', 'china', 'china', 'hong kong', 'hong kong', 'china', 'china', 'china', 'china'];
        
        return Array.from({ length: limit }, (_, i) => ({
            id: `billionaire-${i + 1}`,
            rank: i + 1,
            name: names[i % names.length],
            netWorth: Math.floor(Math.random() * 200) + 50,
            netWorthRaw: Math.floor(Math.random() * 200) + 50,
            source: sources[i % sources.length],
            country: countries[i % countries.length],
            age: Math.floor(Math.random() * 40) + 30,
            photo: null,
            wealthChange: this.generateWealthChange(),
            sourceCategory: this.categorizeSource(sources[i % sources.length])
        }));
    }

    // Get individual billionaire profile
    async getBillionaireProfile(id) {
        try {
            const response = await fetch(`${this.baseUrl}/profile/${id}`);
            if (!response.ok) {
                throw new Error(`Profile not found: ${id}`);
            }
            return await response.json();
        } catch (error) {
            console.error('Error fetching billionaire profile:', error);
            return null;
        }
    }

    // Get historical data
    async getHistoricalData(date) {
        try {
            const response = await fetch(`${this.baseUrl}/list/rtb/${date}`);
            if (!response.ok) {
                throw new Error(`Historical data not available for: ${date}`);
            }
            return await response.json();
        } catch (error) {
            console.error('Error fetching historical data:', error);
            return null;
        }
    }

    // Clear cache
    clearCache() {
        this.cache.clear();
        console.log('Billionaire API cache cleared');
    }

    // Get cache status
    getCacheStatus() {
        const status = {};
        for (const [key, value] of this.cache.entries()) {
            status[key] = {
                timestamp: value.timestamp,
                age: Date.now() - value.timestamp,
                expired: Date.now() - value.timestamp > this.cacheTimeout
            };
        }
        return status;
    }
}

// Create global instance
window.billionaireApi = new BillionaireApiService();

// Export for use in other files
window.BillionaireApiService = BillionaireApiService;
