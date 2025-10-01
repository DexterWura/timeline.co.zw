// Awards page specific functionality
document.addEventListener('DOMContentLoaded', function() {
    initAwardsPage();
});

function initAwardsPage() {
    loadAwardsData();
    initFilters();
    initAwardModal();
    initAwardsInteractions();
}

// Load and display awards data
async function loadAwardsData() {
    try {
        // Simulate API call
        const awardsData = await fetchChartData('awards');
        displayAwardCategories(awardsData);
        displayRecentWinners(awardsData);
    } catch (error) {
        console.error('Error loading awards data:', error);
    }
}

function displayAwardCategories(awards) {
    const categoriesGrid = document.getElementById('categories-grid');
    if (!categoriesGrid) return;
    
    categoriesGrid.innerHTML = '';
    
    awards.forEach((award, index) => {
        const categoryCard = createCategoryCard(award, index);
        categoriesGrid.appendChild(categoryCard);
    });
    
    // Add stagger animation
    const categoryCards = categoriesGrid.querySelectorAll('.category-card');
    categoryCards.forEach((card, index) => {
        setTimeout(() => {
            card.classList.add('animate-slide-up');
        }, index * 100);
    });
}

function createCategoryCard(award, index) {
    const categoryCard = document.createElement('div');
    categoryCard.className = 'category-card';
    categoryCard.setAttribute('data-category', award.category.toLowerCase().replace(/\s+/g, '-'));
    
    const iconMap = {
        'Album of the Year': 'fas fa-compact-disc',
        'Song of the Year': 'fas fa-music',
        'Best New Artist': 'fas fa-star',
        'Record of the Year': 'fas fa-microphone'
    };
    
    const icon = iconMap[award.category] || 'fas fa-trophy';
    
    categoryCard.innerHTML = `
        <div class="category-icon">
            <i class="${icon}"></i>
        </div>
        <h3>${award.category}</h3>
        <p>Recognizing excellence in ${award.category.toLowerCase()} for outstanding musical achievement.</p>
        <div class="winner-preview">
            <h4>Winner</h4>
            <p>${award.winner}</p>
        </div>
        <div class="category-actions">
            <a href="#" class="view-winner-btn" onclick="showAwardModal('${award.category}')">
                View Details <i class="fas fa-arrow-right"></i>
            </a>
            <span class="nominees-count">${award.nominees.length} nominees</span>
        </div>
    `;
    
    return categoryCard;
}

function displayRecentWinners(awards) {
    const winnersTimeline = document.getElementById('winners-timeline');
    if (!winnersTimeline) return;
    
    winnersTimeline.innerHTML = '';
    
    // Create timeline items for each award
    awards.forEach((award, index) => {
        const winnerItem = createWinnerItem(award, index);
        winnersTimeline.appendChild(winnerItem);
    });
    
    // Add stagger animation
    const winnerItems = winnersTimeline.querySelectorAll('.winner-item');
    winnerItems.forEach((item, index) => {
        setTimeout(() => {
            item.classList.add('animate-slide-up');
        }, index * 150);
    });
}

function createWinnerItem(award, index) {
    const winnerItem = document.createElement('div');
    winnerItem.className = 'winner-item';
    
    const dates = ['February 2, 2025', 'January 15, 2025', 'December 20, 2024', 'November 10, 2024'];
    const venues = ['Crypto.com Arena, LA', 'Madison Square Garden, NY', 'Hollywood Bowl, LA', 'Radio City Music Hall, NY'];
    
    winnerItem.innerHTML = `
        <div class="winner-date">${dates[index % dates.length]}</div>
        <div class="winner-content">
            <h4>${award.category}</h4>
            <p>${award.winner}</p>
            <span class="winner-award">${venues[index % venues.length]}</span>
        </div>
    `;
    
    return winnerItem;
}

// Filter functionality
function initFilters() {
    const filterTabs = document.querySelectorAll('.filter-tab');
    const categoryFilter = document.querySelector('.category-filter');
    const sortBtn = document.querySelector('.sort-btn');
    
    filterTabs.forEach(tab => {
        tab.addEventListener('click', () => {
            filterTabs.forEach(t => t.classList.remove('active'));
            tab.classList.add('active');
            
            const filter = tab.getAttribute('data-filter');
            applyAwardFilter(filter);
        });
    });
    
    if (categoryFilter) {
        categoryFilter.addEventListener('change', (e) => {
            applyCategoryFilter(e.target.value);
        });
    }
    
    if (sortBtn) {
        sortBtn.addEventListener('click', () => {
            toggleAwardSort();
        });
    }
}

