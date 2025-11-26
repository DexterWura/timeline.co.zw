window.addEventListener('load', function() {
    document.body.classList.remove('loading');
    document.body.classList.add('loaded');
    
    const cards = document.querySelectorAll('.stat-card, .chart-card, .info-card');
    const cardCount = cards.length;
    const maxDelay = Math.min(cardCount * 20, 200);
    
    cards.forEach((card, index) => {
        const delay = Math.min(index * 20, maxDelay);
        setTimeout(() => {
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, delay);
    });
});

document.addEventListener('DOMContentLoaded', function() {
    document.body.classList.add('loading');
    
    const currentYearElement = document.getElementById('currentYear');
    if (currentYearElement) {
        currentYearElement.textContent = new Date().getFullYear();
    }
    
    const menuToggle = document.getElementById('menuToggle');
    const sidebar = document.querySelector('.sidebar');
    
    if (menuToggle && sidebar) {
        const toggleSidebar = (open) => {
            if (open) {
                sidebar.classList.add('open');
                document.body.classList.add('sidebar-open');
                document.body.style.overflow = 'hidden';
            } else {
                sidebar.classList.remove('open');
                document.body.classList.remove('sidebar-open');
                document.body.style.overflow = '';
            }
        };

        menuToggle.addEventListener('click', function(e) {
            e.stopPropagation();
            const isOpen = sidebar.classList.contains('open');
            toggleSidebar(!isOpen);
        });

        const handleOutsideClick = (event) => {
            if (window.innerWidth <= 1199) {
                if (sidebar.classList.contains('open')) {
                    if (!sidebar.contains(event.target) && !menuToggle.contains(event.target)) {
                        toggleSidebar(false);
                    }
                }
            }
        };

        document.addEventListener('click', handleOutsideClick, { passive: true });

        let resizeTimer;
        window.addEventListener('resize', function() {
            clearTimeout(resizeTimer);
            resizeTimer = setTimeout(() => {
                if (window.innerWidth > 1199) {
                    toggleSidebar(false);
                }
            }, 150);
        }, { passive: true });

        const navItems = document.querySelectorAll('.nav-item');
        navItems.forEach(item => {
            item.addEventListener('click', function() {
                if (window.innerWidth <= 1199) {
                    toggleSidebar(false);
                }
            }, { passive: true });
        });
    }

    const sidebarToggle = document.getElementById('sidebarToggle');
    
    if (sidebarToggle && sidebar) {
        const toggleCollapse = (collapsed) => {
            if (window.innerWidth >= 1200) {
                if (collapsed) {
                    sidebar.classList.add('collapsed');
                    localStorage.setItem('sidebarCollapsed', 'true');
                } else {
                    sidebar.classList.remove('collapsed');
                    localStorage.setItem('sidebarCollapsed', 'false');
                }
            }
        };

        const savedState = localStorage.getItem('sidebarCollapsed');
        if (savedState === 'true' && window.innerWidth >= 1200) {
            toggleCollapse(true);
        }

        sidebarToggle.addEventListener('click', function(e) {
            e.stopPropagation();
            if (window.innerWidth >= 1200) {
                const isCollapsed = sidebar.classList.contains('collapsed');
                toggleCollapse(!isCollapsed);
            }
        }, { passive: true });

        window.addEventListener('resize', function() {
            if (window.innerWidth < 1200) {
                sidebar.classList.remove('collapsed');
            } else {
                const savedState = localStorage.getItem('sidebarCollapsed');
                if (savedState === 'true') {
                    toggleCollapse(true);
                }
            }
        }, { passive: true });
    }
    
    const searchBarToggle = document.querySelector('.search-bar-toggle');
    const searchBox = document.querySelector('.search-box');
    
    if (searchBarToggle && searchBox) {
        searchBarToggle.addEventListener('click', function(e) {
            e.preventDefault();
            searchBox.classList.toggle('search-bar-show');
        }, { passive: false });
        
        document.addEventListener('click', function(event) {
            if (window.innerWidth <= 1199) {
                if (searchBox.classList.contains('search-bar-show')) {
                    if (!searchBox.contains(event.target) && !searchBarToggle.contains(event.target)) {
                        searchBox.classList.remove('search-bar-show');
                    }
                }
            }
        }, { passive: true });
    }

    const chartButtons = document.querySelectorAll('.chart-btn');
    chartButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            chartButtons.forEach(btn => {
                btn.classList.remove('active');
                btn.setAttribute('aria-pressed', 'false');
            });
            this.classList.add('active');
            this.setAttribute('aria-pressed', 'true');
            
            const ripple = document.createElement('span');
            ripple.style.cssText = `
                position: absolute;
                border-radius: 50%;
                background: rgba(255, 255, 255, 0.6);
                transform: scale(0);
                animation: ripple 0.6s ease-out;
                pointer-events: none;
                width: 20px;
                height: 20px;
                left: 50%;
                top: 50%;
                margin-left: -10px;
                margin-top: -10px;
            `;
            this.style.position = 'relative';
            this.style.overflow = 'hidden';
            this.appendChild(ripple);
            
            setTimeout(() => ripple.remove(), 600);
        }, { passive: false });
    });
    
    if (!document.getElementById('ripple-animation')) {
        const style = document.createElement('style');
        style.id = 'ripple-animation';
        style.textContent = `
            @keyframes ripple {
                to {
                    transform: scale(4);
                    opacity: 0;
                }
            }
        `;
        document.head.appendChild(style);
    }

    initRevenueChart();
    initActivityChart();
    initTrafficChart();
    initSalesPieChart();
    initGrowthChart();
    initCategoryChart();
    initComparisonChart();
});

