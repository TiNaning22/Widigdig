<?php
// File: views/pages/userprofile/userprofile.php
session_start();

// Include header component
include dirname(__FILE__) . '/../../layouts/header.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: /views/pages/login/login.php');
    exit();
}

$userId = $_SESSION['user_id'];

// Include necessary files
include dirname(__FILE__) . '/../../../controllers/UserController.php';
require_once dirname(__FILE__) . '/../../../controllers/BookController.php';
require_once dirname(__FILE__) . '/../../../models/BookModel.php';
require_once dirname(__FILE__) . '/../../../services/database.php';

$userController = new UserController();
$bookController = new BookController();

$user = $userController->getUserProfile($userId);
$books = $bookController->getAllBooks();

if (!$user) {
    session_destroy();
    header('Location: /views/pages/login/login.php');
    exit();
}

$defaultProfilePicture = 'data:image/svg+xml;base64,' . base64_encode('
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="#ddd" width="80px" height="80px">
    <circle cx="12" cy="8" r="4" />
    <path d="M12 14c-5 0-9 2.5-9 5v1h18v-1c0-2.5-4-5-9-5z"/>
</svg>
');

$profilePicture = !empty($user['profile_picture']) ? $user['profile_picture'] : $defaultProfilePicture;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/assets/css/user/user.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>
    <!-- Render the header -->
    <?php renderHeader(); ?>

    <div class="container">
        <div class="header">
            <h1>Welcome, <?php echo htmlspecialchars(explode(' ', $user['name'])[0]); ?></h1>
            <div class="search-profile">
               
            </div>
        </div>

        <div class="profile-section">
            <div class="profile-header">
                <div class="profile-header-left">
                    <img src="<?php echo htmlspecialchars($profilePicture); ?>" alt="Profile Picture" class="profile-image">
                    <div>
                        <h2><?php echo htmlspecialchars($user['name']); ?></h2>
                        <p><?php echo htmlspecialchars($user['email']); ?></p>
                    </div>
                </div>
            </div>

            <form action="#" method="POST">
                <div class="form-group mb-3">
                    <label>Name</label>
                    <input type="text" class="form-control" value="<?php echo htmlspecialchars($user['name']); ?>" readonly>
                </div>
                <div class="form-group mb-3">
                    <label>Email</label>
                    <input type="email" class="form-control" value="<?php echo htmlspecialchars($user['email']); ?>" readonly>
                </div>
                <div class="form-group mb-3">
                    <label>Phone Number</label>
                    <input type="tel" class="form-control" value="<?php echo htmlspecialchars($user['phone_number']); ?>" readonly>
                </div>

                <!-- Logout button with modal -->
                <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#logoutModal">
                    Logout
                </button>
            </form>
        </div>
    </div>

    <!-- Book Section -->
    <!-- <div class="container mt-4">
        <h1 class="title">Buku</h1>
        <section class="private-courses">
            <div class="private-container">
                <div class="private-cards-wrapper">
                    <?php foreach ($books as $book): ?>
                    <div class="private-card" data-book-id="<?php echo $book['id']; ?>" data-ebook-file="<?php echo htmlspecialchars($book['ebook_file']); ?>">
                        <div class="private-card-image">
                        <?php
                        $imagePath = str_replace('../public', '/public', htmlspecialchars($book['image']));
                        if (file_exists($_SERVER['DOCUMENT_ROOT'] . $imagePath)) {
                            $imageUrl = $imagePath;
                        } else {
                            $imageUrl = '/public/image-book/6752af8606088_commandermewing.png';
                        }
                        ?>
                        <img src="<?php echo $imageUrl; ?>" alt="Book Thumbnail">
                        </div>
                        <div class="private-card-content">
                        <h3 class="private-course-title"><?php echo htmlspecialchars($book['title']); ?></h3>
                        <p class="private-instructor-name"><?php echo htmlspecialchars($book['description']); ?></p>
                        <div class="private-card-stats">  
                            <div class="private-students"><?php echo $book['rating']; ?> Rating</div>
                        </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>
    </div> -->

    <!-- Logout Confirmation Modal -->
    <div class="modal fade" id="logoutModal" tabindex="-1" aria-labelledby="logoutModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="logoutModalLabel">Konfirmasi Logout</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Apakah Anda yakin ingin logout?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <form action="../userprofile/logout.php" method="POST">
                        <button type="submit" class="btn btn-danger">Logout</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

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
      <?php include dirname(__FILE__) . '/../../layouts/footer.php'; ?>
</body>
</html>
