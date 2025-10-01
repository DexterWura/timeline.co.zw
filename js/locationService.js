// Location and Regional Content Service
class LocationService {
    constructor() {
        this.userLocation = {
            country: 'ZW', // Default to Zimbabwe
            region: 'Africa',
            language: 'en',
            timezone: 'Africa/Harare'
        };
        
        this.africanCountries = [
            'ZW', 'ZA', 'NG', 'KE', 'GH', 'EG', 'MA', 'TN', 'DZ', 'LY', 'SD', 'ET', 'UG', 'TZ', 'RW', 'BI',
            'MW', 'ZM', 'BW', 'SZ', 'LS', 'MZ', 'MG', 'MU', 'SC', 'KM', 'DJ', 'SO', 'ER', 'SS', 'CF', 'TD',
            'NE', 'ML', 'BF', 'CI', 'LR', 'SL', 'GN', 'GW', 'GM', 'SN', 'MR', 'CV', 'AO', 'CD', 'CG', 'GA',
            'GQ', 'ST', 'CM', 'TG', 'BJ'
        ];
        
        this.regionalSettings = {
            'ZW': { name: 'Zimbabwe', language: 'en', currency: 'USD', timezone: 'Africa/Harare' },
            'ZA': { name: 'South Africa', language: 'en', currency: 'ZAR', timezone: 'Africa/Johannesburg' },
            'NG': { name: 'Nigeria', language: 'en', currency: 'NGN', timezone: 'Africa/Lagos' },
            'KE': { name: 'Kenya', language: 'en', currency: 'KES', timezone: 'Africa/Nairobi' },
            'GH': { name: 'Ghana', language: 'en', currency: 'GHS', timezone: 'Africa/Accra' },
            'EG': { name: 'Egypt', language: 'ar', currency: 'EGP', timezone: 'Africa/Cairo' },
            'MA': { name: 'Morocco', language: 'ar', currency: 'MAD', timezone: 'Africa/Casablanca' },
            'TN': { name: 'Tunisia', language: 'ar', currency: 'TND', timezone: 'Africa/Tunis' },
            'DZ': { name: 'Algeria', language: 'ar', currency: 'DZD', timezone: 'Africa/Algiers' },
            'LY': { name: 'Libya', language: 'ar', currency: 'LYD', timezone: 'Africa/Tripoli' },
            'SD': { name: 'Sudan', language: 'ar', currency: 'SDG', timezone: 'Africa/Khartoum' },
            'ET': { name: 'Ethiopia', language: 'am', currency: 'ETB', timezone: 'Africa/Addis_Ababa' },
            'UG': { name: 'Uganda', language: 'en', currency: 'UGX', timezone: 'Africa/Kampala' },
            'TZ': { name: 'Tanzania', language: 'sw', currency: 'TZS', timezone: 'Africa/Dar_es_Salaam' },
            'RW': { name: 'Rwanda', language: 'rw', currency: 'RWF', timezone: 'Africa/Kigali' },
            'BI': { name: 'Burundi', language: 'rn', currency: 'BIF', timezone: 'Africa/Bujumbura' },
            'MW': { name: 'Malawi', language: 'ny', currency: 'MWK', timezone: 'Africa/Blantyre' },
            'ZM': { name: 'Zambia', language: 'en', currency: 'ZMW', timezone: 'Africa/Lusaka' },
            'BW': { name: 'Botswana', language: 'en', currency: 'BWP', timezone: 'Africa/Gaborone' },
            'SZ': { name: 'Eswatini', language: 'en', currency: 'SZL', timezone: 'Africa/Mbabane' },
            'LS': { name: 'Lesotho', language: 'st', currency: 'LSL', timezone: 'Africa/Maseru' },
            'MZ': { name: 'Mozambique', language: 'pt', currency: 'MZN', timezone: 'Africa/Maputo' },
            'MG': { name: 'Madagascar', language: 'mg', currency: 'MGA', timezone: 'Indian/Antananarivo' },
            'MU': { name: 'Mauritius', language: 'en', currency: 'MUR', timezone: 'Indian/Mauritius' },
            'SC': { name: 'Seychelles', language: 'en', currency: 'SCR', timezone: 'Indian/Mahe' },
            'KM': { name: 'Comoros', language: 'ar', currency: 'KMF', timezone: 'Indian/Comoro' },
            'DJ': { name: 'Djibouti', language: 'ar', currency: 'DJF', timezone: 'Africa/Djibouti' },
            'SO': { name: 'Somalia', language: 'so', currency: 'SOS', timezone: 'Africa/Mogadishu' },
            'ER': { name: 'Eritrea', language: 'ti', currency: 'ERN', timezone: 'Africa/Asmara' },
            'SS': { name: 'South Sudan', language: 'en', currency: 'SSP', timezone: 'Africa/Juba' },
            'CF': { name: 'Central African Republic', language: 'fr', currency: 'XAF', timezone: 'Africa/Bangui' },
            'TD': { name: 'Chad', language: 'fr', currency: 'XAF', timezone: 'Africa/Ndjamena' },
            'NE': { name: 'Niger', language: 'fr', currency: 'XOF', timezone: 'Africa/Niamey' },
            'ML': { name: 'Mali', language: 'fr', currency: 'XOF', timezone: 'Africa/Bamako' },
            'BF': { name: 'Burkina Faso', language: 'fr', currency: 'XOF', timezone: 'Africa/Ouagadougou' },
            'CI': { name: 'Côte d\'Ivoire', language: 'fr', currency: 'XOF', timezone: 'Africa/Abidjan' },
            'LR': { name: 'Liberia', language: 'en', currency: 'LRD', timezone: 'Africa/Monrovia' },
            'SL': { name: 'Sierra Leone', language: 'en', currency: 'SLL', timezone: 'Africa/Freetown' },
            'GN': { name: 'Guinea', language: 'fr', currency: 'GNF', timezone: 'Africa/Conakry' },
            'GW': { name: 'Guinea-Bissau', language: 'pt', currency: 'XOF', timezone: 'Africa/Bissau' },
            'GM': { name: 'Gambia', language: 'en', currency: 'GMD', timezone: 'Africa/Banjul' },
            'SN': { name: 'Senegal', language: 'fr', currency: 'XOF', timezone: 'Africa/Dakar' },
            'MR': { name: 'Mauritania', language: 'ar', currency: 'MRU', timezone: 'Africa/Nouakchott' },
            'CV': { name: 'Cape Verde', language: 'pt', currency: 'CVE', timezone: 'Atlantic/Cape_Verde' },
            'AO': { name: 'Angola', language: 'pt', currency: 'AOA', timezone: 'Africa/Luanda' },
            'CD': { name: 'Democratic Republic of the Congo', language: 'fr', currency: 'CDF', timezone: 'Africa/Kinshasa' },
            'CG': { name: 'Republic of the Congo', language: 'fr', currency: 'XAF', timezone: 'Africa/Brazzaville' },
            'GA': { name: 'Gabon', language: 'fr', currency: 'XAF', timezone: 'Africa/Libreville' },
            'GQ': { name: 'Equatorial Guinea', language: 'es', currency: 'XAF', timezone: 'Africa/Malabo' },
            'ST': { name: 'São Tomé and Príncipe', language: 'pt', currency: 'STN', timezone: 'Africa/Sao_Tome' },
            'CM': { name: 'Cameroon', language: 'fr', currency: 'XAF', timezone: 'Africa/Douala' },
            'TG': { name: 'Togo', language: 'fr', currency: 'XOF', timezone: 'Africa/Lome' },
            'BJ': { name: 'Benin', language: 'fr', currency: 'XOF', timezone: 'Africa/Porto-Novo' }
        };
    }