function initRevenueChart() {
    const ctx = document.getElementById('revenueChart');
    if (!ctx) return;

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
            datasets: [{
                label: 'Revenue',
                data: [3200, 4500, 3800, 5200, 4900, 6100, 5800],
                borderColor: '#007aff',
                backgroundColor: 'rgba(0, 122, 255, 0.1)',
                borderWidth: 3,
                fill: true,
                tension: 0.4,
                pointRadius: 4,
                pointBackgroundColor: '#007aff',
                pointBorderColor: '#ffffff',
                pointBorderWidth: 2,
                pointHoverRadius: 6,
                pointHoverBackgroundColor: '#007aff',
                pointHoverBorderColor: '#ffffff',
                pointHoverBorderWidth: 3
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.85)',
                    padding: 12,
                    titleColor: '#ffffff',
                    bodyColor: '#ffffff',
                    titleFont: {
                        size: 14,
                        weight: 'bold'
                    },
                    bodyFont: {
                        size: 13
                    },
                    borderColor: 'rgba(255, 255, 255, 0.1)',
                    borderWidth: 1,
                    displayColors: false,
                    callbacks: {
                        label: function(context) {
                            return '$' + context.parsed.y.toLocaleString();
                        }
                    }
                }
            },
            scales: {
                x: {
                    grid: {
                        display: false
                    },
                    ticks: {
                        color: 'rgba(0, 0, 0, 0.5)',
                        font: {
                            size: 12
                        }
                    }
                },
                y: {
                    grid: {
                        color: 'rgba(0, 0, 0, 0.06)',
                        drawBorder: false
                    },
                    ticks: {
                        color: 'rgba(0, 0, 0, 0.5)',
                        font: {
                            size: 12
                        },
                        callback: function(value) {
                            return '$' + value + 'k';
                        }
                    }
                }
            }
        }
    });
}

function initActivityChart() {
    const ctx = document.getElementById('activityChart');
    if (!ctx) return;

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
            datasets: [{
                label: 'Active Users',
                data: [420, 580, 510, 670, 720, 890, 760],
                backgroundColor: '#5856d6',
                borderColor: '#5856d6',
                borderWidth: 0,
                borderRadius: 8,
                borderSkipped: false,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.85)',
                    padding: 12,
                    titleColor: '#ffffff',
                    bodyColor: '#ffffff',
                    titleFont: {
                        size: 14,
                        weight: 'bold'
                    },
                    bodyFont: {
                        size: 13
                    },
                    borderColor: 'rgba(255, 255, 255, 0.1)',
                    borderWidth: 1,
                    displayColors: false,
                    callbacks: {
                        label: function(context) {
                            return context.parsed.y + ' users';
                        }
                    }
                }
            },
            scales: {
                x: {
                    grid: {
                        display: false
                    },
                    ticks: {
                        color: 'rgba(0, 0, 0, 0.5)',
                        font: {
                            size: 12
                        }
                    }
                },
                y: {
                    grid: {
                        color: 'rgba(0, 0, 0, 0.06)',
                        drawBorder: false
                    },
                    ticks: {
                        color: 'rgba(0, 0, 0, 0.5)',
                        font: {
                            size: 12
                        },
                        stepSize: 200
                    }
                }
            }
        }
    });
}

