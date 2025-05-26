// private-courses.js
document.addEventListener('DOMContentLoaded', function() {
  const carousel = document.getElementById('courseCarousel');
  if (!carousel) return;

  let isScrolling = false;
  let startX;
  let scrollLeft;

  // Touch events for mobile
  carousel.addEventListener('touchstart', (e) => {
      isScrolling = true;
      startX = e.touches[0].pageX - carousel.offsetLeft;
      scrollLeft = carousel.scrollLeft;
  });

  carousel.addEventListener('touchmove', (e) => {
      if (!isScrolling) return;
      e.preventDefault();
      const x = e.touches[0].pageX - carousel.offsetLeft;
      const walk = (x - startX) * 2;
      carousel.scrollLeft = scrollLeft - walk;
  });

  carousel.addEventListener('touchend', () => {
      isScrolling = false;
  });

  // Mouse events for desktop
  carousel.addEventListener('mousedown', (e) => {
      isScrolling = true;
      startX = e.pageX - carousel.offsetLeft;
      scrollLeft = carousel.scrollLeft;
  });

  carousel.addEventListener('mousemove', (e) => {
      if (!isScrolling) return;
      e.preventDefault();
      const x = e.pageX - carousel.offsetLeft;
      const walk = (x - startX) * 2;
      carousel.scrollLeft = scrollLeft - walk;
  });

  carousel.addEventListener('mouseup', () => {
      isScrolling = false;
  });

  carousel.addEventListener('mouseleave', () => {
      isScrolling = false;
  });

  // Prevent click events while scrolling
  let isDragging = false;
  carousel.addEventListener('mousedown', () => {
      isDragging = false;
  });

  carousel.addEventListener('mousemove', () => {
      isDragging = true;
  });

  carousel.addEventListener('click', (e) => {
      if (isDragging) {
          e.preventDefault();
      }
  });

  // Optional: Arrow key navigation
  document.addEventListener('keydown', (e) => {
      if (!carousel.matches(':hover')) return;
      
      const scrollAmount = 300;
      if (e.key === 'ArrowLeft') {
          carousel.scrollBy({
              left: -scrollAmount,
              behavior: 'smooth'
          });
      } else if (e.key === 'ArrowRight') {
          carousel.scrollBy({
              left: scrollAmount,
              behavior: 'smooth'
          });
      }
  });
});