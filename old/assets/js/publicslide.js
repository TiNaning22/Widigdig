document.addEventListener('DOMContentLoaded', function() {
    const publicCardsWrapper = document.querySelector('.public-cards-wrapper');
    let scrollAmount = 0;
    const cardWidth = document.querySelector('.public-card').offsetWidth + 20;
  

    setInterval(() => {
      if (scrollAmount < publicCardsWrapper.scrollWidth - publicCardsWrapper.offsetWidth) {
        scrollAmount += cardWidth;
        publicCardsWrapper.scrollTo({
          left: scrollAmount,
          behavior: 'smooth'
        });
      } else {
        scrollAmount = 0;
        publicCardsWrapper.scrollTo({
          left: scrollAmount,
          behavior: 'smooth'
        });
      }
    }, 5000);
  
    let touchStartX = 0;
    let touchEndX = 0;
  
    publicCardsWrapper.addEventListener('touchstart', (e) => {
      touchStartX = e.changedTouches[0].screenX;
    });
  
    publicCardsWrapper.addEventListener('touchend', (e) => {
      touchEndX = e.changedTouches[0].screenX;
      handleSwipe();
    });
  
    function handleSwipe() {
      if (touchStartX - touchEndX > 50) {
        if (scrollAmount < publicCardsWrapper.scrollWidth - publicCardsWrapper.offsetWidth) {
          scrollAmount += cardWidth;
          publicCardsWrapper.scrollTo({
            left: scrollAmount,
            behavior: 'smooth'
          });
        }
      }
  
      if (touchEndX - touchStartX > 50) {
        if (scrollAmount > 0) {
          scrollAmount -= cardWidth;
          publicCardsWrapper.scrollTo({
            left: scrollAmount,
            behavior: 'smooth'
          });
        }
      }
    }
  });
  