function initTrafficChart() {
    const ctx = document.getElementById('trafficChart');
    if (!ctx) return;

    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['Organic Search', 'Direct', 'Social Media', 'Referral', 'Email'],
            datasets: [{
                data: [35, 28, 20, 12, 5],
                backgroundColor: [
                    '#007aff',
                    '#5856d6',
                    '#af52de',
                    '#ff2d55',
                    '#ff9500'
                ],
                borderColor: [
                    '#ffffff',
                    '#ffffff',
                    '#ffffff',
                    '#ffffff',
                    '#ffffff'
                ],
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        color: 'rgba(0, 0, 0, 0.7)',
                        padding: 15,
                        font: {
                            size: 12
                        },
                        usePointStyle: true,
                        pointStyle: 'circle'
                    }
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.85)',
                    padding: 12,
                    titleColor: '#ffffff',
                    bodyColor: '#ffffff',
                    titleFont: {
                        size: 14,
                        weight: 'bold'
                    },
                    bodyFont: {
                        size: 13
                    },
                    borderColor: 'rgba(255, 255, 255, 0.1)',
                    borderWidth: 1,
                    callbacks: {
                        label: function(context) {
                            const label = context.label || '';
                            const value = context.parsed || 0;
                            return label + ': ' + value + '%';
                        }
                    }
                }
            },
            cutout: '70%'
        }
    });
}

const progressObserverOptions = {
    threshold: 0.3,
    rootMargin: '0px'
};

const progressObserver = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting && !entry.target.dataset.animated) {
            entry.target.dataset.animated = 'true';
            const progressFill = entry.target.querySelector('.progress-fill');
            if (progressFill) {
                const width = progressFill.style.width || progressFill.getAttribute('data-width') || '0%';
                progressFill.setAttribute('data-width', width);
                progressFill.style.width = '0%';
                progressFill.style.opacity = '0';
                
                requestAnimationFrame(() => {
                    setTimeout(() => {
                        progressFill.style.transition = 'width 0.8s cubic-bezier(0.19, 1, 0.22, 1), opacity 0.3s ease';
                        progressFill.style.width = width;
                        progressFill.style.opacity = '1';
                    }, 150);
                });
            }
        }
    });
}, progressObserverOptions);

document.querySelectorAll('.product-item').forEach(item => {
    progressObserver.observe(item);
});

let scrollTimeout;
const optimizedScrollHandler = () => {
    if (scrollTimeout) {
        cancelAnimationFrame(scrollTimeout);
    }
    scrollTimeout = requestAnimationFrame(() => {
    });
};

window.addEventListener('scroll', optimizedScrollHandler, { passive: true });

document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        const href = this.getAttribute('href');
        if (href === '#') return;
        
        e.preventDefault();
        const target = document.querySelector(href);
        if (target) {
            const offset = 80;
            const targetPosition = target.getBoundingClientRect().top + window.pageYOffset - offset;
            
            window.scrollTo({
                top: targetPosition,
                behavior: 'smooth'
            });
        }
    }, { passive: false });
});

const scrollToTop = () => {
    window.scrollTo({
        top: 0,
        behavior: 'smooth'
    });
};

