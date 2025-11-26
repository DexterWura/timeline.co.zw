// Main JavaScript functionality
document.addEventListener('DOMContentLoaded', function() {
    // Initialize all components
    initNavigation();
    initAnimations();
    initCounters();
    initSearch();
    initMobileMenu();
    initFrontendSidebar();
    initTrendingTabs();
});

// Navigation functionality
function initNavigation() {
    const navLinks = document.querySelectorAll('.nav-link');
    const currentPage = window.location.pathname.split('/').pop() || 'index.html';
    
    navLinks.forEach(link => {
        link.classList.remove('active');
        if (link.getAttribute('href') === currentPage) {
            link.classList.add('active');
        }
    });
}

// Animation initialization
function initAnimations() {
    // Intersection Observer for scroll animations
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };
    
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('animate-slide-up');
            }
        });
    }, observerOptions);
    
    // Observe elements for animation
    const animateElements = document.querySelectorAll('.chart-card, .trending-item, .stat-item');
    animateElements.forEach(el => observer.observe(el));
}

// Counter animation
function initCounters() {
    const counters = document.querySelectorAll('.stat-number');
    
    counters.forEach(counter => {
        const target = parseInt(counter.getAttribute('data-target'));
        const duration = 2000; // 2 seconds
        const increment = target / (duration / 16); // 60fps
        let current = 0;
        
        const updateCounter = () => {
            if (current < target) {
                current += increment;
                counter.textContent = Math.floor(current).toLocaleString();
                requestAnimationFrame(updateCounter);
            } else {
                counter.textContent = target.toLocaleString();
            }
        };
        
        // Start animation when element is visible
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    updateCounter();
                    observer.unobserve(entry.target);
                }
            });
        });
        
        observer.observe(counter);
    });
}

// Search functionality
function initSearch() {
    const searchBtn = document.querySelector('.search-btn');
    const searchOverlay = createSearchOverlay();
    
    searchBtn.addEventListener('click', () => {
        document.body.appendChild(searchOverlay);
        searchOverlay.style.display = 'flex';
        setTimeout(() => {
            searchOverlay.classList.add('active');
        }, 10);
    });
}