function applyAwardFilter(filter) {
    const categoryCards = document.querySelectorAll('.category-card');
    
    categoryCards.forEach(card => {
        let shouldShow = true;
        
        switch (filter) {
            case 'grammy':
                shouldShow = card.querySelector('h3').textContent.includes('Album') || 
                            card.querySelector('h3').textContent.includes('Song') ||
                            card.querySelector('h3').textContent.includes('Record') ||
                            card.querySelector('h3').textContent.includes('Artist');
                break;
            case 'mtv':
                shouldShow = card.querySelector('h3').textContent.includes('Video');
                break;
            case 'billboard':
                shouldShow = card.querySelector('h3').textContent.includes('Chart');
                break;
            case 'all':
            default:
                shouldShow = true;
                break;
        }
        
        card.style.display = shouldShow ? 'block' : 'none';
    });
}

function applyCategoryFilter(category) {
    const categoryCards = document.querySelectorAll('.category-card');
    
    categoryCards.forEach(card => {
        const cardCategory = card.getAttribute('data-category');
        const shouldShow = category === 'all' || cardCategory === category;
        card.style.display = shouldShow ? 'block' : 'none';
    });
}

function toggleAwardSort() {
    const categoriesGrid = document.getElementById('categories-grid');
    const items = Array.from(categoriesGrid.querySelectorAll('.category-card'));
    
    // Toggle between alphabetical and reverse alphabetical order
    const isAscending = categoriesGrid.getAttribute('data-sort') === 'asc';
    const newSort = isAscending ? 'desc' : 'asc';
    
    items.sort((a, b) => {
        const titleA = a.querySelector('h3').textContent;
        const titleB = b.querySelector('h3').textContent;
        return newSort === 'asc' ? titleA.localeCompare(titleB) : titleB.localeCompare(titleA);
    });
    
    // Re-append sorted items
    items.forEach(item => categoriesGrid.appendChild(item));
    categoriesGrid.setAttribute('data-sort', newSort);
}

// Award modal functionality
function initAwardModal() {
    const modal = document.getElementById('award-modal');
    const closeBtn = modal?.querySelector('.close-modal');
    
    if (closeBtn) {
        closeBtn.addEventListener('click', () => {
            hideAwardModal(modal);
        });
    }
    
    // Close modal on overlay click
    if (modal) {
        modal.addEventListener('click', (e) => {
            if (e.target === modal) {
                hideAwardModal(modal);
            }
        });
    }
}

function showAwardModal(category) {
    const modal = document.getElementById('award-modal');
    const title = modal.querySelector('#modal-award-title');
    const winnerName = modal.querySelector('#modal-winner-name');
    const nomineesList = modal.querySelector('#modal-nominees-list');
    const description = modal.querySelector('#modal-award-description');
    
    // Find award data
    const awardData = getAwardData(category);
    
    if (title) title.textContent = category;
    if (winnerName) winnerName.textContent = awardData.winner;
    if (description) description.textContent = awardData.description || `The ${category} award recognizes outstanding achievement in this category.`;
    
    if (nomineesList) {
        nomineesList.innerHTML = '';
        awardData.nominees.forEach(nominee => {
            const li = document.createElement('li');
            li.textContent = nominee;
            nomineesList.appendChild(li);
        });
    }
    
    modal.classList.add('active');
    document.body.style.overflow = 'hidden';
}

function hideAwardModal(modal) {
    if (modal) {
        modal.classList.remove('active');
        document.body.style.overflow = 'auto';
    }
}

