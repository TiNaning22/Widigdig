<?php
// File: views/pages/home/privatecourses.php
require_once dirname(__FILE__) . '/../../../controllers/KelasController.php';
$kelasController = new KelasController();

try {
    $courses = $kelasController->showPrivateKelas();
} catch (Exception $e) {
    $courses = [];
    error_log("Gagal mengambil kursus private: " . $e->getMessage());
}
?>

<style>
.private-courses {
    padding: 2rem 0;
    background: #fff;
}

.private-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 1rem;
}

.private-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
}

.private-title {
    font-size: 1.5rem;
    font-weight: 600;
    color: #333;
}

.private-see-all {
    display: flex;
    align-items: center;
    color: #0066cc;
    text-decoration: none;
    font-weight: 500;
}

.arrow-icon {
    margin-left: 0.5rem;
}

.private-cards-wrapper {
    position: relative;
    overflow: hidden;
    width: 100%;
}

.private-cards-container {
    display: flex;
    gap: 1rem;
    transition: transform 0.3s ease;
}

.private-card {
    flex: 0 0 calc(25% - 0.75rem); /* Show 4 cards per view */
    min-width: calc(25% - 0.75rem);
    background: #fff;
    border: 1px solid #e0e0e0;
    border-radius: 8px;
    overflow: hidden;
    transition: transform 0.3s ease;
}

.private-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.private-card-link {
    text-decoration: none;
    color: inherit;
}

.private-card-image {
    position: relative;
    padding-top: 56.25%; /* 16:9 aspect ratio */
    overflow: hidden;
}

