// Richest People page specific functionality
document.addEventListener('DOMContentLoaded', async function() {
    await initRichestPage();
});

async function initRichestPage() {
    await loadRichestData();
    initFilters();
    initModal();
    initRichestInteractions();
}

// Load and display richest people data
async function loadRichestData() {
    const richestContainer = document.getElementById('billionaires-container');
    if (!richestContainer) return;
    
    // Show loading state
    richestContainer.innerHTML = '<div class="loading"><div class="loading-spinner"></div></div>';
    
    try {
        // Simulate API call
        const richestData = await fetchChartData('richest');
        displayRichestPeople(richestData.slice(3)); // Skip first 3 (featured)
    } catch (error) {
        console.error('Error loading richest data:', error);
        richestContainer.innerHTML = '<div class="error">Failed to load richest people data. Please try again.</div>';
    }
}

function displayRichestPeople(people) {
    const richestContainer = document.getElementById('billionaires-container');
    if (!richestContainer) return;
    
    richestContainer.innerHTML = '';
    
    people.forEach((person, index) => {
        const richestItem = createRichestItem(person, index + 4); // Start from rank 4
        richestContainer.appendChild(richestItem);
    });
    
    // Add stagger animation
    const richestItems = richestContainer.querySelectorAll('.billionaire-item');
    richestItems.forEach((item, index) => {
        setTimeout(() => {
            item.classList.add('animate-slide-up');
        }, index * 50);
    });
}

function createRichestItem(person, rank) {
    const richestItem = document.createElement('div');
    richestItem.className = 'billionaire-item';
    richestItem.setAttribute('data-rank', rank);
    richestItem.setAttribute('data-source', person.source || 'technology');
    richestItem.setAttribute('data-country', person.country || 'usa');
    richestItem.setAttribute('data-networth', person.netWorth);
    
    // Add wealth change class
    if (person.wealthChange > 0) {
        richestItem.classList.add('wealth-gain');
    } else if (person.wealthChange < 0) {
        richestItem.classList.add('wealth-loss');
    }
    
    richestItem.innerHTML = `
        <div class="rank-badge">${rank}</div>
        <div class="billionaire-photo">
            <img src="${person.photo || `https://via.placeholder.com/60x60/${getRandomColor()}/ffffff?text=${person.name.split(' ').map(n => n[0]).join('')}`}" 
                 alt="${person.name}" class="photo-img">
            <div class="wealth-indicator">
                <i class="fas fa-chart-line"></i>
            </div>
        </div>
        <div class="billionaire-info">
            <h3 class="billionaire-name">${person.name}</h3>
            <p class="billionaire-source">${person.source}</p>
            <div class="net-worth">
                <span class="worth-amount">$${formatNetWorth(person.netWorth)}</span>
                <span class="worth-change ${person.wealthChange >= 0 ? 'positive' : 'negative'}">
                    ${person.wealthChange >= 0 ? '+' : ''}$${formatNetWorth(Math.abs(person.wealthChange))}
                </span>
            </div>
        </div>
        <div class="billionaire-stats">
            <div class="stat">
                <span class="stat-label">Age</span>
                <span class="stat-value">${person.age}</span>
            </div>
            <div class="stat">
                <span class="stat-label">Country</span>
                <span class="stat-value">${person.country.toUpperCase()}</span>
            </div>
            <div class="stat">
                <span class="stat-label">Source</span>
                <span class="stat-value">${person.source}</span>
            </div>
        </div>
        <div class="billionaire-actions">
            <button class="action-btn" title="View Profile" onclick="viewProfile('${person.id}')">
                <i class="fas fa-user"></i>
            </button>
            <button class="action-btn" title="View Companies" onclick="viewCompanies('${person.id}')">
                <i class="fas fa-building"></i>
            </button>
        </div>
    `;
    
    return richestItem;
}

// Filter functionality
function initFilters() {
    const filterTabs = document.querySelectorAll('.filter-tab');
    const countryFilter = document.querySelector('.country-filter');
    const sortBtn = document.querySelector('.sort-btn');
    
    filterTabs.forEach(tab => {
        tab.addEventListener('click', () => {
            filterTabs.forEach(t => t.classList.remove('active'));
            tab.classList.add('active');
            
            const filter = tab.getAttribute('data-filter');
            applyRichestFilter(filter);
        });
    });
    
    if (countryFilter) {
        countryFilter.addEventListener('change', (e) => {
            applyCountryFilter(e.target.value);
        });
    }
    
    if (sortBtn) {
        sortBtn.addEventListener('click', () => {
            toggleRichestSort();
        });
    }
}

function applyRichestFilter(filter) {
    const richestItems = document.querySelectorAll('.billionaire-item');
    
    richestItems.forEach(item => {
        const itemSource = item.getAttribute('data-source');
        let shouldShow = true;
        
        switch (filter) {
            case 'tech':
                shouldShow = itemSource === 'technology';
                break;
            case 'finance':
                shouldShow = itemSource === 'finance';
                break;
            case 'retail':
                shouldShow = itemSource === 'retail';
                break;
            case 'entertainment':
                shouldShow = itemSource === 'entertainment';
                break;
            case 'all':
            default:
                shouldShow = true;
                break;
        }
        
        item.style.display = shouldShow ? 'flex' : 'none';
    });
}

function applyCountryFilter(country) {
    const richestItems = document.querySelectorAll('.billionaire-item');
    
    richestItems.forEach(item => {
        const itemCountry = item.getAttribute('data-country');
        const shouldShow = country === 'all' || itemCountry === country;
        item.style.display = shouldShow ? 'flex' : 'none';
    });
}

function toggleRichestSort() {
    const richestContainer = document.getElementById('billionaires-container');
    const items = Array.from(richestContainer.querySelectorAll('.billionaire-item'));
    
    // Toggle between ascending and descending net worth order
    const isAscending = richestContainer.getAttribute('data-sort') === 'asc';
    const newSort = isAscending ? 'desc' : 'asc';
    
    items.sort((a, b) => {
        const netWorthA = parseFloat(a.getAttribute('data-networth'));
        const netWorthB = parseFloat(b.getAttribute('data-networth'));
        return newSort === 'asc' ? netWorthA - netWorthB : netWorthB - netWorthA;
    });
    
    // Re-append sorted items
    items.forEach(item => richestContainer.appendChild(item));
    richestContainer.setAttribute('data-sort', newSort);
}

// Modal functionality
function initModal() {
    const modal = document.getElementById('wealth-info-modal');
    const closeBtn = modal?.querySelector('.close-modal');
    const infoBtn = document.querySelector('.action-btn[title="Wealth Info"]');
    
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

// Richest interactions
function initRichestInteractions() {
    // Date selector functionality
    const dateBtn = document.querySelector('.date-btn');
    if (dateBtn) {
        dateBtn.addEventListener('click', () => {
            showDatePicker();
        });
    }
    
    // Share functionality
    const shareBtn = document.querySelector('.action-btn[title="Share List"]');
    if (shareBtn) {
        shareBtn.addEventListener('click', () => {
            shareRichestList();
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
                    <input type="month" id="richest-date" class="date-input">
                    <button class="btn btn-primary" onclick="loadRichestForDate()">Load Data</button>
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

function loadRichestForDate() {
    const dateInput = document.getElementById('richest-date');
    const selectedDate = dateInput.value;
    
    if (selectedDate) {
        // Update date button text
        const dateBtn = document.querySelector('.date-btn');
        const formattedDate = new Date(selectedDate + '-01').toLocaleDateString('en-US', {
            year: 'numeric',
            month: 'long'
        });
        
        dateBtn.innerHTML = `<i class="fas fa-calendar"></i> AS OF ${formattedDate.toUpperCase()}`;
        
        // Reload richest data for selected date
        loadRichestData();
        
        // Close modal
        document.querySelector('.modal').remove();
    }
}

function shareRichestList() {
    if (navigator.share) {
        navigator.share({
            title: 'Timeline Top 100 Richest People',
            text: 'Check out the world\'s richest people on Timeline!',
            url: window.location.href
        });
    } else {
        // Fallback: copy to clipboard
        navigator.clipboard.writeText(window.location.href).then(() => {
            showNotification('Richest list link copied to clipboard!');
        });
    }
}

// Richest interaction functions
function viewProfile(personId) {
    console.log('Viewing profile:', personId);
    showNotification('Opening profile for: ' + personId);
    // Implement profile view functionality
}

function viewCompanies(personId) {
    console.log('Viewing companies:', personId);
    showNotification('Opening companies for: ' + personId);
    // Implement companies view functionality
}

// Utility functions
function formatNetWorth(amount) {
    if (amount >= 100) {
        return (amount / 100).toFixed(1) + 'B';
    } else if (amount >= 1) {
        return amount.toFixed(1) + 'B';
    } else {
        return (amount * 1000).toFixed(0) + 'M';
    }
}

function getRandomColor() {
    const colors = ['00d4aa', '667eea', '764ba2', 'f093fb', 'f5576c', '4facfe', '00f2fe'];
    return colors[Math.floor(Math.random() * colors.length)];
}

function getRealPersonPhoto(name) {
    // Real photos of famous billionaires
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
        'Mukesh Ambani': 'https://upload.wikimedia.org/wikipedia/commons/7/7b/Mukesh_Ambani.jpg'
    };
    
    return photos[name] || `https://ui-avatars.com/api/?name=${encodeURIComponent(name)}&size=200&background=00d4aa&color=fff&bold=true`;
}