function getAwardData(category) {
    // Mock award data - in real app, this would come from API
    const awardsData = {
        'Album of the Year': {
            winner: 'Taylor Swift - Midnights',
            nominees: ['Drake - Her Loss', 'Beyoncé - Renaissance', 'Bad Bunny - Un Verano Sin Ti'],
            description: 'Awarded to the artist and producer for the best album of the year.'
        },
        'Song of the Year': {
            winner: 'Harry Styles - As It Was',
            nominees: ['Lizzo - About Damn Time', 'Steve Lacy - Bad Habit', 'Beyoncé - Break My Soul'],
            description: 'Awarded to the songwriter for the best song of the year.'
        },
        'Best New Artist': {
            winner: 'Wet Leg',
            nominees: ['Omar Apollo', 'Anitta', 'Måneskin'],
            description: 'Awarded to a new artist who has released their first recording during the eligibility period.'
        },
        'Record of the Year': {
            winner: 'Lizzo - About Damn Time',
            nominees: ['Harry Styles - As It Was', 'Beyoncé - Break My Soul', 'Adele - Easy on Me'],
            description: 'Awarded to the artist and producer for the best recording of the year.'
        }
    };
    
    return awardsData[category] || {
        winner: 'Unknown',
        nominees: ['No nominees available'],
        description: 'No description available.'
    };
}

// Awards interactions
function initAwardsInteractions() {
    // Year selector functionality
    const yearBtn = document.querySelector('.year-btn');
    if (yearBtn) {
        yearBtn.addEventListener('click', () => {
            showYearPicker();
        });
    }
    
    // Share functionality
    const shareBtn = document.querySelector('.action-btn[title="Share Awards"]');
    if (shareBtn) {
        shareBtn.addEventListener('click', () => {
            shareAwards();
        });
    }
}

function showYearPicker() {
    // Create year picker modal
    const modal = document.createElement('div');
    modal.className = 'modal active';
    modal.innerHTML = `
        <div class="modal-content">
            <div class="modal-header">
                <h3>Select Year</h3>
                <button class="close-modal">&times;</button>
            </div>
            <div class="modal-body">
                <div class="year-picker">
                    <select id="award-year" class="year-select">
                        <option value="2025">2025</option>
                        <option value="2024">2024</option>
                        <option value="2023">2023</option>
                        <option value="2022">2022</option>
                    </select>
                    <button class="btn btn-primary" onclick="loadAwardsForYear()">Load Awards</button>
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

function loadAwardsForYear() {
    const yearSelect = document.getElementById('award-year');
    const selectedYear = yearSelect.value;
    
    if (selectedYear) {
        // Update year button text
        const yearBtn = document.querySelector('.year-btn');
        yearBtn.innerHTML = `<i class="fas fa-calendar"></i> ${selectedYear} AWARDS`;
        
        // Reload awards data for selected year
        loadAwardsData();
        
        // Close modal
        document.querySelector('.modal').remove();
    }
}

function shareAwards() {
    if (navigator.share) {
        navigator.share({
            title: 'Timeline Music Awards',
            text: 'Check out the latest music awards on Timeline!',
            url: window.location.href
        });
    } else {
        // Fallback: copy to clipboard
        navigator.clipboard.writeText(window.location.href).then(() => {
            showNotification('Awards link copied to clipboard!');
        });
    }
}

// Enhanced mock data generation for awards
function generateMockAwardsData() {
    return [
        {
            category: 'Album of the Year',
            winner: 'Taylor Swift - Midnights',
            nominees: ['Drake - Her Loss', 'Beyoncé - Renaissance', 'Bad Bunny - Un Verano Sin Ti', 'Harry Styles - Harry\'s House'],
            description: 'Awarded to the artist and producer for the best album of the year.'
        },
        {
            category: 'Song of the Year',
            winner: 'Harry Styles - As It Was',
            nominees: ['Lizzo - About Damn Time', 'Steve Lacy - Bad Habit', 'Beyoncé - Break My Soul', 'Adele - Easy on Me'],
            description: 'Awarded to the songwriter for the best song of the year.'
        },
        {
            category: 'Best New Artist',
            winner: 'Wet Leg',
            nominees: ['Omar Apollo', 'Anitta', 'Måneskin', 'DOMi & JD BECK'],
            description: 'Awarded to a new artist who has released their first recording during the eligibility period.'
        },
        {
            category: 'Record of the Year',
            winner: 'Lizzo - About Damn Time',
            nominees: ['Harry Styles - As It Was', 'Beyoncé - Break My Soul', 'Adele - Easy on Me', 'Brandi Carlile - You and Me on the Rock'],
            description: 'Awarded to the artist and producer for the best recording of the year.'
        }
    ];
}