    // Initialize location detection
    async initialize() {
        // Check if location is already stored
        const storedLocation = this.getStoredLocation();
        if (storedLocation) {
            this.userLocation = storedLocation;
            return storedLocation;
        }

        // Try to detect location
        try {
            const detectedLocation = await this.detectLocation();
            this.userLocation = detectedLocation;
            this.storeLocation(detectedLocation);
            return detectedLocation;
        } catch (error) {
            console.warn('Location detection failed, using default:', error);
            this.storeLocation(this.userLocation);
            return this.userLocation;
        }
    }

    // Detect user location
    async detectLocation() {
        // Try geolocation first
        if (navigator.geolocation) {
            try {
                const position = await this.getCurrentPosition();
                const location = await this.reverseGeocode(position.coords.latitude, position.coords.longitude);
                return this.processLocationData(location);
            } catch (error) {
                console.log('Geolocation failed, trying IP detection');
            }
        }

        // Fallback to IP-based detection
        return this.detectLocationByIP();
    }

    // Get current position
    getCurrentPosition() {
        return new Promise((resolve, reject) => {
            navigator.geolocation.getCurrentPosition(resolve, reject, {
                enableHighAccuracy: true,
                timeout: 10000,
                maximumAge: 300000 // 5 minutes
            });
        });
    }

