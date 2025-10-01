// Advanced animation functionality
class AnimationController {
    constructor() {
        this.observers = new Map();
        this.animations = new Map();
        this.init();
    }

    init() {
        this.setupScrollAnimations();
        this.setupParallaxEffects();
        this.setupHoverAnimations();
        this.setupPageTransitions();
    }

    setupScrollAnimations() {
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const scrollObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    this.triggerAnimation(entry.target);
                }
            });
        }, observerOptions);

        // Observe elements with animation classes
        const animatedElements = document.querySelectorAll('[class*="animate-"]');
        animatedElements.forEach(el => {
            scrollObserver.observe(el);
        });

        this.observers.set('scroll', scrollObserver);
    }

    setupParallaxEffects() {
        const parallaxElements = document.querySelectorAll('.parallax');
        
        window.addEventListener('scroll', throttle(() => {
            const scrolled = window.pageYOffset;
            const rate = scrolled * -0.5;
            
            parallaxElements.forEach(el => {
                el.style.transform = `translateY(${rate}px)`;
            });
        }, 16));
    }

    setupHoverAnimations() {
        // Add hover effects to interactive elements
        const hoverElements = document.querySelectorAll('.hover-lift, .hover-scale, .hover-rotate');
        
        hoverElements.forEach(el => {
            el.addEventListener('mouseenter', () => {
                this.addHoverClass(el);
            });
            
            el.addEventListener('mouseleave', () => {
                this.removeHoverClass(el);
            });
        });
    }

    setupPageTransitions() {
        // Add page transition effects
        const links = document.querySelectorAll('a[href$=".html"]');
        
        links.forEach(link => {
            link.addEventListener('click', (e) => {
                e.preventDefault();
                this.transitionToPage(link.href);
            });
        });
    }

    triggerAnimation(element) {
        const animationClass = Array.from(element.classList).find(cls => cls.startsWith('animate-'));
        if (animationClass) {
            element.classList.add(animationClass);
        }
    }

    addHoverClass(element) {
        element.style.transition = 'all 0.3s ease';
    }

    removeHoverClass(element) {
        // Reset any hover-specific styles
    }

    transitionToPage(url) {
        // Create transition overlay
        const overlay = document.createElement('div');
        overlay.className = 'page-transition-overlay';
        overlay.innerHTML = '<div class="transition-spinner"></div>';
        
        document.body.appendChild(overlay);
        
        // Animate in
        requestAnimationFrame(() => {
            overlay.classList.add('active');
        });
        
        // Navigate after animation
        setTimeout(() => {
            window.location.href = url;
        }, 500);
    }

    // Advanced animation methods
    createStaggerAnimation(elements, delay = 100) {
        elements.forEach((el, index) => {
            setTimeout(() => {
                el.classList.add('animate-slide-up');
            }, index * delay);
        });
    }

    createTypewriterEffect(element, text, speed = 100) {
        element.textContent = '';
        let i = 0;
        
        const typeInterval = setInterval(() => {
            if (i < text.length) {
                element.textContent += text.charAt(i);
                i++;
            } else {
                clearInterval(typeInterval);
            }
        }, speed);
    }

    createProgressAnimation(element, targetValue, duration = 2000) {
        const startValue = 0;
        const startTime = performance.now();
        
        const animate = (currentTime) => {
            const elapsed = currentTime - startTime;
            const progress = Math.min(elapsed / duration, 1);
            
            const currentValue = startValue + (targetValue - startValue) * this.easeOutCubic(progress);
            element.style.width = `${currentValue}%`;
            
            if (progress < 1) {
                requestAnimationFrame(animate);
            }
        };
        
        requestAnimationFrame(animate);
    }

    easeOutCubic(t) {
        return 1 - Math.pow(1 - t, 3);
    }

    // Particle system for hero section
    createParticleSystem(container) {
        const particleCount = 50;
        const particles = [];
        
        for (let i = 0; i < particleCount; i++) {
            const particle = document.createElement('div');
            particle.className = 'particle';
            particle.style.cssText = `
                position: absolute;
                width: 2px;
                height: 2px;
                background: rgba(255, 255, 255, 0.5);
                border-radius: 50%;
                left: ${Math.random() * 100}%;
                top: ${Math.random() * 100}%;
                animation: float ${5 + Math.random() * 10}s ease-in-out infinite;
                animation-delay: ${Math.random() * 5}s;
            `;
            
            container.appendChild(particle);
            particles.push(particle);
        }
        
        return particles;
    }

    // Chart animation
    animateChartBars(bars) {
        bars.forEach((bar, index) => {
            setTimeout(() => {
                bar.classList.add('chart-bar');
            }, index * 50);
        });
    }

    // Number counter with easing
    animateCounter(element, target, duration = 2000) {
        const startValue = 0;
        const startTime = performance.now();
        
        const animate = (currentTime) => {
            const elapsed = currentTime - startTime;
            const progress = Math.min(elapsed / duration, 1);
            
            const currentValue = Math.floor(startValue + (target - startValue) * this.easeOutQuart(progress));
            element.textContent = currentValue.toLocaleString();
            
            if (progress < 1) {
                requestAnimationFrame(animate);
            }
        };
        
        requestAnimationFrame(animate);
    }

    easeOutQuart(t) {
        return 1 - Math.pow(1 - t, 4);
    }

    // Text reveal animation
    revealText(element) {
        const text = element.textContent;
        element.innerHTML = '';
        
        for (let i = 0; i < text.length; i++) {
            const span = document.createElement('span');
            span.textContent = text[i];
            span.style.cssText = `
                opacity: 0;
                transform: translateY(20px);
                animation: revealChar 0.5s ease forwards;
                animation-delay: ${i * 0.05}s;
            `;
            element.appendChild(span);
        }
    }

    // Cleanup method
    destroy() {
        this.observers.forEach(observer => observer.disconnect());
        this.observers.clear();
        this.animations.clear();
    }
}

// Initialize animation controller when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    window.animationController = new AnimationController();
    
    // Add custom CSS for animations
    const style = document.createElement('style');
    style.textContent = `
        .page-transition-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, #00d4aa, #00b894);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 9999;
            opacity: 0;
            transition: opacity 0.5s ease;
        }
        
        .page-transition-overlay.active {
            opacity: 1;
        }
        
        .transition-spinner {
            width: 50px;
            height: 50px;
            border: 4px solid rgba(255, 255, 255, 0.3);
            border-top: 4px solid white;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }
        
        .particle {
            pointer-events: none;
        }
        
        @keyframes revealChar {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
    `;
    document.head.appendChild(style);
});

// Utility functions
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

function debounce(func, wait, immediate) {
    let timeout;
    return function() {
        const context = this, args = arguments;
        const later = function() {
            timeout = null;
            if (!immediate) func.apply(context, args);
        };
        const callNow = immediate && !timeout;
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
        if (callNow) func.apply(context, args);
    };
}