// Enhanced mock data generation for richest people
async function generateMockRichestData(count) {
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
    
    const sourceCategories = ['technology', 'retail', 'technology', 'finance', 'technology',
        'technology', 'technology', 'technology', 'technology', 'retail',
        'infrastructure', 'luxury', 'cosmetics', 'telecommunications', 'retail',
        'conglomerate', 'conglomerate', 'technology', 'retail', 'retail',
        'technology', 'technology', 'technology', 'technology', 'conglomerate',
        'real estate', 'technology', 'technology', 'technology', 'real estate'];
    
    const countries = ['usa', 'usa', 'usa', 'usa', 'usa', 'usa', 'usa', 'usa', 'usa', 'india',
        'india', 'france', 'france', 'mexico', 'spain', 'usa', 'usa', 'usa', 'usa', 'usa',
        'china', 'china', 'china', 'china', 'hong kong', 'hong kong', 'china', 'china', 'china', 'china'];
    
    const data = [];
    for (let i = 0; i < count; i++) {
        const photo = window.imageService ? await window.imageService.getBillionairePhoto(names[i % names.length]) : getRealPersonPhoto(names[i % names.length]);
        data.push({
        id: `person-${i + 1}`,
        name: names[i % names.length],
        source: sources[i % sources.length],
        sourceCategory: sourceCategories[i % sourceCategories.length],
        netWorth: Math.floor(Math.random() * 200) + 50, // Billions
        wealthChange: (Math.random() - 0.5) * 20, // -10 to +10 billion change
        age: Math.floor(Math.random() * 40) + 30,
        country: countries[i % countries.length],
        photo: photo
        });
    }
    return data;
}