    // Reverse geocode coordinates
    async reverseGeocode(lat, lng) {
        try {
            const response = await fetch(`https://api.bigdatacloud.net/data/reverse-geocode-client?latitude=${lat}&longitude=${lng}&localityLanguage=en`);
            const data = await response.json();
            return data;
        } catch (error) {
            throw new Error('Reverse geocoding failed');
        }
    }

    // Detect location by IP
    async detectLocationByIP() {
        try {
            const response = await fetch('https://ipapi.co/json/');
            const data = await response.json();
            return this.processLocationData(data);
        } catch (error) {
            throw new Error('IP-based location detection failed');
        }
    }

    // Process location data
    processLocationData(data) {
        const countryCode = data.country_code || 'ZW';
        const region = data.principalSubdivision || data.region || 'Unknown';
        
        return {
            country: countryCode,
            region: region,
            language: this.getLanguageForCountry(countryCode),
            timezone: data.timezone || this.getTimezoneForCountry(countryCode),
            city: data.city || data.locality || 'Unknown',
            isAfrican: this.africanCountries.includes(countryCode),
            isZimbabwean: countryCode === 'ZW'
        };
    }

    // Get language for country
    getLanguageForCountry(countryCode) {
        const settings = this.regionalSettings[countryCode];
        return settings ? settings.language : 'en';
    }

    // Get timezone for country
    getTimezoneForCountry(countryCode) {
        const settings = this.regionalSettings[countryCode];
        return settings ? settings.timezone : 'Africa/Harare';
    }

    // Store location in localStorage
    storeLocation(location) {
        try {
            localStorage.setItem('user_location', JSON.stringify(location));
        } catch (error) {
            console.warn('Failed to store location:', error);
        }
    }

    // Get stored location
    getStoredLocation() {
        try {
            const stored = localStorage.getItem('user_location');
            return stored ? JSON.parse(stored) : null;
        } catch (error) {
            console.warn('Failed to get stored location:', error);
            return null;
        }
    }

    // Update location
    async updateLocation(countryCode, region = null) {
        const newLocation = {
            country: countryCode,
            region: region || this.regionalSettings[countryCode]?.name || 'Unknown',
            language: this.getLanguageForCountry(countryCode),
            timezone: this.getTimezoneForCountry(countryCode),
            isAfrican: this.africanCountries.includes(countryCode),
            isZimbabwean: countryCode === 'ZW'
        };

        this.userLocation = newLocation;
        this.storeLocation(newLocation);
        
        // Update session on server
        try {
            await fetch('api/update-location.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(newLocation)
            });
        } catch (error) {
            console.warn('Failed to update location on server:', error);
        }

        return newLocation;
    }

    // Get content preferences based on location
    getContentPreferences() {
        const isAfrican = this.userLocation.isAfrican;
        const isZimbabwean = this.userLocation.isZimbabwean;

        return {
            primaryRegion: isZimbabwean ? 'ZW' : (isAfrican ? this.userLocation.country : 'global'),
            secondaryRegion: isZimbabwean ? 'Africa' : (isAfrican ? 'global' : 'Africa'),
            language: this.userLocation.language,
            prioritizeAfrican: isAfrican,
            prioritizeZimbabwean: isZimbabwean,
            showAfricanBadges: isAfrican,
            defaultCategory: isAfrican ? 'African Music' : 'Global Music'
        };
    }

    // Get regional content filters
    getRegionalFilters() {
        const prefs = this.getContentPreferences();
        
        return {
            regionCode: prefs.primaryRegion,
            language: prefs.language,
            category: prefs.defaultCategory,
            includeAfrican: prefs.prioritizeAfrican,
            includeZimbabwean: prefs.prioritizeZimbabwean
        };
    }

    // Check if user is in Africa
    isAfricanUser() {
        return this.userLocation.isAfrican;
    }

    // Check if user is in Zimbabwe
    isZimbabweanUser() {
        return this.userLocation.isZimbabwean;
    }

    // Get country name
    getCountryName(countryCode = null) {
        const code = countryCode || this.userLocation.country;
        const settings = this.regionalSettings[code];
        return settings ? settings.name : 'Unknown';
    }

    // Get all African countries
    getAfricanCountries() {
        return this.africanCountries.map(code => ({
            code: code,
            name: this.getCountryName(code)
        }));
    }

    // Format location for display
    formatLocation() {
        const countryName = this.getCountryName();
        const region = this.userLocation.region;
        
        if (region && region !== countryName) {
            return `${region}, ${countryName}`;
        }
        return countryName;
    }
}

// Create global instance
window.locationService = new LocationService();

// Export for use in other files
window.LocationService = LocationService;
