<?php
// File: views/pages/checkout/learn.php
// Gunakan path absolut atau dirname untuk mencari path controller
require_once dirname(__DIR__, 3) . '/controllers/BookController.php';
require_once dirname(__DIR__, 3) . '/models/BookModel.php';
require_once dirname(__DIR__, 3) . '/services/database.php';

$bookController = new BookController();
$books = $bookController->getAllBooks();
?>

<h1 class="title">Buku</h1>
<section class="private-courses">
  <div class="private-container">
    <div class="private-cards-wrapper">
      <?php foreach ($books as $book): ?>
        <div class="private-card">
          <div class="private-card-image">
            <?php
            $imagePath = str_replace('../public', '/public', htmlspecialchars($book['image']));
            if (file_exists($_SERVER['DOCUMENT_ROOT'] . $imagePath)) {
              $imageUrl = $imagePath;
            } else {
              $imageUrl = '/public/image-book/6752af8606088_commandermewing.png'; // Fallback image
            }
            ?>

            <img src="<?php echo $imageUrl; ?>" alt="Book Thumbnail">
          </div>
          <div class="private-card-content">
            <h3 class="private-course-title"><?php echo htmlspecialchars($book['title']); ?></h3>
            <p class="private-instructor-name"><?php echo htmlspecialchars($book['description']); ?></p>
            <div class="private-card-stats">
              <div class="private-students"><?php echo $book['rating']; ?> Rating</div>
              <div class="private-avatars">
              </div>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<script>
  document.addEventListener('DOMContentLoaded', function() {
    const privateCardsWrapper = document.querySelector('.private-cards-wrapper');
    let scrollAmount = 0;
    const cardWidth = document.querySelector('.private-card').offsetWidth + 20;

    setInterval(() => {
      if (scrollAmount < privateCardsWrapper.scrollWidth - privateCardsWrapper.offsetWidth) {
        scrollAmount += cardWidth;
        privateCardsWrapper.scrollTo({
          left: scrollAmount,
          behavior: 'smooth'
        });
      } else {
        scrollAmount = 0;
        privateCardsWrapper.scrollTo({
          left: scrollAmount,
          behavior: 'smooth'
        });
      }
    }, 3000);

    // Fitur swipe di perangkat mobile
    let touchStartX = 0;
    let touchEndX = 0;

    privateCardsWrapper.addEventListener('touchstart', (e) => {
      touchStartX = e.changedTouches[0].screenX;
    });

    privateCardsWrapper.addEventListener('touchend', (e) => {
      touchEndX = e.changedTouches[0].screenX;
      handleSwipe();
    });

    function handleSwipe() {
      if (touchStartX - touchEndX > 50) {
        // Geser ke kanan
        if (scrollAmount < privateCardsWrapper.scrollWidth - privateCardsWrapper.offsetWidth) {
          scrollAmount += cardWidth;
          privateCardsWrapper.scrollTo({
            left: scrollAmount,
            behavior: 'smooth'
          });
        }
      }

      if (touchEndX - touchStartX > 50) {
        // Geser ke kiri
        if (scrollAmount > 0) {
          scrollAmount -= cardWidth;
          privateCardsWrapper.scrollTo({
            left: scrollAmount,
            behavior: 'smooth'
          });
        }
      }
    }
  });
</script>