function createSearchOverlay() {
    const overlay = document.createElement('div');
    overlay.className = 'search-overlay';
    overlay.innerHTML = `
        <div class="search-container">
            <div class="search-header">
                <h3>Search Timeline</h3>
                <button class="close-search">&times;</button>
            </div>
            <div class="search-input-container">
                <input type="text" placeholder="Search for artists, songs, videos..." class="search-input">
                <button class="search-submit"><i class="fas fa-search"></i></button>
            </div>
            <div class="search-suggestions">
                <div class="suggestion-category">
                    <h4>Popular Searches</h4>
                    <div class="suggestion-tags">
                        <span class="suggestion-tag">Hot 100</span>
                        <span class="suggestion-tag">Top Videos</span>
                        <span class="suggestion-tag">Awards</span>
                        <span class="suggestion-tag">Business Charts</span>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    // Close search functionality
    overlay.querySelector('.close-search').addEventListener('click', () => {
        overlay.classList.remove('active');
        setTimeout(() => {
            overlay.remove();
        }, 300);
    });
    
    // Close on overlay click
    overlay.addEventListener('click', (e) => {
        if (e.target === overlay) {
            overlay.classList.remove('active');
            setTimeout(() => {
                overlay.remove();
            }, 300);
        }
    });
    
    return overlay;
}

// Subscribe modal functionality
function openSubscribeModal() {
    const modal = document.getElementById('subscribeModal');
    if (modal) {
        modal.classList.add('active');
        document.body.style.overflow = 'hidden';
    }
}

function closeSubscribeModal() {
    const modal = document.getElementById('subscribeModal');
    if (modal) {
        modal.classList.remove('active');
        document.body.style.overflow = '';
    }
}

// Initialize subscribe modal
document.addEventListener('DOMContentLoaded', function() {
    // Create subscribe modal if it doesn't exist
    if (!document.getElementById('subscribeModal')) {
        const modal = document.createElement('div');
        modal.id = 'subscribeModal';
        modal.className = 'subscribe-modal';
        modal.innerHTML = `
            <div class="subscribe-modal-content">
                <button class="subscribe-modal-close" onclick="closeSubscribeModal()">&times;</button>
                <h2>Subscribe to Newsletter</h2>
                <p>Get the latest music charts, news, and updates delivered to your inbox.</p>
                <form class="subscribe-form" id="subscribeForm">
                    <input type="email" name="email" placeholder="Enter your email" required>
                    <button type="submit">Subscribe</button>
                </form>
                <div id="subscribeMessage" style="margin-top: 1rem; display: none;"></div>
            </div>
        `;
        document.body.appendChild(modal);
        
        // Handle form submission
        document.getElementById('subscribeForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            const form = e.target;
            const button = form.querySelector('button');
            const messageDiv = document.getElementById('subscribeMessage');
            const email = form.email.value;
            
            button.disabled = true;
            button.textContent = 'Subscribing...';
            messageDiv.style.display = 'none';
            
            try {
                const response = await fetch('/api/subscribe.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        email: email,
                        type: 'newsletter',
                        source: 'website'
                    })
                });
                
                const data = await response.json();
                
                messageDiv.style.display = 'block';
                if (data.success) {
                    messageDiv.className = 'alert alert-success';
                    messageDiv.innerHTML = '<i class="fas fa-check-circle"></i> ' + data.message;
                    form.reset();
                    setTimeout(() => {
                        closeSubscribeModal();
                    }, 2000);
                } else {
                    messageDiv.className = 'alert alert-error';
                    messageDiv.innerHTML = '<i class="fas fa-exclamation-circle"></i> ' + (data.error || data.message || 'Failed to subscribe');
                }
            } catch (error) {
                messageDiv.style.display = 'block';
                messageDiv.className = 'alert alert-error';
                messageDiv.innerHTML = '<i class="fas fa-exclamation-circle"></i> An error occurred. Please try again.';
            } finally {
                button.disabled = false;
                button.textContent = 'Subscribe';
            }
        });
        
        // Close modal on outside click
        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                closeSubscribeModal();
            }
        });
    }
});

// Mobile menu functionality
function initMobileMenu() {
    const menuToggle = document.getElementById('mobileMenuToggle');
    const mainNav = document.getElementById('mainNav');
    const overlay = document.createElement('div');
    overlay.className = 'mobile-menu-overlay';
    document.body.appendChild(overlay);
    
    if (!menuToggle || !mainNav) return;
    
    const toggleMenu = (open) => {
        if (open) {
            mainNav.classList.add('active');
            menuToggle.classList.add('active');
            overlay.classList.add('active');
            document.body.classList.add('no-scroll');
        } else {
            mainNav.classList.remove('active');
            menuToggle.classList.remove('active');
            overlay.classList.remove('active');
            document.body.classList.remove('no-scroll');
        }
    };
    
    // Toggle menu on button click
    menuToggle.addEventListener('click', function(e) {
        e.stopPropagation();
        const isOpen = mainNav.classList.contains('active');
        toggleMenu(!isOpen);
    });
    
    // Close menu when clicking overlay
    overlay.addEventListener('click', function() {
        toggleMenu(false);
    });
    
    // Close menu when clicking nav links
    const navLinks = mainNav.querySelectorAll('.nav-link');
    navLinks.forEach(link => {
        link.addEventListener('click', function() {
            if (window.innerWidth <= 991) {
                toggleMenu(false);
            }
        });
    });
    
    // Close menu on window resize if desktop
    let resizeTimer;
    window.addEventListener('resize', function() {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(() => {
            if (window.innerWidth > 991) {
                toggleMenu(false);
            }
        }, 150);
    });
    
    // Close menu on ESC key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && mainNav.classList.contains('active')) {
            toggleMenu(false);
        }
    });
}

// Frontend sidebar mobile toggle
function initFrontendSidebar() {
    const sidebarToggle = document.getElementById('sidebarToggleMobile');
    const sidebarClose = document.getElementById('sidebarCloseMobile');
    const sidebar = document.getElementById('frontendSidebar');
    
    if (!sidebarToggle || !sidebar) return;
    
    const toggleSidebar = (open) => {
        if (open) {
            sidebar.classList.add('open');
            document.body.classList.add('sidebar-open-mobile');
            document.body.style.overflow = 'hidden';
        } else {
            sidebar.classList.remove('open');
            document.body.classList.remove('sidebar-open-mobile');
            document.body.style.overflow = '';
        }
    };
    
    // Toggle sidebar on button click
    sidebarToggle.addEventListener('click', function(e) {
        e.stopPropagation();
        toggleSidebar(true);
    });
    
    // Close sidebar on close button
    if (sidebarClose) {
        sidebarClose.addEventListener('click', function(e) {
            e.stopPropagation();
            toggleSidebar(false);
        });
    }
    
    // Close sidebar when clicking outside on mobile
    document.addEventListener('click', function(e) {
        if (window.innerWidth <= 991) {
            if (sidebar && sidebar.classList.contains('open')) {
                if (!sidebar.contains(e.target) && !sidebarToggle.contains(e.target)) {
                    toggleSidebar(false);
                }
            }
        }
    });
    
    // Close sidebar when clicking sidebar links on mobile
    const sidebarLinks = sidebar.querySelectorAll('.sidebar-section');
    sidebarLinks.forEach(link => {
        link.addEventListener('click', function() {
            if (window.innerWidth <= 991) {
                toggleSidebar(false);
            }
        });
    });
    
    // Close sidebar on window resize if desktop
    let resizeTimer;
    window.addEventListener('resize', function() {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(() => {
            if (window.innerWidth > 991) {
                toggleSidebar(false);
            }
        }, 150);
    });
    
    // Close sidebar on ESC key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && sidebar.classList.contains('open')) {
            toggleSidebar(false);
        }
    });
}

// Initialize both menus
document.addEventListener('DOMContentLoaded', function() {
    initMobileMenu();
    initFrontendSidebar();
});

// Utility functions
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

function throttle(func, limit) {
    let inThrottle;
    return function() {
        const args = arguments;
        const context = this;
        if (!inThrottle) {
            func.apply(context, args);
            inThrottle = true;
            setTimeout(() => inThrottle = false, limit);
        }
    };
}

// Smooth scrolling for anchor links
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        if (target) {
            target.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        }
    });
});

// Add loading states
function showLoading(element) {
    element.innerHTML = '<div class="loading-spinner"></div>';
}

function hideLoading(element, content) {
    element.innerHTML = content;
}

// API simulation functions (to be replaced with real API calls)
function fetchChartData(chartType) {
    return new Promise((resolve) => {
        setTimeout(() => {
            // Simulate API response
            const mockData = {
                'hot-100': generateMockChartData(100),
                'videos': generateMockVideoData(100),
                'richest': generateMockRichestData(100),
                'awards': generateMockAwardsData(),
                'business': generateMockBusinessData()
            };
            resolve(mockData[chartType] || []);
        }, 1000);
    });
}

function generateMockChartData(count) {
    const artists = ['Taylor Swift', 'Drake', 'Ariana Grande', 'The Weeknd', 'Billie Eilish', 'Post Malone', 'Dua Lipa', 'Ed Sheeran', 'Justin Bieber', 'Olivia Rodrigo'];
    const songs = ['Golden', 'Ordinary', 'Soda', 'Midnight', 'Sunset', 'Dreams', 'Reality', 'Fantasy', 'Echo', 'Silence'];
    
    return Array.from({ length: count }, (_, i) => ({
        rank: i + 1,
        title: songs[i % songs.length],
        artist: artists[i % artists.length],
        weeks: Math.floor(Math.random() * 50) + 1,
        peak: Math.floor(Math.random() * (i + 1)) + 1,
        change: i < 10 ? Math.floor(Math.random() * 5) - 2 : 0
    }));
}

function generateMockVideoData(count) {
    const videos = ['Music Video 1', 'Concert Performance', 'Behind the Scenes', 'Acoustic Version', 'Dance Challenge', 'Lyric Video', 'Live Session', 'Official Video', 'Remix Video', 'Tribute Video'];
    
    return Array.from({ length: count }, (_, i) => ({
        rank: i + 1,
        title: videos[i % videos.length],
        artist: `Artist ${i + 1}`,
        views: Math.floor(Math.random() * 1000000000) + 1000000,
        duration: `${Math.floor(Math.random() * 4) + 2}:${Math.floor(Math.random() * 60).toString().padStart(2, '0')}`,
        uploadDate: new Date(Date.now() - Math.random() * 365 * 24 * 60 * 60 * 1000).toISOString().split('T')[0]
    }));
}

function generateMockRichestData(count) {
    const names = ['Elon Musk', 'Jeff Bezos', 'Bill Gates', 'Warren Buffett', 'Mark Zuckerberg', 'Larry Page', 'Sergey Brin', 'Steve Ballmer', 'Larry Ellison', 'Mukesh Ambani'];
    
    return Array.from({ length: count }, (_, i) => ({
        rank: i + 1,
        name: names[i % names.length],
        netWorth: Math.floor(Math.random() * 200) + 50, // Billions
        source: ['Technology', 'Finance', 'Real Estate', 'Entertainment', 'Retail'][i % 5],
        age: Math.floor(Math.random() * 40) + 30,
        country: ['USA', 'India', 'China', 'Germany', 'France'][i % 5]
    }));
}

function generateMockAwardsData() {
    return [
        { category: 'Album of the Year', winner: 'Taylor Swift - Midnights', nominees: ['Drake - Her Loss', 'Beyoncé - Renaissance', 'Bad Bunny - Un Verano Sin Ti'] },
        { category: 'Song of the Year', winner: 'Harry Styles - As It Was', nominees: ['Lizzo - About Damn Time', 'Steve Lacy - Bad Habit', 'Beyoncé - Break My Soul'] },
        { category: 'Best New Artist', winner: 'Wet Leg', nominees: ['Omar Apollo', 'Anitta', 'Måneskin'] },
        { category: 'Record of the Year', winner: 'Lizzo - About Damn Time', nominees: ['Harry Styles - As It Was', 'Beyoncé - Break My Soul', 'Adele - Easy on Me'] }
    ];
}

function generateMockBusinessData() {
    return [
        { metric: 'Streaming Revenue', value: '$12.3B', change: '+15.2%', period: '2024' },
        { metric: 'Physical Sales', value: '$1.8B', change: '-8.5%', period: '2024' },
        { metric: 'Digital Downloads', value: '$0.9B', change: '-22.1%', period: '2024' },
        { metric: 'Sync Revenue', value: '$0.4B', change: '+5.7%', period: '2024' }
    ];
}

// Trending tabs functionality
function initTrendingTabs() {
    const trendingTabs = document.querySelectorAll('.trending-tab');
    const trendingContents = document.querySelectorAll('.trending-content');
    
    trendingTabs.forEach(tab => {
        tab.addEventListener('click', () => {
            const category = tab.getAttribute('data-category');
            
            // Remove active class from all tabs and contents
            trendingTabs.forEach(t => t.classList.remove('active'));
            trendingContents.forEach(content => content.classList.remove('active'));
            
            // Add active class to clicked tab and corresponding content
            tab.classList.add('active');
            const targetContent = document.getElementById(`trending-${category}`);
            if (targetContent) {
                targetContent.classList.add('active');
            }
        });
    });
}