const addScrollToTop = () => {
    const button = document.createElement('button');
    button.innerHTML = '<i class="fa-solid fa-arrow-up"></i>';
    button.className = 'scroll-to-top';
    button.style.cssText = `
        position: fixed;
        bottom: 2rem;
        right: 2rem;
        width: 48px;
        height: 48px;
        border-radius: 50%;
        background: var(--primary-color);
        color: white;
        border: none;
        cursor: pointer;
        display: none;
        align-items: center;
        justify-content: center;
        box-shadow: 0 4px 12px rgba(0, 122, 255, 0.3);
        z-index: 999;
        transition: all 0.3s ease;
        opacity: 0;
        transform: translateY(20px);
    `;
    button.setAttribute('aria-label', 'Scroll to top');
    document.body.appendChild(button);
    
    let ticking = false;
    const handleScroll = () => {
        if (!ticking) {
            window.requestAnimationFrame(() => {
                const scrollY = window.scrollY || window.pageYOffset;
                if (scrollY > 300) {
                    button.style.display = 'flex';
                    setTimeout(() => {
                        button.style.opacity = '1';
                        button.style.transform = 'translateY(0)';
                    }, 10);
                } else {
                    button.style.opacity = '0';
                    button.style.transform = 'translateY(20px)';
                    setTimeout(() => {
                        if (window.scrollY < 300) {
                            button.style.display = 'none';
                        }
                    }, 300);
                }
                ticking = false;
            });
            ticking = true;
        }
    };
    
    window.addEventListener('scroll', handleScroll, { passive: true });
    button.addEventListener('click', scrollToTop);
    
    button.addEventListener('mouseenter', () => {
        button.style.transform = 'translateY(-4px) scale(1.1)';
        button.style.boxShadow = '0 6px 20px rgba(0, 122, 255, 0.4)';
    });
    
    button.addEventListener('mouseleave', () => {
        button.style.transform = 'translateY(0) scale(1)';
        button.style.boxShadow = '0 4px 12px rgba(0, 122, 255, 0.3)';
    });
};

addScrollToTop();

function animateValue(element, start, end, duration) {
    let startTimestamp = null;
    
    const easeOutCubic = (t) => {
        return 1 - Math.pow(1 - t, 3);
    };
    
    const step = (timestamp) => {
        if (!startTimestamp) startTimestamp = timestamp;
        const elapsed = timestamp - startTimestamp;
        const progress = Math.min(elapsed / duration, 1);
        const easedProgress = easeOutCubic(progress);
        const current = Math.floor(easedProgress * (end - start) + start);
        
        const originalText = element.dataset.originalText || element.textContent;
        const hasDollar = originalText.includes('$');
        const hasPercent = originalText.includes('%');
        
        if (hasDollar) {
            element.textContent = '$' + current.toLocaleString();
        } else if (hasPercent) {
            const decimalValue = (easedProgress * (end - start) + start).toFixed(2);
            element.textContent = decimalValue + '%';
        } else {
            element.textContent = current.toLocaleString();
        }
        
        if (progress < 1) {
            window.requestAnimationFrame(step);
        } else {
            if (hasDollar) {
                element.textContent = '$' + end.toLocaleString();
            } else if (hasPercent) {
                element.textContent = end.toFixed(2) + '%';
            } else {
                element.textContent = end.toLocaleString();
            }
        }
    };
    window.requestAnimationFrame(step);
}

const animateStatsOnScroll = () => {
    const statValues = document.querySelectorAll('.stat-value');
    const observerOptions = {
        threshold: 0.5,
        rootMargin: '0px'
    };
    
    const statObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting && !entry.target.dataset.animated) {
                entry.target.dataset.animated = 'true';
                const finalValue = entry.target.textContent;
                entry.target.dataset.originalText = finalValue;
                let numericValue = parseFloat(finalValue.replace(/[^0-9.]/g, ''));
                
                if (!isNaN(numericValue)) {
                    entry.target.textContent = '0';
                    setTimeout(() => {
                        animateValue(entry.target, 0, numericValue, 2000);
                    }, 200);
                }
            }
        });
    }, observerOptions);
    
    statValues.forEach(stat => {
        statObserver.observe(stat);
    });
};

animateStatsOnScroll();

function initSalesPieChart() {
    const ctx = document.getElementById('salesPieChart');
    if (!ctx) return;

    new Chart(ctx, {
        type: 'pie',
        data: {
            labels: ['Electronics', 'Clothing', 'Books', 'Accessories', 'Other'],
            datasets: [{
                data: [35, 28, 20, 12, 5],
                backgroundColor: [
                    '#007aff',
                    '#5856d6',
                    '#af52de',
                    '#ff2d55',
                    '#ff9500'
                ],
                borderColor: '#ffffff',
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        color: 'rgba(0, 0, 0, 0.7)',
                        padding: 15,
                        font: { size: 12 },
                        usePointStyle: true
                    }
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.85)',
                    titleColor: '#ffffff',
                    bodyColor: '#ffffff',
                    callbacks: {
                        label: function(context) {
                            return context.label + ': ' + context.parsed + '%';
                        }
                    }
                }
            }
        }
    });
}