.private-card-image img {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.private-card-content {
    padding: 1rem;
}

.private-course-title {
    font-size: 1rem;
    font-weight: 600;
    margin-bottom: 0.5rem;
    color: #333;
}

.private-instructor-name {
    font-size: 0.875rem;
    color: #666;
    margin-bottom: 0.5rem;
}

.private-price {
    font-weight: 600;
    color: #0066cc;
    margin-bottom: 0.5rem;
}

.private-students {
    font-size: 0.875rem;
    color: #666;
    margin-bottom: 0.25rem;
}

.private-status {
    font-size: 0.875rem;
    color: #22c55e;
}

.no-courses-message {
    text-align: center;
    padding: 2rem;
    color: #666;
}

/* Responsive design */
@media (max-width: 1024px) {
  .private-card {
    flex: 0 0 calc(33.333% - 1rem);
    max-width: calc(33.333% - 1rem);
  }
}

@media (max-width: 768px) {
  .private-header {
    flex-direction: column;
    align-items: flex-start;
  }

  .private-title {
    font-size: 1.25rem;
  }

  .private-card {
    flex: 0 0 calc(50% - 1rem);
    max-width: calc(50% - 1rem);
  }

  .private-card-content {
    padding: 0.75rem;
  }

  .private-course-title {
    font-size: 0.9rem;
  }

  .private-price {
    font-size: 1rem;
  }
}

@media (max-width: 480px) {
  .private-container {
    padding: 0 1rem;
  }

  .private-cards-wrapper {
    display: flex;
    flex-wrap: nowrap;
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
    gap: 1rem;
    padding: 1rem 1rem 1.5rem 1rem;
    margin: 0 -1rem;
    scroll-padding: 0 1rem;
    scroll-snap-type: x proximity;
  }

  .private-card {
    flex: 0 0 80%;
    max-width: 80%;
    scroll-snap-align: start;
    margin-right: 0;
  }

  .private-cards-wrapper {
    scrollbar-width: thin;
    scrollbar-color: #cbd5e0 #f8f9fa;
  }

  .private-cards-wrapper::-webkit-scrollbar {
    display: block;
    height: 6px;
  }

  .private-cards-wrapper::-webkit-scrollbar-track {
    background: #f8f9fa;
    border-radius: 3px;
  }

  .private-cards-wrapper::-webkit-scrollbar-thumb {
    background-color: #cbd5e0;
    border-radius: 3px;
  }

  .private-card-content {
    padding: 1.25rem;
  }

  .private-header {
    padding: 0 1rem;
    margin-bottom: 1.5rem;
  }

  .private-course-title {
    font-size: 1rem;
  }

  .private-price {
    font-size: 1.1rem;
  }

  .private-card-image img {
    height: 180px;
  }
}
</style>

<section class="private-courses">
    <div class="private-container">
        <div class="private-header fade-up">
            <h2 class="private-title">Kursus Private</h2>
            <a href="#" class="private-see-all">
                Lihat semua kursus
                <span class="arrow-icon">→</span>
            </a>
        </div>
        
        <div class="private-cards-wrapper fade-in">
            <div class="private-cards-container">
                <?php if (!empty($courses)): ?>
                    <?php foreach ($courses as $course): ?>
                        <div class="private-card">
                            <a href="/views/pages/checkout/index.php?id=<?php echo urlencode($course['id']); ?>" class="private-card-link">
                                <div class="private-card-image">
                                    <?php
                                    if (!empty($course['image'])) {
                                        $filename = basename($course['image']);
                                        $imagePath = "/public/image-class/" . $filename;
                                    } else {
                                        $imagePath = '/assets/images/default-course.svg';
                                    }
                                    ?>
                                    <img 
                                        src="<?php echo htmlspecialchars($imagePath); ?>" 
                                        alt="Gambar Kelas <?php echo isset($course['name']) ? htmlspecialchars($course['name']) : 'Tidak diketahui'; ?>"
                                        onerror="this.onerror=null; this.src='/assets/images/default-course.svg';"
                                    >
                                </div>
                                <div class="private-card-content">
                                    <h3 class="private-course-title"><?php echo isset($course['name']) ? htmlspecialchars($course['name']) : 'Nama tidak tersedia'; ?></h3>
                                    <p class="private-instructor-name"><?php echo isset($course['name_mentor']) ? htmlspecialchars($course['name_mentor']) : 'Tidak diketahui'; ?></p>
                                    <p class="private-price">Rp <?php echo isset($course['price']) ? number_format($course['price'], 0, ',', '.') : '0'; ?></p>
                                    <div class="private-students">Kuota: <?php echo isset($course['quota_left']) ? $course['quota_left'] : '0'; ?></div>
                                    <p class="private-status">Status: <?php echo isset($course['status']) ? htmlspecialchars($course['status']) : 'Tidak tersedia'; ?></p>
                                </div>
                            </a>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="no-courses-message">Tidak ada kursus private tersedia saat ini. Silakan cek kembali nanti.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const cardsContainer = document.querySelector('.private-cards-container');
    const cardsWrapper = document.querySelector('.private-cards-wrapper');
    const cards = document.querySelectorAll('.private-card');
    
    if (cards.length <= 4) return; // Don't initialize scroll if 4 or fewer cards
    
    let currentIndex = 0;
    const cardWidth = cards[0].offsetWidth;
    const visibleCards = 4;
    
    // Auto scroll
    const autoScroll = setInterval(() => {
        currentIndex = (currentIndex + 1) % (cards.length - visibleCards + 1);
        scrollToIndex(currentIndex);
    }, 3000);
    
    function scrollToIndex(index) {
        const scrollAmount = index * cardWidth;
        cardsContainer.style.transform = `translateX(-${scrollAmount}px)`;
    }
    
    // Touch handling
    let touchStartX = 0;
    let touchEndX = 0;
    
    cardsWrapper.addEventListener('touchstart', (e) => {
        touchStartX = e.changedTouches[0].screenX;
        clearInterval(autoScroll);
    });
    
    cardsWrapper.addEventListener('touchend', (e) => {
        touchEndX = e.changedTouches[0].screenX;
        handleSwipe();
    });
    
    function handleSwipe() {
        const swipeThreshold = 50;
        
        if (touchStartX - touchEndX > swipeThreshold) {
            // Swipe left
            if (currentIndex < cards.length - visibleCards) {
                currentIndex++;
                scrollToIndex(currentIndex);
            }
        } else if (touchEndX - touchStartX > swipeThreshold) {
            // Swipe right
            if (currentIndex > 0) {
                currentIndex--;
                scrollToIndex(currentIndex);
            }
        }
    }
});
</script>