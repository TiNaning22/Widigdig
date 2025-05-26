<?php
require_once dirname(__DIR__, 3) . '/controllers/BookController.php';
require_once dirname(__DIR__, 3) . '/models/BookModel.php';
require_once dirname(__DIR__, 3) . '/services/database.php';

$bookController = new BookController();
$books = $bookController->getAllBooks();
?>

<style>
    @import url('https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700&display=swap');

    .private-courses {
        padding: 2rem 0;
        width: 100%;
    }

    .private-courses h2 {
        font-family: 'Manrope', sans-serif;
        font-weight: 600;
        margin-bottom: 2rem;
        text-align: center;
    }

    .private-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 1rem;
    }

    .private-cards-wrapper {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 2rem;
        padding: 1rem;
    }

    .private-card {
        background: white;
        border-radius: 12px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        overflow: hidden;
        transition: transform 0.3s ease;
        cursor: pointer;
        min-height: 200px;
        /* Reduced from 400px */
        display: flex;
        flex-direction: column;
    }

    .private-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
    }

    .private-card-content {
        padding: 1.5rem;
        flex-grow: 1;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }

    .private-course-title {
        font-size: 1.5rem;
        font-weight: 600;
        color: #333;
        margin-bottom: 1rem;
        line-height: 1.3;
    }

    .private-instructor-name {
        font-size: 1rem;
        color: #666;
        margin-bottom: 1rem;
        line-height: 1.5;
        flex-grow: 1;
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
        .private-cards-wrapper {
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        }

        .private-card {
            min-height: 150px;
            /* Reduced from 350px */
        }

        .private-course-title {
            font-size: 1.25rem;
        }
    }
</style>

<section class="private-courses">
    <h2>E-Book File</h2>
    <div class="private-container">
        <div class="private-cards-wrapper">
            <?php foreach ($books as $book): ?>
                <div class="private-card" data-book-id="<?php echo $book['id']; ?>" data-ebook-file="<?php echo htmlspecialchars($book['ebook_file']); ?>">
                    <div class="private-card-content">
                        <h3 class="private-course-title"><?php echo htmlspecialchars($book['title']); ?></h3>
                        <p class="private-instructor-name"><?php echo htmlspecialchars($book['description']); ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const bookCards = document.querySelectorAll('.private-card');
        bookCards.forEach(card => {
            card.addEventListener('click', function() {
                const bookId = this.getAttribute('data-book-id');

                if (bookId) {
                    window.location.href = '/views/pages/class/dowload-ebook.php?book_id=' + bookId;
                } else {
                    alert('Ebook file not available for this book.');
                }
            });
        });
    });
</script>