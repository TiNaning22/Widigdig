document.addEventListener('DOMContentLoaded', function() {
    const slideContainer = document.querySelector('.slide-container');
    const slides = document.querySelectorAll('.slide');
    let currentSlide = 0;
    let slideInterval;
    const intervalTime = 3000;
    
    function initSlides() {
        slides.forEach((slide, index) => {
            slide.style.opacity = index === 0 ? '1' : '0';
            slide.style.position = 'absolute';
            slide.style.top = '0';
            slide.style.left = '0';
            slide.style.width = '100%';
            slide.style.height = '100%';
            slide.style.transition = 'opacity 0.5s ease-in-out';
        });
    }

    function goToSlide(n) {
        slides.forEach(slide => {
            slide.style.opacity = '0';
        });
        
        currentSlide = n >= slides.length ? 0 : n;
        
        slides[currentSlide].style.opacity = '1';
    }

    function nextSlide() {
        goToSlide(currentSlide + 1);
    }

    function startSlideshow() {
        if (slideInterval) {
            clearInterval(slideInterval);
        }
        slideInterval = setInterval(nextSlide, intervalTime);
    }

    let touchStartX = 0;
    let touchEndX = 0;

    slideContainer.addEventListener('touchstart', (e) => {
        touchStartX = e.changedTouches[0].screenX;
        clearInterval(slideInterval);
    });

    slideContainer.addEventListener('touchend', (e) => {
        touchEndX = e.changedTouches[0].screenX;
        handleSwipe();
        startSlideshow();
    });

    function handleSwipe() {
        const swipeThreshold = 50;
        
        if (touchStartX - touchEndX > swipeThreshold) {
            goToSlide(currentSlide + 1);
        }
        
        if (touchEndX - touchStartX > swipeThreshold) {
            goToSlide(currentSlide > 0 ? currentSlide - 1 : slides.length - 1);
        }
    }
    slideContainer.addEventListener('mouseenter', () => {
        clearInterval(slideInterval);
    });

    slideContainer.addEventListener('mouseleave', startSlideshow);
    initSlides();
    startSlideshow();
});