function initGrowthChart() {
    const ctx = document.getElementById('growthChart');
    if (!ctx) return;

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
            datasets: [{
                label: 'Sales',
                data: [12000, 19000, 15000, 25000, 22000, 30000],
                borderColor: '#007aff',
                backgroundColor: 'rgba(0, 122, 255, 0.2)',
                borderWidth: 3,
                fill: true,
                tension: 0.4
            }, {
                label: 'Revenue',
                data: [18000, 24000, 20000, 30000, 28000, 35000],
                borderColor: '#10b981',
                backgroundColor: 'rgba(16, 185, 129, 0.2)',
                borderWidth: 3,
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        color: 'rgba(0, 0, 0, 0.7)',
                        padding: 15,
                        font: { size: 12 }
                    }
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.85)',
                    titleColor: '#ffffff',
                    bodyColor: '#ffffff'
                }
            },
            scales: {
                x: {
                    grid: { display: false },
                    ticks: { color: 'rgba(0, 0, 0, 0.5)' }
                },
                y: {
                    grid: { color: 'rgba(0, 0, 0, 0.06)' },
                    ticks: { 
                        color: 'rgba(0, 0, 0, 0.5)',
                        callback: function(value) { return '$' + (value/1000) + 'k'; }
                    }
                }
            }
        }
    });
}

function initCategoryChart() {
    const ctx = document.getElementById('categoryChart');
    if (!ctx) return;

    new Chart(ctx, {
        type: 'radar',
        data: {
            labels: ['Sales', 'Revenue', 'Users', 'Engagement', 'Retention', 'Satisfaction'],
            datasets: [{
                label: 'Electronics',
                data: [85, 90, 75, 80, 85, 88],
                borderColor: '#007aff',
                backgroundColor: 'rgba(0, 122, 255, 0.2)',
                borderWidth: 2
            }, {
                label: 'Clothing',
                data: [70, 75, 85, 90, 75, 82],
                borderColor: '#5856d6',
                backgroundColor: 'rgba(88, 86, 214, 0.2)',
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        color: 'rgba(0, 0, 0, 0.7)',
                        padding: 15,
                        font: { size: 12 }
                    }
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.85)',
                    titleColor: '#ffffff',
                    bodyColor: '#ffffff'
                }
            },
            scales: {
                r: {
                    beginAtZero: true,
                    max: 100,
                    ticks: {
                        color: 'rgba(0, 0, 0, 0.5)',
                        backdropColor: 'transparent'
                    },
                    grid: {
                        color: 'rgba(0, 0, 0, 0.1)'
                    },
                    pointLabels: {
                        color: 'rgba(0, 0, 0, 0.7)',
                        font: { size: 11 }
                    }
                }
            }
        }
    });
}

function initComparisonChart() {
    const ctx = document.getElementById('comparisonChart');
    if (!ctx) return;

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
            datasets: [{
                label: 'Last Year',
                data: [15000, 18000, 20000, 22000, 24000, 26000],
                backgroundColor: 'rgba(0, 0, 0, 0.1)',
                borderColor: 'rgba(0, 0, 0, 0.3)',
                borderWidth: 1,
                borderRadius: 8
            }, {
                label: 'This Year',
                data: [18000, 24000, 20000, 30000, 28000, 35000],
                backgroundColor: '#007aff',
                borderColor: '#007aff',
                borderWidth: 0,
                borderRadius: 8
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        color: 'rgba(0, 0, 0, 0.7)',
                        padding: 15,
                        font: { size: 12 }
                    }
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.85)',
                    titleColor: '#ffffff',
                    bodyColor: '#ffffff'
                }
            },
            scales: {
                x: {
                    grid: { display: false },
                    ticks: { color: 'rgba(0, 0, 0, 0.5)' }
                },
                y: {
                    grid: { color: 'rgba(0, 0, 0, 0.06)' },
                    ticks: { 
                        color: 'rgba(0, 0, 0, 0.5)',
                        callback: function(value) { return '$' + (value/1000) + 'k'; }
                    }
                }
            }
        }
    });
}

