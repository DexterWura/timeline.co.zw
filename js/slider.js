// Billboard-style Top Charts Slider
document.addEventListener('DOMContentLoaded', function() {
    const slider = document.getElementById('topChartsSlider');
    if (!slider) return;
    
    const slides = slider.querySelectorAll('.slide');
    if (slides.length === 0) return;
    
    let currentSlide = 0;
    const totalSlides = slides.length;
    
    // Auto-advance slider
    function nextSlide() {
        currentSlide = (currentSlide + 1) % totalSlides;
        updateSlider();
    }
    
    function updateSlider() {
        const translateX = -currentSlide * 100;
        slider.style.transform = `translateX(${translateX}%)`;
    }
    
    // Navigation buttons
    const prevBtn = document.getElementById('prev-slide');
    const nextBtn = document.getElementById('next-slide');
    
    if (prevBtn) {
        prevBtn.addEventListener('click', () => {
            currentSlide = (currentSlide - 1 + totalSlides) % totalSlides;
            updateSlider();
        });
    }
    
    if (nextBtn) {
        nextBtn.addEventListener('click', () => {
            nextSlide();
        });
    }
    
    // Auto-advance every 5 seconds
    if (totalSlides > 1) {
        setInterval(nextSlide, 5000);
    }
    
    // Initialize
    updateSlider();
});

